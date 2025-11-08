<?php 
// If $page is not set by the page, infer it from the current script filename
if (!isset($page)) {
    $script = basename($_SERVER['SCRIPT_NAME']);
    switch ($script) {
        case 'index.php':
            $page = 'home';
            break;
        case 'about.php':
            $page = 'about';
            break;
        case 'properties.php':
            $page = 'properties';
            break;
        case 'property-details.php':
            $page = 'property-details';
            break;
        case 'contact.php':
            $page = 'contact';
            break;
        default:
            $page = '';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />

  <title><?php echo isset($page_title) ? $page_title : 'Đông Sơn Export'; ?></title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    
    <?php if(isset($additional_css)) echo $additional_css; ?>
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
<!--

TemplateMo 591 villa agency

https://templatemo.com/tm-591-villa-agency

-->
  </head>

<body>

  <!-- ***** Preloader Start ***** -->
  <div id="js-preloader" class="js-preloader">
    <div class="preloader-inner">
      <span class="dot"></span>
      <div class="dots">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  </div>
  <!-- ***** Preloader End ***** -->

  <div class="sub-header">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-8">
          <ul class="info">
            <li><i class="fa fa-envelope"></i> info@dongsonexport.com</li>
            <li><i class="fa fa-phone"></i> +84 965 032 630</li>
          </ul>
        </div>
        <div class="col-lg-4 col-md-4">
          <ul class="social-links">
            <li><a href="#"><i class="fab fa-facebook"></i></a></li>
            <li><a href="https://x.com/minthu" target="_blank"><i class="fab fa-twitter"></i></a></li>
            <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- ***** Header Area Start ***** -->
  <header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- ***** Logo Start ***** -->
                    <a href="index.php" class="logo">
                        <h1>Dongson</h1>
                    </a>
                    <!-- ***** Logo End ***** -->
                    
                    <!-- ***** Language Switcher Start ***** -->
                    <div class="language-switcher">
                        <button class="lang-btn" id="currentLang">
                            <img src="images/vietnam.png" alt="Tiếng Việt" class="flag-icon">
                            <span class="lang-text">VN</span>
                            <i class="fa fa-chevron-down"></i>
                        </button>
                        <div class="lang-dropdown" id="langDropdown">
                            <a href="#" class="lang-option active" data-lang="vi">
                                <img src="images/vietnam.png" alt="Tiếng Việt">
                                <span>Tiếng Việt</span>
                            </a>
                            <a href="#" class="lang-option" data-lang="en">
                                <img src="images/united-states.png" alt="English">
                                <span>English</span>
                            </a>
                            <a href="#" class="lang-option" data-lang="jp">
                                <img src="images/japan.png" alt="日本語">
                                <span>日本語</span>
                            </a>
                            <a href="#" class="lang-option" data-lang="kr">
                                <img src="images/south-korea.png" alt="한국어">
                                <span>한국어</span>
                            </a>
                            <a href="#" class="lang-option" data-lang="th">
                                <img src="images/thailand.png" alt="ไทย">
                                <span>ไทย</span>
                            </a>
                        </div>
                    </div>
                    <!-- ***** Language Switcher End ***** -->
                    
                    <!-- ***** Menu Start ***** -->
                    <ul class="nav">
                      <li><a href="index.php" class="<?php echo (isset($page) && $page == 'home') ? 'active' : ''; ?>">Trang Chủ</a></li>
                      <li><a href="about.php" class="<?php echo (isset($page) && $page == 'about') ? 'active' : ''; ?>">Giới Thiệu</a></li>
                      <li><a href="properties.php" class="<?php echo (isset($page) && $page == 'properties') ? 'active' : ''; ?>">Sản Phẩm</a></li>
                      <li><a href="contact.php" class="<?php echo (isset($page) && $page == 'contact') ? 'active' : ''; ?>">Liên Hệ</a></li>
                      <li><a href="#"><i class="fa fa-calendar"></i> Yêu cầu báo giá</a></li>
                  </ul>   
                    <a class='menu-trigger'>
                        <span>Menu</span> 
                    </a>
                    <!-- ***** Menu End ***** -->
                </nav>
            </div>
        </div>
    </div>
  </header>
  <!-- ***** Header Area End ***** -->
