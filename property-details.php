<?php 
$page = 'property-details';
$page_title = 'Đông Sơn Export - Chi tiết sản phẩm';
include 'includes/header.php'; 
include 'includes/db.php';

// Get product slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
  header('Location: properties.php');
  exit;
}

// Get product details
$productStmt = $pdo->prepare('
  SELECT p.*, c.name as category_name, c.id as category_id, c.slug as category_slug
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.id
  WHERE p.slug = ?
  LIMIT 1
');
$productStmt->execute([$slug]);
$product = $productStmt->fetch();

if (!$product) {
  header('Location: properties.php');
  exit;
}

// Get all product images
$imagesStmt = $pdo->prepare('
  SELECT image_path, sort_order
  FROM product_images
  WHERE product_id = ?
  ORDER BY sort_order ASC
');
$imagesStmt->execute([$product['id']]);
$productImages = $imagesStmt->fetchAll();

// Get related products (same category, exclude current product)
$relatedStmt = $pdo->prepare('
  SELECT p.*, pi.image_path, c.name as category_name
  FROM products p
  LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.sort_order = 0
  LEFT JOIN categories c ON p.category_id = c.id
  WHERE p.category_id = ? AND p.id != ?
  ORDER BY RAND()
  LIMIT 4
');
$relatedStmt->execute([$product['category_id'], $product['id']]);
$relatedProducts = $relatedStmt->fetchAll();

// Get other products (different categories)
$otherStmt = $pdo->prepare('
  SELECT p.*, pi.image_path, c.name as category_name
  FROM products p
  LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.sort_order = 0
  LEFT JOIN categories c ON p.category_id = c.id
  WHERE p.category_id != ? AND p.id != ?
  ORDER BY RAND()
  LIMIT 4
');
$otherStmt->execute([$product['category_id'], $product['id']]);
$otherProducts = $otherStmt->fetchAll();

$page_title = htmlspecialchars($product['title']) . ' - Đông Sơn Export';
?>

<!-- Import product details CSS instead of inline styles -->
<link rel="stylesheet" href="assets/css/product-details.css">

  <div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <span class="breadcrumb">
            <a href="index.php">Trang Chủ</a> / 
            <a href="properties.php">Sản Phẩm</a> 
            <?php if (!empty($product['category_name'])): ?>
              / <a href="properties.php?category=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
            <?php endif; ?>
            / <?php echo htmlspecialchars($product['title']); ?>
          </span>
          <h3><?php echo htmlspecialchars($product['title']); ?></h3>
        </div>
      </div>
    </div>
  </div>

  <div class="single-property section">
    <div class="container">
      <!-- Product Detail Layout: Image Left, Content Right -->
      <div class="product-detail-wrapper">
        <!-- Left: Product Gallery -->
        <div class="product-gallery-section">
          <div class="product-gallery">
            <?php if (count($productImages) > 0): ?>
              <img id="mainImage" class="main-product-image" 
                   src="uploads/products/<?php echo htmlspecialchars($productImages[0]['image_path']); ?>" 
                   alt="<?php echo htmlspecialchars($product['title']); ?>">
              
              <?php if (count($productImages) > 1): ?>
              <div class="thumbnail-gallery">
                <?php foreach ($productImages as $index => $img): ?>
                  <div class="thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>" 
                       onclick="changeImage(this, 'uploads/products/<?php echo htmlspecialchars($img['image_path']); ?>')">
                    <img src="uploads/products/<?php echo htmlspecialchars($img['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?> - Ảnh <?php echo $index + 1; ?>">
                  </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            <?php else: ?>
              <img id="mainImage" class="main-product-image" 
                   src="assets/images/no-image.png" 
                   alt="No image">
            <?php endif; ?>
          </div>
        </div>

        <!-- Right: Product Content -->
        <div class="product-info-section">
          <div class="product-content">
            <?php if (!empty($product['category_name'])): ?>
              <span class="category"><?php echo htmlspecialchars($product['category_name']); ?></span>
            <?php endif; ?>
            <br>
            <h4><?php echo htmlspecialchars($product['title']); ?></h4>
            
            <!-- <?php if (!empty($product['short_description'])): ?>
            <div class="product-summary">
              <p><?php echo nl2br(htmlspecialchars($product['short_description'])); ?></p>
            </div>
            <?php endif; ?> -->

            <?php if (!empty($product['price']) && $product['price'] > 0): ?>
            <div class="product-price">
              <?php if (!empty($product['promo_price']) && $product['promo_price'] > 0): ?>
                <span class="old-price"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                <span class="current-price"><?php echo number_format($product['promo_price'], 0, ',', '.'); ?> VNĐ</span>
                <span class="discount-badge">
                  -<?php echo round((($product['price'] - $product['promo_price']) / $product['price']) * 100); ?>%
                </span>
              <?php else: ?>
                <span class="current-price"><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
              <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="product-description">
              <?php echo $product['description']; ?>
            </div>

            <!-- Contact Order Button -->
            <div class="contact-order-section">
              <a href="contact.php" class="btn-contact-order">
                <i class="fa fa-phone"></i>Liên Hệ Đặt Hàng Ngay
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products Section -->
      <?php if (count($relatedProducts) > 0): ?>
      <div class="related-products">
        <div class="row">
          <div class="col-lg-12">
            <div class="section-heading">
              <h6>| Sản Phẩm Liên Quan</h6>
              <h2>Các sản phẩm khác trong danh mục <?php echo htmlspecialchars($product['category_name']); ?></h2>
            </div>
          </div>
        </div>

        <div class="row">
          <?php foreach ($relatedProducts as $relProd): ?>
          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <a href="property-details.php?slug=<?php echo urlencode($relProd['slug']); ?>">
                <?php if (!empty($relProd['image_path'])): ?>
                  <img class="product-card-image" 
                       src="uploads/products/<?php echo htmlspecialchars($relProd['image_path']); ?>" 
                       alt="<?php echo htmlspecialchars($relProd['title']); ?>">
                <?php else: ?>
                  <img class="product-card-image" src="assets/images/no-image.png" alt="No image">
                <?php endif; ?>
              </a>
              <div class="product-card-body">
                <div class="product-card-category"><?php echo htmlspecialchars($relProd['category_name'] ?? 'Sản phẩm'); ?></div>
                <h5 class="product-card-title">
                  <a href="property-details.php?slug=<?php echo urlencode($relProd['slug']); ?>">
                    <?php echo htmlspecialchars($relProd['title']); ?>
                  </a>
                </h5>
                <a href="property-details.php?slug=<?php echo urlencode($relProd['slug']); ?>" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <!-- Other Products Section -->
      <?php if (count($otherProducts) > 0): ?>
      <div class="related-products" style="margin-top: 40px;">
        <div class="row">
          <div class="col-lg-12">
            <div class="section-heading">
              <h6>| Sản Phẩm Khác</h6>
              <h2>Các sản phẩm khác bạn có thể quan tâm</h2>
            </div>
          </div>
        </div>

        <div class="row">
          <?php foreach ($otherProducts as $otherProd): ?>
          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <a href="property-details.php?slug=<?php echo urlencode($otherProd['slug']); ?>">
                <?php if (!empty($otherProd['image_path'])): ?>
                  <img class="product-card-image" 
                       src="uploads/products/<?php echo htmlspecialchars($otherProd['image_path']); ?>" 
                       alt="<?php echo htmlspecialchars($otherProd['title']); ?>">
                <?php else: ?>
                  <img class="product-card-image" src="assets/images/no-image.png" alt="No image">
                <?php endif; ?>
              </a>
              <div class="product-card-body">
                <div class="product-card-category"><?php echo htmlspecialchars($otherProd['category_name'] ?? 'Sản phẩm'); ?></div>
                <h5 class="product-card-title">
                  <a href="property-details.php?slug=<?php echo urlencode($otherProd['slug']); ?>">
                    <?php echo htmlspecialchars($otherProd['title']); ?>
                  </a>
                </h5>
                <a href="property-details.php?slug=<?php echo urlencode($otherProd['slug']); ?>" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </div>

<script>
  let currentImageIndex = 0;
  let imagesList = [];
  
  // Initialize gallery
  document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.thumbnail-item img');
    imagesList = Array.from(thumbnails).map(img => img.src);
    
    <?php if (count($productImages) > 1): ?>
    // Auto slide images every 5 seconds
    setInterval(function() {
      nextImage();
    }, 5000);
    <?php endif; ?>
  });
  
  function changeImage(element, imageSrc) {
    // Update main image with fade effect
    const mainImg = document.getElementById('mainImage');
    mainImg.style.opacity = '0';
    
    setTimeout(() => {
      mainImg.src = imageSrc;
      mainImg.style.opacity = '1';
    }, 300);
    
    // Remove active class from all thumbnails
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    thumbnails.forEach(thumb => thumb.classList.remove('active'));
    
    // Add active class to clicked thumbnail
    element.classList.add('active');
    
    // Update current index
    currentImageIndex = Array.from(thumbnails).indexOf(element);
  }
  
  function nextImage() {
    if (imagesList.length <= 1) return;
    
    currentImageIndex = (currentImageIndex + 1) % imagesList.length;
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    
    if (thumbnails[currentImageIndex]) {
      changeImage(thumbnails[currentImageIndex], imagesList[currentImageIndex]);
    }
  }
  
  function prevImage() {
    if (imagesList.length <= 1) return;
    
    currentImageIndex = (currentImageIndex - 1 + imagesList.length) % imagesList.length;
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    
    if (thumbnails[currentImageIndex]) {
      changeImage(thumbnails[currentImageIndex], imagesList[currentImageIndex]);
    }
  }
  
  // Add keyboard navigation
  document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') {
      prevImage();
    } else if (e.key === 'ArrowRight') {
      nextImage();
    }
  });
</script>

<?php include 'includes/footer.php'; ?>