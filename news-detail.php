<?php 
$page = 'news-detail';
include 'includes/db.php';

// Get post slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
  header('Location: news.php');
  exit;
}

// Get post details by slug
$postStmt = $pdo->prepare("
  SELECT p.*, c.name as category_name, c.slug as category_slug, c.id as category_id
  FROM posts p
  LEFT JOIN categories c ON p.category_id = c.id
  WHERE p.slug = :slug AND p.is_active = 1
");
$postStmt->execute([':slug' => $slug]);
$post = $postStmt->fetch();

if (!$post) {
  header('Location: news.php');
  exit;
}

$page_title = htmlspecialchars($post['title']) . ' - Đông Sơn Export';

// Get related posts (same category, exclude current post)
$relatedStmt = $pdo->prepare("
  SELECT p.*, c.name as category_name
  FROM posts p
  LEFT JOIN categories c ON p.category_id = c.id
  WHERE p.category_id = :category_id 
    AND p.id != :current_id 
    AND p.is_active = 1
  ORDER BY p.created_at DESC
  LIMIT 3
");
$relatedStmt->execute([
  ':category_id' => $post['category_id'],
  ':current_id' => $post['id']
]);
$related_posts = $relatedStmt->fetchAll();

// Get recent posts
$recentStmt = $pdo->prepare("
  SELECT p.*, c.name as category_name
  FROM posts p
  LEFT JOIN categories c ON p.category_id = c.id
  WHERE p.is_active = 1 AND p.id != :current_id
  ORDER BY p.created_at DESC
  LIMIT 5
");
$recentStmt->execute([':current_id' => $post['id']]);
$recent_posts = $recentStmt->fetchAll();

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/news.css">

<div class="news-detail-section section" style="margin-top:50px;">
  <div class="container">
    <div class="row">
      
      <!-- Main Content -->
      <div class="col-lg-8">
        <div class="post-detail">
          
          <!-- Post Header -->
          <div class="post-header">
            
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="post-meta">
              <span class="meta-item">
                <i class="fa fa-calendar"></i> 
                <?php echo date('d/m/Y', strtotime($post['created_at'])); ?>
              </span>
              <span class="meta-item">
                <i class="fa fa-clock"></i> 
                <?php echo date('H:i', strtotime($post['created_at'])); ?>
              </span>
              <span class="meta-item">
                <i class="fa fa-user"></i> Admin
              </span>
            </div>
          </div>

          <!-- Featured Image -->
          <?php if (!empty($post['featured_image'])): ?>
            <div class="featured-image">
              <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                   alt="<?php echo htmlspecialchars($post['title']); ?>">
            </div>
          <?php endif; ?>

          <!-- Post Excerpt -->
          <?php if (!empty($post['excerpt'])): ?>
            <div class="post-excerpt">
              <p><?php echo nl2br(htmlspecialchars($post['excerpt'])); ?></p>
            </div>
          <?php endif; ?>

          <!-- Post Content -->
          <div class="post-content">
            <?php echo $post['content']; ?>
          </div>

          <!-- Post Footer -->
          <div class="post-footer">
            <div class="social-share">
              <h5><i class="fa fa-share-alt"></i> Chia sẻ bài viết:</h5>
              <div class="share-buttons">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                   target="_blank" class="share-btn facebook">
                  <i class="fab fa-facebook-f"></i> Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                   target="_blank" class="share-btn twitter">
                  <i class="fab fa-twitter"></i> Twitter
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($post['title']); ?>" 
                   target="_blank" class="share-btn linkedin">
                  <i class="fab fa-linkedin-in"></i> LinkedIn
                </a>
                <a href="javascript:void(0);" onclick="copyLink()" class="share-btn copy">
                  <i class="fa fa-link"></i> Sao chép link
                </a>
              </div>
            </div>
          </div>

          <!-- Related Posts -->
          <?php if (count($related_posts) > 0): ?>
            <div class="related-posts">
              <h3><i class="fa fa-newspaper"></i> Bài viết liên quan</h3>
              <div class="row">
                <?php foreach ($related_posts as $related): ?>
                  <div class="col-md-4">
                    <div class="related-item">
                      <a href="news-detail.php?slug=<?php echo urlencode($related['slug']); ?>">
                        <?php if (!empty($related['featured_image'])): ?>
                          <img src="<?php echo htmlspecialchars($related['featured_image']); ?>" 
                               alt="<?php echo htmlspecialchars($related['title']); ?>">
                        <?php else: ?>
                          <img src="assets/images/default-post.jpg" alt="Default image">
                        <?php endif; ?>
                      </a>
                      <div class="related-content">
                        <span class="date">
                          <i class="fa fa-calendar"></i> 
                          <?php echo date('d/m/Y', strtotime($related['created_at'])); ?>
                        </span>
                        <h5>
                          <a href="news-detail.php?slug=<?php echo urlencode($related['slug']); ?>">
                            <?php echo htmlspecialchars($related['title']); ?>
                          </a>
                        </h5>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="sidebar">
          
          <!-- Recent Posts Widget -->
          <div class="widget recent-posts-widget">
            <h4 class="widget-title">
              <i class="fa fa-clock"></i> Tin tức mới nhất
            </h4>
            <div class="recent-posts-list">
              <?php foreach ($recent_posts as $recent): ?>
                <div class="recent-post-item">
                  <a href="news-detail.php?slug=<?php echo urlencode($recent['slug']); ?>" class="recent-post-image">
                    <?php if (!empty($recent['featured_image'])): ?>
                      <img src="<?php echo htmlspecialchars($recent['featured_image']); ?>" 
                           alt="<?php echo htmlspecialchars($recent['title']); ?>">
                    <?php else: ?>
                      <img src="assets/images/default-post.jpg" alt="Default image">
                    <?php endif; ?>
                  </a>
                  <div class="recent-post-content">
                    <h6>
                      <a href="news-detail.php?slug=<?php echo urlencode($recent['slug']); ?>">
                        <?php echo htmlspecialchars($recent['title']); ?>
                      </a>
                    </h6>
                    <span class="post-date">
                      <i class="fa fa-calendar"></i> 
                      <?php echo date('d/m/Y', strtotime($recent['created_at'])); ?>
                    </span>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Back to News Widget -->
          <div class="widget cta-widget">
            <div class="cta-content">
              <i class="fa fa-newspaper"></i>
              <h4>Khám phá thêm tin tức</h4>
              <p>Cập nhật những tin tức mới nhất từ Đông Sơn Export</p>
              <a href="news.php" class="cta-btn">
                Xem tất cả tin tức
              </a>
            </div>
          </div>

          <!-- Contact Widget -->
          <div class="widget contact-widget">
            <h4 class="widget-title">
              <i class="fa fa-phone"></i> Liên hệ với chúng tôi
            </h4>
            <div class="contact-info">
              <div class="contact-item">
                <i class="fa fa-envelope"></i>
                <div>
                  <strong>Email</strong>
                  <p>info@dongsonexport.com</p>
                </div>
              </div>
              <div class="contact-item">
                <i class="fa fa-phone"></i>
                <div>
                  <strong>Hotline</strong>
                  <p>056 821 5678</p>
                </div>
              </div>
              <div class="contact-item">
                <i class="fa fa-map-marker-alt"></i>
                <div>
                  <strong>Địa chỉ</strong>
                  <p>Kiến Thuỵ, Khánh giàng, Ngọc Châu</p>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
// Force hide preloader
$(document).ready(function() {
  setTimeout(function() {
    $('#js-preloader').addClass('loaded');
  }, 100);
});

function copyLink() {
  const url = window.location.href;
  navigator.clipboard.writeText(url).then(() => {
    alert('Đã sao chép link vào clipboard!');
  }).catch(err => {
    console.error('Lỗi khi sao chép:', err);
  });
}
</script>

<?php include 'includes/footer.php'; ?>
