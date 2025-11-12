<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$isEditMode = isset($_GET['id']);
$post = null;

if ($isEditMode) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        $_SESSION['error'] = 'Không tìm thấy bài viết';
        header('Location: index.php');
        exit;
    }
}

// Get post categories
$categoriesStmt = $pdo->prepare('SELECT * FROM categories WHERE type = ? AND is_active = 1 ORDER BY sort_order ASC, name ASC');
$categoriesStmt->execute(['post']);
$categories = $categoriesStmt->fetchAll();

function slugify($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $vietnamese = [
        'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
        'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
        'đ' => 'd'
    ];
    $text = strtr($text, $vietnamese);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/../_head.php'; ?>
  <title><?= $isEditMode ? 'Sửa' : 'Thêm' ?> Bài Viết - Admin</title>
  <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
</head>
<body>
  <div class="admin-container">
  <h1><?= $isEditMode ? 'Sửa' : 'Thêm' ?> Bài Viết</h1>
  <?php include __DIR__ . '/../_nav.php'; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <p style="color:#ff3b30;background:#fee;padding:12px;border-radius:8px;border:1px solid #fcc"><?= $_SESSION['error'] ?></p>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <form method="POST" action="save.php" enctype="multipart/form-data" style="max-width:100%">
    <?php if ($isEditMode): ?>
      <input type="hidden" name="id" value="<?= $post['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
      <label>Tiêu đề <span style="color:#ff3b30">*</span></label>
      <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title'] ?? '') ?>" required placeholder="Nhập tiêu đề bài viết">
    </div>

    <div class="form-row">
      <label>Đường dẫn (slug)</label>
      <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($post['slug'] ?? '') ?>" placeholder="tu-dong-tao-tu-tieu-de">
      <p class="muted">Để trống để tự động tạo từ tiêu đề</p>
    </div>

    <div class="form-row">
      <label>Danh mục <span style="color:#ff3b30">*</span></label>
      <div style="display:flex;gap:8px;align-items:flex-start">
        <select name="category_id" id="category_id" required style="flex:1">
          <option value="">-- Chọn danh mục --</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button type="button" class="btn" onclick="openQuickAddCategory()" title="Thêm danh mục mới" style="white-space:nowrap">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/></svg>
        </button>
      </div>
      <p class="muted">Chọn danh mục bài viết</p>
    </div>

    <div class="form-row">
      <label>Mô tả ngắn</label>
      <textarea id="excerpt" name="excerpt" rows="3" placeholder="Mô tả ngắn gọn (hiển thị trong danh sách)"><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
    </div>

    <div class="form-row">
      <label>Ảnh đại diện</label>
      <?php if ($isEditMode && $post['featured_image']): ?>
        <div style="margin-bottom:10px">
          <img src="../../<?= htmlspecialchars($post['featured_image']) ?>" alt="Featured" style="max-width:300px;border-radius:8px;border:1px solid #d2d2d7">
          <p class="muted">Chọn ảnh mới để thay thế</p>
        </div>
      <?php endif; ?>
      <input type="file" id="featured_image" name="featured_image" accept="image/*">
      <p class="muted">Ảnh đại diện hiển thị trong danh sách. Khuyến nghị: 800x600px</p>
    </div>

    <div class="form-row">
      <label>Nội dung <span style="color:#ff3b30">*</span></label>
      <textarea id="content" name="content" required><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
      <p class="muted">Dùng nút "Image" trong thanh công cụ để thêm ảnh vào nội dung</p>
    </div>

    <div class="form-row">
      <label>
        <input type="checkbox" name="is_active" value="1" <?= ($post['is_active'] ?? 1) ? 'checked' : '' ?>>
        Hiển thị bài viết
      </label>
    </div>

    <div class="form-row">
      <button type="submit" class="btn"><?= $isEditMode ? 'Cập nhật' : 'Thêm bài viết' ?></button>
      <a href="index.php" class="btn">Hủy</a>
    </div>
  </form>

  </div>
  <?php include __DIR__ . '/../_footer.php'; ?>

  <script>
    CKEDITOR.replace('content', {
      height: 400,
      filebrowserUploadUrl: '../upload.php',
      filebrowserUploadMethod: 'form'
    });

    document.getElementById('title').addEventListener('input', function() {
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

    // Quick add category
    function openQuickAddCategory() {
      var modal = document.getElementById('quick-add-category-modal');
      if (!modal) {
        modal = document.createElement('div');
        modal.id = 'quick-add-category-modal';
        modal.innerHTML = `
          <div class="modal-overlay" onclick="closeQuickAddCategory()"></div>
          <div class="modal-content">
            <h3>Thêm Danh Mục Mới</h3>
            <form id="quick-category-form" onsubmit="saveQuickCategory(event)">
              <div class="form-row">
                <label>Tên danh mục <span style="color:#ff3b30">*</span></label>
                <input type="text" id="quick-cat-name" required>
              </div>
              <div class="form-row">
                <label>Slug</label>
                <input type="text" id="quick-cat-slug" placeholder="Tự động tạo từ tên">
              </div>
              <div class="form-row">
                <button type="submit" class="btn">Lưu</button>
                <button type="button" class="btn" onclick="closeQuickAddCategory()">Hủy</button>
              </div>
            </form>
          </div>
        `;
        document.body.appendChild(modal);
        
        document.getElementById('quick-cat-name').addEventListener('input', function() {
          var slugInput = document.getElementById('quick-cat-slug');
          if (!slugInput.value || slugInput.dataset.auto !== 'false') {
            slugInput.value = slugify(this.value);
            slugInput.dataset.auto = 'true';
          }
        });
        
        document.getElementById('quick-cat-slug').addEventListener('input', function() {
          this.dataset.auto = 'false';
        });
      }
      modal.style.display = 'block';
      document.getElementById('quick-cat-name').focus();
    }

    function closeQuickAddCategory() {
      var modal = document.getElementById('quick-add-category-modal');
      if (modal) {
        modal.style.display = 'none';
        document.getElementById('quick-category-form').reset();
      }
    }

    function saveQuickCategory(e) {
      e.preventDefault();
      
      var name = document.getElementById('quick-cat-name').value.trim();
      var slug = document.getElementById('quick-cat-slug').value.trim() || slugify(name);
      
      if (!name) {
        alert('Vui lòng nhập tên danh mục');
        return;
      }
      
      var formData = new FormData();
      formData.append('name', name);
      formData.append('slug', slug);
      formData.append('type', 'post');
      formData.append('is_active', '1');
      formData.append('sort_order', '0');
      
      fetch('../categories/quick_add.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          var select = document.getElementById('category_id');
          var option = document.createElement('option');
          option.value = data.category.id;
          option.text = data.category.name;
          option.selected = true;
          select.appendChild(option);
          
          closeQuickAddCategory();
          alert('Đã thêm danh mục mới!');
        } else {
          alert('Lỗi: ' + (data.message || 'Không thể thêm danh mục'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thêm danh mục');
      });
    }
  </script>
  
  <style>
    #quick-add-category-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 10000;
    }
    .modal-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0,0,0,0.5);
    }
    .modal-content {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: #fff;
      padding: 24px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
      min-width: 400px;
      max-width: 90%;
    }
    .modal-content h3 {
      margin: 0 0 20px 0;
      color: #1d1d1f;
    }
  </style>
</body>
</html>
