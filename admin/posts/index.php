<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get search query and filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Get categories for filter
$categoriesStmt = $pdo->prepare('SELECT * FROM categories WHERE type = ? ORDER BY name ASC');
$categoriesStmt->execute(['post']);
$categories = $categoriesStmt->fetchAll();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$query = "SELECT p.*, c.name as category_name FROM posts p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (p.title LIKE :search OR p.content LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($categoryFilter > 0) {
    $query .= " AND p.category_id = :category";
    $params[':category'] = $categoryFilter;
}

$query .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// Get total count for pagination
$countQuery = "SELECT COUNT(*) FROM posts p WHERE 1=1";
if ($search) {
    $countQuery .= " AND (p.title LIKE :search OR p.content LIKE :search)";
}
if ($categoryFilter > 0) {
    $countQuery .= " AND p.category_id = :category";
}
$countStmt = $pdo->prepare($countQuery);
if ($search) {
    $countStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
if ($categoryFilter > 0) {
    $countStmt->bindValue(':category', $categoryFilter, PDO::PARAM_INT);
}
$countStmt->execute();
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $perPage);
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/../_head.php'; ?>
  <title>Quản Lý Bài Viết</title>
</head>
<body>
  <div class="admin-container">
  <?php include __DIR__ . '/../_nav.php'; ?>
  
  <h1><i class="fas fa-newspaper"></i> Quản Lý Bài Viết</h1>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <i class="fas fa-check-circle"></i>
      <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
      <i class="fas fa-exclamation-circle"></i>
      <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
  
  <div class="toolbar">
    <div class="toolbar-left">
      <a href="form.php" class="btn">
        <i class="fas fa-plus-circle"></i>
        Thêm Bài Viết
      </a>
    </div>
    
    <div class="toolbar-right">
      <form method="get" class="search-form-compact">
        <div class="search-group">
          <select name="category" class="form-select">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" name="search" placeholder="Tìm kiếm bài viết..." value="<?= htmlspecialchars($search) ?>" class="form-input">
          </div>
          <button type="submit" class="btn btn-primary">
            Tìm
          </button>
          <?php if ($search || $categoryFilter): ?>
            <a href="index.php" class="btn btn-secondary">
              <i class="fas fa-times"></i>
            </a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <table>
    <thead>
      <tr><th>Ảnh</th><th>Tiêu Đề</th><th>Danh Mục</th><th>Slug</th><th>Trạng Thái</th><th>Ngày Tạo</th><th>Hành Động</th></tr>
    </thead>
    <tbody>
    <?php if (empty($posts)): ?>
      <tr>
        <td colspan="7">
          <div class="empty-state" style="padding: 40px 20px;">
            <svg width="64" height="64" viewBox="0 0 16 16" fill="currentColor">
              <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
            </svg>
            <h2>Không tìm thấy bài viết nào</h2>
            <p>Hãy thử tìm kiếm với từ khóa khác hoặc thêm bài viết mới</p>
          </div>
        </td>
      </tr>
    <?php else: ?>
      <?php foreach ($posts as $post): ?>
      <tr>
        <td>
          <?php if ($post['featured_image']): ?>
            <img src="../../<?= htmlspecialchars($post['featured_image']) ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:4px">
          <?php else: ?>
            <div style="width:60px;height:60px;background:#f5f5f7;border-radius:4px;display:flex;align-items:center;justify-content:center">
              <svg width="24" height="24" viewBox="0 0 16 16" fill="#86868b"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/></svg>
            </div>
          <?php endif; ?>
        </td>
        <td>
          <strong><?= htmlspecialchars($post['title']) ?></strong>
          <?php if ($post['excerpt']): ?>
            <div class="muted" style="font-size:13px;margin-top:4px;color:#86868b">
              <?= htmlspecialchars(mb_substr($post['excerpt'], 0, 100)) ?>...
            </div>
          <?php endif; ?>
        </td>
        <td><?= $post['category_name'] ? htmlspecialchars($post['category_name']) : '<span class="muted">Chưa phân loại</span>' ?></td>
        <td><code style="font-size:12px;color:#06c"><?= htmlspecialchars($post['slug']) ?></code></td>
        <td>
          <?php if ($post['is_active']): ?>
            <span class="badge badge-success">Hiển thị</span>
          <?php else: ?>
            <span class="badge">Ẩn</span>
          <?php endif; ?>
        </td>
        <td class="muted"><?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></td>
        <td>
          <a href="form.php?id=<?= $post['id'] ?>" class="btn-link">
            <i class="fas fa-edit"></i>
            Sửa
          </a>
          <a href="delete.php?id=<?= $post['id'] ?>" onclick="return confirm('Bạn có chắc muốn xóa bài viết này?')" class="btn-link danger">
            <i class="fas fa-trash"></i>
            Xóa
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
  </table>  </table>

  <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $categoryFilter ? '&category=' . $categoryFilter : '' ?>" class="btn btn-secondary">
          <i class="fas fa-chevron-left"></i>
          Trước
        </a>
      <?php endif; ?>
      
      <span class="pagination-info">Trang <?= $page ?> / <?= $totalPages ?></span>
      
      <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $categoryFilter ? '&category=' . $categoryFilter : '' ?>" class="btn btn-secondary">
          Sau
          <i class="fas fa-chevron-right"></i>
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  </div>
  <?php include __DIR__ . '/../_footer.php'; ?>
</body>
</html>
