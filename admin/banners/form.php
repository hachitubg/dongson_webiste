<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$locationCode = isset($_GET['location']) ? trim($_GET['location']) : '';
$banner = null;
$isEdit = false;

if ($id) {
    $isEdit = true;
    $stmt = $pdo->prepare('SELECT * FROM banners WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $banner = $stmt->fetch();
    if (!$banner) {
        header('Location: index.php');
        exit;
    }
    // Get location from banner if editing
    $locationCode = $banner['location_code'];
}

// Validate location code
$allLocations = getAllBannerLocations();
if (empty($locationCode) || !isset($allLocations[$locationCode])) {
    $_SESSION['error'] = 'Vị trí không hợp lệ';
    header('Location: index.php');
    exit;
}

$locationName = $allLocations[$locationCode];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    $postId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $isEditMode = $postId > 0;
    
    $location_code = trim($_POST['location_code'] ?? '');
    $link_url = trim($_POST['link_url'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Auto-generate title from location name
    $title = getBannerLocationName($location_code) . ' - Banner #' . time();
    
    // Validation
    if (empty($location_code)) {
        $errors[] = 'Vị trí không hợp lệ';
    }
    
    // Validate image for new banner
    if (!$isEditMode && empty($_FILES['image']['name'])) {
        $errors[] = 'Vui lòng chọn hình ảnh banner';
    }
    
    // Validate image upload if provided
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowed)) {
            $errors[] = 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)';
        }
    } elseif (!$isEditMode) {
        $errors[] = 'Vui lòng chọn hình ảnh banner';
    }
    
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
    
    // Auto-assign sort_order for new banner (get max + 1)
    if (!$isEditMode) {
        $maxStmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) + 1 as next_order FROM banners WHERE location_code = ?');
        $maxStmt->execute([$location_code]);
        $sort_order = $maxStmt->fetchColumn();
    }
    
    // Handle image upload if provided
    $imagePath = $isEditMode ? $banner['image_path'] : '';
    
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = rtrim(UPLOAD_DIR, '\\\/') . DIRECTORY_SEPARATOR . 'banners';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $destination = $uploadDir . DIRECTORY_SEPARATOR . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
            // Delete old image if updating
            if ($isEditMode && !empty($banner['image_path'])) {
                $oldFile = __DIR__ . '/../../' . $banner['image_path'];
                if (is_file($oldFile)) {
                    @unlink($oldFile);
                }
            }
            
            $imagePath = 'uploads/banners/' . $filename;
        } else {
            $_SESSION['error'] = 'Lỗi khi tải lên hình ảnh';
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
    
    try {
        if ($isEditMode) {
            // Keep existing sort_order when editing
            $sort_order = $banner['sort_order'];
            $stmt = $pdo->prepare('UPDATE banners SET title = ?, location_code = ?, image_path = ?, link_url = ?, is_active = ? WHERE id = ?');
            $stmt->execute([$title, $location_code, $imagePath, $link_url, $is_active, $postId]);
            $_SESSION['success'] = 'Cập nhật banner thành công';
        } else {
            $stmt = $pdo->prepare('INSERT INTO banners (title, location_code, image_path, link_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$title, $location_code, $imagePath, $link_url, $sort_order, $is_active]);
            $_SESSION['success'] = 'Thêm banner thành công';
        }
        
        header('Location: location.php?code=' . urlencode($location_code));
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Lỗi cơ sở dữ liệu: ' . $e->getMessage();
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

$pageTitle = $isEdit ? 'Sửa Banner' : 'Thêm Banner';
$pageTitle .= ' - ' . $locationName;

// Retrieve and clear form errors/data from session
$formErrors = $_SESSION['form_errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/../_head.php'; ?>
  <title><?= $pageTitle ?> - Admin</title>
</head>
<body>
  <div class="admin-container">
  <h1><?= $pageTitle ?></h1>
  <?php include __DIR__ . '/../_nav.php'; ?>
  
  <?php if (!empty($formErrors)): ?>
    <p style="color:#ff3b30;background:#fee;padding:12px;border-radius:8px;border:1px solid #fcc">
      <strong>Vui lòng sửa các lỗi sau:</strong><br>
      <?php foreach ($formErrors as $error): ?>
        • <?= htmlspecialchars($error) ?><br>
      <?php endforeach; ?>
    </p>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <p style="color:#ff3b30;background:#fee;padding:12px;border-radius:8px;border:1px solid #fcc">
      <?= htmlspecialchars($_SESSION['error']) ?>
    </p>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <div style="margin-bottom:24px">
    <a href="location.php?code=<?= urlencode($locationCode) ?>" class="btn">← Quay lại <?= htmlspecialchars($locationName) ?></a>
  </div>

  <form method="post" enctype="multipart/form-data" style="max-width:100%">
    <input type="hidden" name="id" value="<?= $id ?>">
    <input type="hidden" name="location_code" value="<?= htmlspecialchars($locationCode) ?>">
    
    <div style="background:#f5f5f7;padding:16px;border-radius:8px;margin-bottom:24px">
      <p style="margin:0;color:#1d1d1f">
        <strong>Vị trí:</strong> <?= htmlspecialchars($locationName) ?>
      </p>
    </div>

    <div class="form-row">
      <label>Hình Ảnh <?= !$isEdit ? '<span style="color:#ff3b30">*</span>' : '' ?></label>
      <input type="file" name="image" accept="image/*" <?= !$isEdit ? 'required' : '' ?>>
      <p class="muted">Chấp nhận: JPEG, PNG, GIF, WebP. Kích thước khuyến nghị: 1920x600px</p>
      
      <?php if ($isEdit && $banner['image_path']): ?>
        <div style="margin-top:12px">
          <p style="font-size:13px;color:#86868b;margin-bottom:8px">Ảnh hiện tại:</p>
          <img src="../../<?= htmlspecialchars($banner['image_path']) ?>" alt="" 
               style="max-width:100%;height:auto;border-radius:8px;border:1px solid #d2d2d7">
        </div>
      <?php endif; ?>
    </div>

    <div class="form-row">
      <label>Link URL</label>
      <div class="input-with-icon">
        <i class="fas fa-link input-icon"></i>
        <input type="url" name="link_url" class="form-input-icon" placeholder="https://example.com" 
               value="<?= htmlspecialchars($formData['link_url'] ?? $banner['link_url'] ?? '') ?>">
      </div>
      <p class="muted">URL sẽ mở khi click vào banner (để trống nếu không cần)</p>
    </div>

    <div class="form-row">
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
        <input type="checkbox" name="is_active" value="1" 
               <?= ($formData['is_active'] ?? $banner['is_active'] ?? 1) ? 'checked' : '' ?>
               style="width:18px;height:18px;cursor:pointer">
        <span>Hiển thị banner này</span>
      </label>
      <p class="muted">
        <?= !$isEdit ? 'Thứ tự banner sẽ tự động được đặt ở cuối cùng. Bạn có thể sắp xếp lại trong danh sách.' : 'Sử dụng nút Lên/Xuống trong danh sách để thay đổi thứ tự hiển thị.' ?>
      </p>
    </div>

    <div style="display:flex;gap:12px;margin-top:24px">
      <button type="submit" class="btn">
        <?= $isEdit ? 'Cập Nhật' : 'Thêm Banner' ?>
      </button>
      <a href="location.php?code=<?= urlencode($locationCode) ?>" class="btn">Hủy</a>
    </div>
  </form>

  </div>
  <?php include __DIR__ . '/../_footer.php'; ?>
</body>
</html>
