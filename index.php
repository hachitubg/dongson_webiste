<?php 
$page = 'home';
$page_title = 'Đông Sơn Export - Xuất khẩu thuốc thú y & nông sản sạch';
include 'includes/header.php'; 
include 'includes/db.php';

// Get banners for homepage
$bannerStmt = $pdo->prepare('
  SELECT * FROM banners 
  WHERE location_code = :location AND is_active = 1
  ORDER BY sort_order ASC, created_at DESC
');
$bannerStmt->execute(['location' => 'trang_chu']);
$banners = $bannerStmt->fetchAll();

// Get 6 latest products
$productStmt = $pdo->prepare('
  SELECT p.*, pi.image_path, c.name as category_name
  FROM products p
  LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.sort_order = 0
  LEFT JOIN categories c ON p.category_id = c.id
  ORDER BY p.created_at DESC
  LIMIT 6
');
$productStmt->execute();
$products = $productStmt->fetchAll();

// Get 6 latest posts/news
$postStmt = $pdo->prepare('
  SELECT p.*, c.name as category_name
  FROM posts p
  LEFT JOIN categories c ON p.category_id = c.id
  WHERE p.is_active = 1
  ORDER BY p.created_at DESC
  LIMIT 6
');
$postStmt->execute();
$posts = $postStmt->fetchAll();
?>

  <link rel="stylesheet" href="assets/css/index.css">

  <div class="main-banner">
    <div class="owl-carousel owl-banner">
      <?php if (!empty($banners)): ?>
        <?php foreach ($banners as $banner): ?>
          <img src="./<?php echo htmlspecialchars($banner['image_path']); ?>" alt="banner">
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Default banners if no banners in database -->
        <div class="item item-1"></div>
        <div class="item item-2"></div>
        <div class="item item-3"></div>
      <?php endif; ?>
    </div>
  </div>

  <div class="featured section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="left-image">
            <img src="images/banners/banner04.jpg" alt="">
            <a href="property-details.php"><img src="assets/images/featured-icon.png" alt="" style="max-width: 60px; padding: 0px;"></a>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="section-heading">
            <h6>| Về Đông Sơn</h6>
            <h2>Đông Sơn Export</h2>
          </div>
          <div class="accordion" id="accordionExample">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  Giới thiệu về công ty
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  <p><strong>Đông Sơn Export JSC</strong> là doanh nghiệp Việt Nam hoạt động trong lĩnh vực xuất khẩu thuốc thú y và nông sản, tiên phong trong việc mang các sản phẩm xanh — sạch — an toàn của Việt Nam đến với các thị trường quốc tế như Mỹ, Nhật Bản, Hàn Quốc, Trung Quốc và nhiều nước khác. Chúng tôi cam kết chất lượng, truy xuất nguồn gốc và tính bền vững trong từng sản phẩm.</p>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                  Hoạt động như thế nào?
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  <p>Đông Sơn Export vận hành trên ba trụ cột cốt lõi, cam kết mang đến sản phẩm an toàn, chất lượng và nguồn gốc minh bạch phù hợp tiêu chuẩn xuất khẩu:</p>
                  <ul>
                    <li><p><strong>Chuỗi cung ứng chuẩn quốc tế</strong> — Hợp tác cùng nhà máy Win Pharma (GMP-WHO) và vùng nguyên liệu đạt chuẩn xuất khẩu.</p></li>
                    <li><p><strong>Chất lượng &amp; truy xuất minh bạch</strong> — Mọi sản phẩm đều có COA, MSDS, C/O và hồ sơ kỹ thuật.</p></li>
                    <li><p><strong>Tầm nhìn toàn cầu — Bản sắc Việt</strong> — "Đông Sơn" lấy cảm hứng từ trống đồng Đông Sơn, biểu tượng trường tồn và tinh hoa Việt Nam.</p></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Tại sao chọn Đông Sơn Export?
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  <p><strong>Đông Sơn Export</strong> là đối tác tin cậy cho khách hàng muốn đưa sản phẩm thú y và nông sản sạch ra thị trường quốc tế.</p>
                  <p>Chọn Đông Sơn Export để đảm bảo sản phẩm của bạn đến tay đối tác quốc tế với độ tin cậy, an toàn và chất lượng cao nhất.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3">
          <div class="info-table">
            <ul>
              <li>
                <img src="assets/images/info-icon-01.png" alt="" style="max-width: 52px;">
                <h4>250 m2<br><span>Diện tích kho</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-02.png" alt="" style="max-width: 52px;">
                <h4>Hợp đồng<br><span>Sẵn sàng ký</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-03.png" alt="" style="max-width: 52px;">
                <h4>Thanh toán<br><span>Quy trình thanh toán</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-04.png" alt="" style="max-width: 52px;">
                <h4>An toàn<br><span>Giám sát 24/7</span></h4>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="video section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 offset-lg-4">
          <div class="section-heading text-center">
            <h6>| Giới thiệu về công ty</h6>
            <h2>Khám phá quy trình sản xuất và truy xuất nguồn gốc</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="video-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <div class="video-frame">
            <img src="images/banners/banner02.jpg" alt="">
            <a href="https://youtube.com" target="_blank"><i class="fa fa-play"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="fun-facts">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="wrapper">
            <div class="row">
              <div class="col-lg-4">
                <div class="counter">
                  <h2 class="timer count-title count-number" data-to="12" data-speed="1000"></h2>
                   <p class="count-text ">Năm<br>Kinh nghiệm</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="counter">
                  <h2 class="timer count-title count-number" data-to="10" data-speed="1000"></h2>
                  <p class="count-text ">Năm<br>Xuất khẩu</p>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="counter">
                  <h2 class="timer count-title count-number" data-to="24" data-speed="1000"></h2>
                  <p class="count-text ">Giải thưởng<br>Từ năm 2013</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="properties section">
    <div class="container">

      <div class="row">
        <div class="col-lg-4 offset-lg-4">
          <div class="section-heading text-center">
            <h6>| Sản Phẩm</h6>
            <h2>Danh mục sản phẩm</h2>
          </div>
        </div>
      </div>

      <div class="row">
        <style>
          .short-desc{
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 3.6em;
            margin-bottom: 0.75rem;
          }
          .item hr{border:0;border-top:1px solid #8f6600;margin:30px 0;}
        </style>

        <?php if (count($products) > 0): ?>
          <?php foreach ($products as $product): ?>
            <div class="col-lg-4 col-md-6">
              <div class="item">
                <a href="property-details.php?slug=<?php echo urlencode($product['slug']); ?>">
                  <?php if (!empty($product['image_path'])): ?>
                    <img src="./uploads/products/<?php echo htmlspecialchars($product['image_path']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                  <?php else: ?>
                    <img src="assets/images/no-image.png" alt="No image">
                  <?php endif; ?>
                </a>
                <span class="category"><?php echo !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'Sản phẩm'; ?></span>
                <br>
                <h4><a href="property-details.php?slug=<?php echo urlencode($product['slug']); ?>"><?php echo htmlspecialchars($product['title']); ?></a></h4>
                <p class="short-desc"><?php echo htmlspecialchars($product['short_description'] ?? 'Sản phẩm chất lượng cao từ Đông Sơn Export'); ?></p>
                <hr>
                <div class="main-button">
                  <a href="property-details.php?slug=<?php echo urlencode($product['slug']); ?>">Yêu cầu báo giá</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <p class="text-center">Chưa có sản phẩm nào. Vui lòng quay lại sau.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="news-posts section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 offset-lg-4">
          <div class="section-heading text-center">
            <h6>| Tin Tức</h6>
            <h2>Tin tức & Sự kiện</h2>
          </div>
        </div>
      </div>

      <div class="row">
        <style>
          .news-posts .item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.4s;
            margin-bottom: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
          }
          .news-posts .item:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(209, 165, 58, 0.3);
          }
          .news-posts .item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            transition: all 0.5s;
          }
          .news-posts .item:hover img {
            transform: scale(1.1);
          }
          .news-posts .item .content {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
          }
          .news-posts .item .post-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #888;
          }
          .news-posts .item .post-meta i {
            color: #D1A53A;
            margin-right: 5px;
          }
          .news-posts .item h4 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.4;
            min-height: 56px;
          }
          .news-posts .item h4 a {
            color: #2a2a2a;
            transition: all 0.3s;
          }
          .news-posts .item:hover h4 a {
            color: #D1A53A;
          }
          .news-posts .item .excerpt {
            font-size: 14px;
            line-height: 1.6;
            color: #666;
            margin-bottom: 15px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
          }
          .news-posts .item .read-more {
            display: inline-block;
            padding: 10px 25px;
            background: linear-gradient(135deg, #D1A53A 0%, #b8923a 100%);
            color: white;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            text-align: center;
            margin-top: auto;
          }
          .news-posts .item .read-more:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(209, 165, 58, 0.4);
          }
        </style>

        <?php if (count($posts) > 0): ?>
          <?php foreach ($posts as $post): ?>
            <div class="col-lg-4 col-md-6" style="margin-bottom: 20px;">
              <div class="item">
                <div class="image-wrapper" style="overflow: hidden;">
                  <a href="news-detail.php?slug=<?php echo urlencode($post['slug']); ?>">
                    <?php if (!empty($post['featured_image'])): ?>
                      <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <?php else: ?>
                      <img src="assets/images/default-post.jpg" alt="Default image">
                    <?php endif; ?>
                  </a>
                </div>
                <div class="content">
                  <div class="post-meta">
                    <span><i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                    <?php if (!empty($post['category_name'])): ?>
                      <span><i class="fa fa-folder"></i> <?php echo htmlspecialchars($post['category_name']); ?></span>
                    <?php endif; ?>
                  </div>
                  <h4><a href="news-detail.php?slug=<?php echo urlencode($post['slug']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h4>
                  <p class="excerpt"><?php echo htmlspecialchars($post['excerpt'] ?? strip_tags(substr($post['content'] ?? '', 0, 150)) . '...'); ?></p>
                  <a href="news-detail.php?slug=<?php echo urlencode($post['slug']); ?>" class="read-more">Đọc thêm <i class="fa fa-arrow-right"></i></a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <p class="text-center">Chưa có tin tức nào. Vui lòng quay lại sau.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="contact section">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 offset-lg-4">
          <div class="section-heading text-center">
            <h6>| Liên Hệ</h6>
            <h2>Liên hệ với Đông Sơn Export</h2>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="contact-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-7">
          <div id="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5477.546491309108!2d106.07864107714315!3d21.37293004539374!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31351532e09eaa4f%3A0xaeddb20687cfd03d!2zS2nDqm4gVGjhu6d5IEtow6FuaCBnacOgbmctIE5n4buNYyBDaMOidQ!5e0!3m2!1svi!2s!4v1762616660688!5m2!1svi!2s" width="100%" height="500px" frameborder="0" style="border:0; border-radius: 10px; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.15);" allowfullscreen=""></iframe>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="item phone">
                <img src="assets/images/phone-icon.png" alt="" style="max-width: 52px;">
                <h6>Hotline: +8456 821 5678<br><span>Hỗ trợ khách hàng</span></h6>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="item phone">
                <img src="assets/images/email-icon.png" alt="" style="max-width: 52px;">
                <h6>info@dongsongexport.vn<br><span>Email liên hệ</span></h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5">
          <div id="message-container" style="margin-bottom: 20px;"></div>
          <form id="contact-form" method="post">
            <div class="row">
              <div class="col-lg-12">
                <fieldset>
                  <label for="name">Họ và tên</label>
                  <input type="text" name="name" id="name" placeholder="Họ và tên..." autocomplete="on" required>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="email">Email</label>
                  <input type="email" name="email" id="email" placeholder="Email của bạn..." required>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="phone">Số điện thoại</label>
                  <input type="tel" name="phone" id="phone" placeholder="Số điện thoại..." autocomplete="on" required>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="subject">Chủ đề</label>
                  <input type="text" name="subject" id="subject" placeholder="Chủ đề..." autocomplete="on">
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <label for="message">Nội dung</label>
                  <textarea name="message" id="message" placeholder="Nội dung yêu cầu..." required></textarea>
                </fieldset>
              </div>
              <div class="col-lg-12">
                <fieldset>
                  <button type="submit" id="form-submit" class="orange-button">Gửi yêu cầu</button>
                </fieldset>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/contact.js"></script>
