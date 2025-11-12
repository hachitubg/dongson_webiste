<?php 
$page = 'news';
$page_title = 'Tin Tức - Đông Sơn Export';
include 'includes/header.php'; 
include 'includes/db.php';

// Pagination settings
$posts_per_page = 9;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $posts_per_page;

// Category filter
$category_id = isset($_GET['category']) ? intval($_GET['category']) : null;

// Get post categories
$categoryStmt = $pdo->prepare("
  SELECT id, name, slug 
  FROM categories 
  WHERE type = 'post' AND is_active = 1 
  ORDER BY sort_order, name
");
$categoryStmt->execute();
$categories = $categoryStmt->fetchAll();

// Build query for posts
$where_clause = "WHERE p.is_active = 1";
$params = [];

if ($category_id) {
  $where_clause .= " AND p.category_id = :category_id";
  $params[':category_id'] = $category_id;
}

// Get total posts count
$countStmt = $pdo->prepare("
  SELECT COUNT(*) as total
  FROM posts p
  $where_clause
");
$countStmt->execute($params);
$total_posts = $countStmt->fetch()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts for current page
$postStmt = $pdo->prepare("
  SELECT p.*, c.name as category_name, c.slug as category_slug
  FROM posts p
  LEFT JOIN categories c ON p.category_id = c.id
  $where_clause
  ORDER BY p.created_at DESC
  LIMIT $posts_per_page OFFSET $offset
");
$postStmt->execute($params);
$posts = $postStmt->fetchAll();

// Get selected category name
$selected_category_name = 'Tất cả tin tức';
if ($category_id) {
  foreach ($categories as $cat) {
    if ($cat['id'] == $category_id) {
      $selected_category_name = $cat['name'];
      break;
    }
  }
}
?>

<link rel="stylesheet" href="assets/css/news.css">

<div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <span class="breadcrumb"><a href="index.php">Trang Chủ</a> / Sản Phẩm</span>
          <h3>Sản Phẩm Xuất Khẩu</h3>
        </div>
      </div>
    </div>
  </div>

<div class="news-section section" style="margin-top:50px;">
  <div class="container">
    
    <!-- Category Filter -->
    <div class="row">
      <div class="col-lg-12">
        <div class="category-filter">
          <h4><i class="fa fa-filter"></i> Lọc theo danh mục:</h4>
          <div class="filter-buttons">
            <a href="news.php" class="filter-btn <?php echo !$category_id ? 'active' : ''; ?>">
              <i class="fa fa-th"></i> Tất cả
            </a>
            <?php foreach ($categories as $cat): ?>
              <a href="news.php?category=<?php echo $cat['id']; ?>" 
                 class="filter-btn <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                <i class="fa fa-folder"></i> <?php echo htmlspecialchars($cat['name']); ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Posts Grid -->
    <div class="row">
      <div class="col-lg-12">
        <h5 class="section-subtitle">
          <?php echo htmlspecialchars($selected_category_name); ?> 
          <span class="post-count">(<?php echo $total_posts; ?> bài viết)</span>
        </h5>
      </div>
    </div>

    <div class="row">
      <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
          <div class="col-lg-4 col-md-6" style="margin-bottom: 20px;">
            <div class="news-item">
              <div class="image-wrapper">
                <a href="news-detail.php?slug=<?php echo urlencode($post['slug']); ?>">
                  <?php if (!empty($post['featured_image'])): ?>
                    <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>">
                  <?php else: ?>
                    <img src="assets/images/default-post.jpg" alt="Default image">
                  <?php endif; ?>
                </a>
                <?php if (!empty($post['category_name'])): ?>
                  <span class="category-badge">
                    <?php echo htmlspecialchars($post['category_name']); ?>
                  </span>
                <?php endif; ?>
              </div>
              
              <div class="content">
                <div class="post-meta">
                  <span class="date">
                    <i class="fa fa-calendar"></i> 
                    <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
                  </span>
                  <span class="author">
                    <i class="fa fa-user"></i> Admin
                  </span>
                </div>
                
                <h4>
                  <a href="news-detail.php?slug=<?php echo urlencode($post['slug']); ?>">
                    <?php echo htmlspecialchars($post['title']); ?>
                  </a>
                </h4>
                
                <p class="excerpt">
                  <?php 
                    $excerpt = $post['excerpt'] ?? strip_tags($post['content'] ?? '');
                    echo htmlspecialchars(mb_substr($excerpt, 0, 150, 'UTF-8')) . '...'; 
                  ?>
                </p>
                
                <a href="news-detail.php?slug=<?php echo urlencode($post['slug']); ?>" class="read-more">
                  Đọc thêm <i class="fa fa-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12">
          <div class="no-posts">
            <i class="fa fa-inbox"></i>
            <h3>Không có tin tức nào</h3>
            <p>Hiện tại chưa có tin tức trong danh mục này. Vui lòng quay lại sau.</p>
            <a href="news.php" class="btn-back">
              Quay lại tất cả tin tức
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="pagination">
            <?php if ($current_page > 1): ?>
              <a href="?page=<?php echo $current_page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>" 
                 class="page-link">
                <i class="fa fa-chevron-left"></i>
              </a>
            <?php endif; ?>

            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            if ($start_page > 1): ?>
              <a href="?page=1<?php echo $category_id ? '&category=' . $category_id : ''; ?>" 
                 class="page-link">1</a>
              <?php if ($start_page > 2): ?>
                <span class="page-dots">...</span>
              <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
              <a href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>" 
                 class="page-link <?php echo $i == $current_page ? 'active' : ''; ?>">
                <?php echo $i; ?>
              </a>
            <?php endfor; ?>

            <?php if ($end_page < $total_pages): ?>
              <?php if ($end_page < $total_pages - 1): ?>
                <span class="page-dots">...</span>
              <?php endif; ?>
              <a href="?page=<?php echo $total_pages; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>" 
                 class="page-link"><?php echo $total_pages; ?></a>
            <?php endif; ?>

            <?php if ($current_page < $total_pages): ?>
              <a href="?page=<?php echo $current_page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>" 
                 class="page-link">
                <i class="fa fa-chevron-right"></i>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endif; ?>

  </div>
</div>

<script>
// Force hide preloader on news page
$(document).ready(function() {
  setTimeout(function() {
    $('#js-preloader').addClass('loaded');
  }, 100);
});
</script>

<?php include 'includes/footer.php'; ?>
