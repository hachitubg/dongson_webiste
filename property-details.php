<?php 
    $page = 'property-details';
    $page_title = 'Đông Sơn Export - Chi tiết sản phẩm';
    include 'includes/header.php'; 
?>

<!-- Import product details CSS instead of inline styles -->
<link rel="stylesheet" href="assets/css/product-details.css">

  <div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <span class="breadcrumb"><a href="index.php">Trang Chủ</a> / <a href="properties.php">Sản Phẩm</a> / Chi Tiết Sản Phẩm</span>
          <h3>Chi Tiết Sản Phẩm</h3>
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
            <img id="mainImage" class="main-product-image" src="images_winvet/BỘ SẢN PHẨM MỚI/W-AMPICOL HỘP GIẤY 1KG WINVET.png" alt="Sản phẩm chính">
            
            <div class="thumbnail-gallery">
              <div class="thumbnail-item active" onclick="changeImage(this, 'images_winvet/BỘ SẢN PHẨM MỚI/W-AMPICOL HỘP GIẤY 1KG WINVET.png')">
                <img src="images_winvet/BỘ SẢN PHẨM MỚI/W-AMPICOL HỘP GIẤY 1KG WINVET.png" alt="Ảnh 1">
              </div>
              <div class="thumbnail-item" onclick="changeImage(this, 'images_winvet/BỘ SẢN PHẨM MỚI/AMOCICOL 200W HỘP GIẤY 1KG WINVET.png')">
                <img src="images_winvet/BỘ SẢN PHẨM MỚI/AMOCICOL 200W HỘP GIẤY 1KG WINVET.png" alt="Ảnh 2">
              </div>
              <div class="thumbnail-item" onclick="changeImage(this, 'images_winvet/BỘ SẢN PHẨM MỚI/W-CANXI NANO 1 LÍT WIN VET.png')">
                <img src="images_winvet/BỘ SẢN PHẨM MỚI/W-CANXI NANO 1 LÍT WIN VET.png" alt="Ảnh 3">
              </div>
              <div class="thumbnail-item" onclick="changeImage(this, 'images_winvet/BỘ SẢN PHẨM MỚI/W-PHAGE 500ml WINVET.png')">
                <img src="images_winvet/BỘ SẢN PHẨM MỚI/W-PHAGE 500ml WINVET.png" alt="Ảnh 4">
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Product Content -->
        <div class="product-info-section">
          <div class="product-content">
            <span class="category">Kháng sinh</span>
            <h4>W-AMPICOL - Kháng sinh đường uống cho gia súc, gia cầm</h4>
            
            <div class="product-description">
              <h5>Thành phần:</h5>
              <ul>
                <li>Ampicillin (dưới dạng Ampicillin trihydrate): 200g</li>
                <li>Colistin (dưới dạng Colistin sulfate): 1.200.000 UI</li>
                <li>Tá dược vừa đủ: 1kg</li>
              </ul>

              <h5>Công dụng:</h5>
              <p>Điều trị các bệnh nhiễm khuẩn đường tiêu hóa, đường hô hấp ở gia súc, gia cầm do vi khuẩn nhạy cảm với Ampicillin và Colistin gây ra như:</p>
              <ul>
                <li>Bệnh tụ huyết trùng</li>
                <li>Bệnh phó thương hàn, thương hàn ở lợn, gia cầm</li>
                <li>Bệnh tiêu chảy vàng, tiêu chảy trắng, kiết lỵ</li>
                <li>Bệnh nhiễm trùng đường hô hấp</li>
                <li>Viêm ruột do E.coli, Salmonella</li>
              </ul>

              <h5>Liều dùng và cách dùng:</h5>
              <p><strong>Pha vào nước uống:</strong></p>
              <ul>
                <li>Gia cầm: 1g/2-3 lít nước uống, dùng liên tục 3-5 ngày</li>
                <li>Lợn: 1g/1-2 lít nước uống, dùng liên tục 3-5 ngày</li>
              </ul>

              <h5>Quy cách đóng gói:</h5>
              <p>Hộp giấy 1kg (10 gói x 100g)</p>

              <h5>Bảo quản:</h5>
              <p>Nơi khô mát, tránh ánh sáng trực tiếp. Nhiệt độ dưới 30°C</p>

              <h5>Thời gian ngưng thuốc:</h5>
              <ul>
                <li>Thịt gia cầm: 7 ngày trước khi giết mổ</li>
                <li>Thịt lợn: 14 ngày trước khi giết mổ</li>
              </ul>

              <h5>Xuất xứ:</h5>
              <p>Sản xuất bởi Win Pharma - Việt Nam<br>
              Tiêu chuẩn: GMP-WHO</p>
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
      <div class="related-products">
        <div class="row">
          <div class="col-lg-12">
            <div class="section-heading">
              <h6>| Sản Phẩm Liên Quan</h6>
              <h2>Các sản phẩm khác trong danh mục Kháng sinh</h2>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <img class="product-card-image" src="images_winvet/BỘ SẢN PHẨM MỚI/AMOCICOL 200W HỘP GIẤY 1KG WINVET.png" alt="AMOCICOL 200W">
              <div class="product-card-body">
                <div class="product-card-category">Kháng sinh</div>
                <h5 class="product-card-title">AMOCICOL 200W - Kháng sinh hỗn hợp</h5>
                <a href="property-details.php" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <img class="product-card-image" src="images_winvet/BỘ SẢN PHẨM MỚI/W-DOXY PLUS 1KG WIN VET.png" alt="W-DOXY PLUS">
              <div class="product-card-body">
                <div class="product-card-category">Kháng sinh</div>
                <h5 class="product-card-title">W-DOXY PLUS - Kháng sinh Doxycycline</h5>
                <a href="property-details.php" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <img class="product-card-image" src="images_winvet/BỘ SẢN PHẨM MỚI/OXYVET 50_ 1KG WIN VET.png" alt="OXYVET 50">
              <div class="product-card-body">
                <div class="product-card-category">Kháng sinh</div>
                <h5 class="product-card-title">OXYVET 50 - Kháng sinh Oxytetracycline</h5>
                <a href="property-details.php" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <img class="product-card-image" src="images_winvet/BỘ SẢN PHẨM MỚI/W-COLIS 1KG WIN VET.png" alt="W-COLIS">
              <div class="product-card-body">
                <div class="product-card-category">Kháng sinh</div>
                <h5 class="product-card-title">W-COLIS - Kháng sinh Colistin</h5>
                <a href="property-details.php" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Other Products Section -->
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
          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <img class="product-card-image" src="images_winvet/BỘ SẢN PHẨM MỚI/W-CANXI NANO 1 LÍT WIN VET.png" alt="W-CANXI NANO">
              <div class="product-card-body">
                <div class="product-card-category">Dinh dưỡng</div>
                <h5 class="product-card-title">W-CANXI NANO - Bổ sung canxi</h5>
                <a href="property-details.php" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <img class="product-card-image" src="images_winvet/BỘ SẢN PHẨM MỚI/SIÊU MEN VIT 5 LÍT WINVET.png" alt="SIÊU MEN VIT">
              <div class="product-card-body">
                <div class="product-card-category">Dinh dưỡng</div>
                <h5 class="product-card-title">SIÊU MEN VIT - Vitamin tổng hợp</h5>
                <a href="property-details.php" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <img class="product-card-image" src="images_winvet/BỘ SẢN PHẨM MỚI/W-PHAGE 500ml WINVET.png" alt="W-PHAGE">
              <div class="product-card-body">
                <div class="product-card-category">Chế phẩm sinh học</div>
                <h5 class="product-card-title">W-PHAGE - Diệt khuẩn sinh học</h5>
                <a href="property-details.php" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-md-6" style="padding-bottom: 20px;">
            <div class="product-card">
              <img class="product-card-image" src="images_winvet/BỘ SẢN PHẨM MỚI/SORBITOL 5 LÍT WIN VET.png" alt="SORBITOL">
              <div class="product-card-body">
                <div class="product-card-category">Dinh dưỡng</div>
                <h5 class="product-card-title">SORBITOL - Bổ sung năng lượng</h5>
                <a href="property-details.php" class="btn-view-detail">Xem chi tiết</a>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

<script>
  function changeImage(element, imageSrc) {
    // Update main image
    document.getElementById('mainImage').src = imageSrc;
    
    // Remove active class from all thumbnails
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    thumbnails.forEach(thumb => thumb.classList.remove('active'));
    
    // Add active class to clicked thumbnail
    element.classList.add('active');
  }
</script>

<?php include 'includes/footer.php'; ?>