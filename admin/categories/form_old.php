<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$type = $_GET['type'] ?? 'product';
if (!in_array($type, ['product', 'post'])) {
    $type = 'product';
}

$category = null;
$isEdit = false;
$children = [];

if ($id) {
    $isEdit = true;
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ? AND parent_id IS NULL LIMIT 1');
    $stmt->execute([$id]);
    $category = $stmt->fetch();
    if (!$category) {
        header('Location: index.php?type=' . $type);
        exit;
    }
    $type = $category['type'];
    
    // Get children categories
    $childStmt = $pdo->prepare('SELECT * FROM categories WHERE parent_id = ? ORDER BY sort_order ASC, name ASC');
    $childStmt->execute([$id]);
    $children = $childStmt->fetchAll();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    $postId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $isEditMode = $postId > 0;
    
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $type = $_POST['type'] ?? 'product';
    $description = trim($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Get children data
    $childrenData = [];
    if (isset($_POST['children']) && is_array($_POST['children'])) {
        foreach ($_POST['children'] as $childData) {
            if (!empty(trim($childData['name']))) {
                $childrenData[] = [
                    'id' => intval($childData['id'] ?? 0),
                    'name' => trim($childData['name']),
                    'slug' => trim($childData['slug']),
                    'is_active' => isset($childData['is_active']) ? 1 : 0,
                    'sort_order' => intval($childData['sort_order'] ?? 0)
                ];
            }
        }
    }
    
    // Validation
    if (strlen($name) < 2 || strlen($name) > 255) {
        $errors[] = 'Tên danh mục phải từ 2 đến 255 ký tự';
    }
    
    if (empty($slug)) {
        $errors[] = 'Slug không được để trống';
    }
    
    if (!in_array($type, ['product', 'post'])) {
        $errors[] = 'Loại danh mục không hợp lệ';
    }
    
    // For product type, require at least 1 child
    if ($type === 'product' && empty($childrenData)) {
        $errors[] = 'Danh mục sản phẩm phải có ít nhất 1 danh mục con';
    }
    
    // Check unique slug for parent
    if ($isEditMode) {
        $slugCheck = $pdo->prepare('SELECT id FROM categories WHERE slug = ? AND id != ? LIMIT 1');
        $slugCheck->execute([$slug, $postId]);
    } else {
        $slugCheck = $pdo->prepare('SELECT id FROM categories WHERE slug = ? LIMIT 1');
        $slugCheck->execute([$slug]);
    }
    
    if ($slugCheck->fetch()) {
        $errors[] = 'Slug này đã tồn tại, vui lòng chọn slug khác';
    }
    
    // Check unique slugs for children
    $childSlugs = array_column($childrenData, 'slug');
    if (count($childSlugs) !== count(array_unique($childSlugs))) {
        $errors[] = 'Slug của các danh mục con không được trùng nhau';
    }
    
    foreach ($childrenData as $child) {
        if (empty($child['slug'])) {
            $errors[] = 'Slug của danh mục con không được để trống';
            break;
        }
        // Check if child slug exists in database (excluding existing children)
        $childSlugCheck = $pdo->prepare('SELECT id FROM categories WHERE slug = ? AND id != ? AND parent_id != ? LIMIT 1');
        $childSlugCheck->execute([$child['slug'], $child['id'], $postId ?: 0]);
        if ($childSlugCheck->fetch()) {
            $errors[] = "Slug '{$child['slug']}' đã tồn tại";
            break;
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Auto-assign sort_order for parent
    if (!$isEditMode) {
        $maxOrderStmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) + 1 FROM categories WHERE type = ? AND parent_id IS NULL');
        $maxOrderStmt->execute([$type]);
        $sort_order = $maxOrderStmt->fetchColumn();
    } else {
        $sort_order = $category['sort_order'];
    }

    try {
        $pdo->beginTransaction();
        
        if ($isEditMode) {
            // Update parent category
            $upd = $pdo->prepare('UPDATE categories SET name = ?, slug = ?, type = ?, description = ?, is_active = ?, sort_order = ? WHERE id = ?');
            $upd->execute([$name, $slug, $type, $description, $is_active, $sort_order, $postId]);
            $parentId = $postId;
        } else {
            // Insert parent category
            $ins = $pdo->prepare('INSERT INTO categories (name, slug, type, parent_id, description, is_active, sort_order) VALUES (?, ?, ?, NULL, ?, ?, ?)');
            $ins->execute([$name, $slug, $type, $description, $is_active, $sort_order]);
            $parentId = $pdo->lastInsertId();
        }
        
        // Handle children
        if ($type === 'product') {
            // Get existing children IDs
            $existingIds = array_column($children, 'id');
            $newIds = array_filter(array_column($childrenData, 'id'));
            
            // Delete removed children
            $toDelete = array_diff($existingIds, $newIds);
            if (!empty($toDelete)) {
                $placeholders = implode(',', array_fill(0, count($toDelete), '?'));
                $delStmt = $pdo->prepare("DELETE FROM categories WHERE id IN ($placeholders) AND parent_id = ?");
                $delStmt->execute(array_merge($toDelete, [$parentId]));
            }
            
            // Insert or update children
            foreach ($childrenData as $child) {
                if ($child['id'] > 0) {
                    // Update existing child
                    $updChild = $pdo->prepare('UPDATE categories SET name = ?, slug = ?, is_active = ?, sort_order = ? WHERE id = ? AND parent_id = ?');
                    $updChild->execute([$child['name'], $child['slug'], $child['is_active'], $child['sort_order'], $child['id'], $parentId]);
                } else {
                    // Insert new child
                    $insChild = $pdo->prepare('INSERT INTO categories (parent_id, name, slug, type, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)');
                    $insChild->execute([$parentId, $child['name'], $child['slug'], $type, $child['is_active'], $child['sort_order']]);
                }
            }
        }
        
        $pdo->commit();
        header('Location: index.php?type=' . $type);
        exit;
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['form_errors'] = ['Lỗi khi lưu dữ liệu: ' . $e->getMessage()];
        $_SESSION['form_data'] = $_POST;
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

function slugify($s) {
    $vietnamese = [
        'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
        'đ' => 'd',
        'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
    ];
    $s = mb_strtolower($s, 'UTF-8');
    $s = strtr($s, $vietnamese);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    $s = trim($s, '-');
    $s = preg_replace('/-+/', '-', $s);
    if (!$s) return 'cat-' . time();
    return $s;
}

$pageTitle = $isEdit ? 'Sửa Danh Mục' : 'Thêm Danh Mục';

// Retrieve and clear form errors/data from session
$formErrors = $_SESSION['form_errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/../_head.php'; ?>
  <title><?php echo $pageTitle; ?></title>
  <style>
    .children-section {
      margin-top: 32px;
      padding: 24px;
      background: #f8f9fa;
      border-radius: 12px;
      border: 2px dashed #d0d0d0;
    }
    .children-section h3 {
      margin: 0 0 16px 0;
      color: #1d1d1f;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .child-item {
      background: white;
      padding: 16px;
      margin-bottom: 12px;
      border-radius: 8px;
      border: 1px solid #e0e0e0;
      position: relative;
    }
    .child-item:hover {
      border-color: #0071e3;
      box-shadow: 0 2px 8px rgba(0,113,227,0.1);
    }
    .child-header {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }
    .child-number {
      background: #0071e3;
      color: white;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      font-size: 13px;
      flex-shrink: 0;
    }
    .child-inputs {
      display: grid;
      grid-template-columns: 1fr 1fr auto auto;
      gap: 12px;
      align-items: start;
    }
    .btn-remove-child {
      padding: 8px 12px;
      background: #ff3b30;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 13px;
      white-space: nowrap;
    }
    .btn-remove-child:hover {
      background: #ff2d20;
    }
    .btn-add-child {
      width: 100%;
      padding: 12px;
      background: white;
      border: 2px dashed #0071e3;
      color: #0071e3;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-top: 8px;
    }
    .btn-add-child:hover {
      background: #f0f8ff;
    }
    .form-input-small {
      padding: 8px 12px;
      border: 1px solid #d2d2d7;
      border-radius: 6px;
      font-size: 14px;
    }
    .form-input-small:focus {
      outline: none;
      border-color: #0071e3;
      box-shadow: 0 0 0 3px rgba(0,113,227,0.1);
    }
    .required-note {
      background: #fff3cd;
      border: 1px solid #ffc107;
      color: #856404;
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <h1><?php echo $pageTitle; ?></h1>
    <?php include __DIR__ . '/../_nav.php'; ?>
    
    <?php if (!empty($formErrors)): ?>
    <div style="background:#fee;border:1px solid #f33;border-radius:8px;padding:16px;margin-bottom:24px">
      <strong style="color:#c00">Vui lòng sửa các lỗi sau:</strong>
      <ul style="margin:8px 0 0 20px;color:#c00">
        <?php foreach ($formErrors as $error): ?>
          <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
    
    <div style="margin-bottom:24px">
      <a href="index.php?type=<?php echo $type; ?>" class="btn">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0L6.59 1.41 12.17 7H0v2h12.17l-5.58 5.59L8 16l8-8z" transform="rotate(180 8 8)"/></svg>
        Quay lại danh sách
      </a>
    </div>

    <form method="post" style="max-width:800px" id="category-form">
      <input type="hidden" name="id" value="<?php echo $id; ?>">
      
      <div class="form-row">
        <label>Loại Danh Mục <span class="required">*</span></label>
        <select name="type" id="type-select" required <?php echo $isEdit ? 'disabled' : ''; ?>>
          <option value="product" <?php echo ($formData['type'] ?? $category['type'] ?? $type) === 'product' ? 'selected' : ''; ?>>Sản Phẩm</option>
          <option value="post" <?php echo ($formData['type'] ?? $category['type'] ?? $type) === 'post' ? 'selected' : ''; ?>>Bài Viết</option>
        </select>
        <?php if ($isEdit): ?>
          <input type="hidden" name="type" value="<?php echo $category['type']; ?>">
          <p class="muted">Không thể thay đổi loại danh mục khi đã tạo</p>
        <?php endif; ?>
      </div>

      <?php if ($type === 'product'): ?>
      <div class="form-row" id="parent-category-row">
        <label>Danh Mục Cha</label>
        <select name="parent_id">
          <option value="">-- Không có (Danh mục cấp 1) --</option>
          <?php foreach ($parentCategories as $parent): ?>
            <option value="<?php echo $parent['id']; ?>" <?php echo ($formData['parent_id'] ?? $category['parent_id'] ?? '') == $parent['id'] ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($parent['name']); ?>
            </option>
          <?php endforeach; ?>
        </select>
        <p class="muted">Để trống nếu đây là danh mục cấp 1. Chỉ danh mục sản phẩm mới có cấp cha-con.</p>
      </div>
      <?php else: ?>
        <input type="hidden" name="parent_id" value="">
      <?php endif; ?>

      <div class="form-row">
        <label>Tên Danh Mục <span class="required">*</span></label>
        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($formData['name'] ?? $category['name'] ?? ''); ?>" minlength="2" maxlength="255">
        <span class="form-error">Tên danh mục bắt buộc (2-255 ký tự)</span>
      </div>

      <div class="form-row">
        <label>Slug (URL thân thiện) <span class="required">*</span></label>
        <input type="text" name="slug" id="slug" required value="<?php echo htmlspecialchars($formData['slug'] ?? $category['slug'] ?? ''); ?>">
        <p class="muted">Tự động tạo từ tên danh mục (có thể chỉnh sửa)</p>
      </div>

      <div class="form-row">
        <label>Mô Tả</label>
        <textarea name="description" rows="4"><?php echo htmlspecialchars($formData['description'] ?? $category['description'] ?? ''); ?></textarea>
      </div>

      <div class="form-row">
        <label class="checkbox-label">
          <input type="checkbox" name="is_active" value="1" <?php echo ($formData['is_active'] ?? $category['is_active'] ?? 1) ? 'checked' : ''; ?>>
          <span>Kích hoạt danh mục</span>
        </label>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>
          <?php echo $isEdit ? 'Cập Nhật' : 'Lưu Danh Mục'; ?>
        </button>
        <a href="index.php?type=<?php echo $type; ?>" class="btn">Hủy</a>
      </div>
    </form>
  </div>

  <script>
    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
      var slugInput = document.getElementById('slug');
      if (!slugInput.value || slugInput.dataset.auto !== 'false') {
        slugInput.value = slugify(this.value);
        slugInput.dataset.auto = 'true';
      }
    });

    document.getElementById('slug').addEventListener('input', function() {
      this.dataset.auto = 'false';
    });

    function slugify(text) {
      text = text.toLowerCase();
      var vn = {'à':'a','á':'a','ạ':'a','ả':'a','ã':'a','ă':'a','ằ':'a','ắ':'a','ặ':'a','ẳ':'a','ẵ':'a','â':'a','ầ':'a','ấ':'a','ậ':'a','ẩ':'a','ẫ':'a','è':'e','é':'e','ẹ':'e','ẻ':'e','ẽ':'e','ê':'e','ề':'e','ế':'e','ệ':'e','ể':'e','ễ':'e','ì':'i','í':'i','ị':'i','ỉ':'i','ĩ':'i','ò':'o','ó':'o','ọ':'o','ỏ':'o','õ':'o','ô':'o','ồ':'o','ố':'o','ộ':'o','ổ':'o','ỗ':'o','ơ':'o','ờ':'o','ớ':'o','ợ':'o','ở':'o','ỡ':'o','ù':'u','ú':'u','ụ':'u','ủ':'u','ũ':'u','ư':'u','ừ':'u','ứ':'u','ự':'u','ử':'u','ữ':'u','ỳ':'y','ý':'y','ỵ':'y','ỷ':'y','ỹ':'y','đ':'d'};
      for (var k in vn) text = text.replace(new RegExp(k, 'g'), vn[k]);
      return text.replace(/[^a-z0-9\s-]/g, '').replace(/[\s-]+/g, '-').trim().replace(/^-+|-+$/g, '');
    }

    // Form validation
    document.getElementById('category-form').addEventListener('submit', function(e) {
      var name = document.getElementById('name').value.trim();
      
      document.querySelectorAll('.has-error').forEach(function(el) {
        el.classList.remove('has-error');
      });

      var hasError = false;

      if (name.length < 2) {
        document.getElementById('name').closest('.form-row').classList.add('has-error');
        hasError = true;
      }

      if (hasError) {
        e.preventDefault();
        return false;
      }
    });
  </script>
  <?php include __DIR__ . '/../_footer.php'; ?>
</body>
</html>
