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
    <meta name="description" content="Đông Sơn Export - Xuất khẩu thuốc thú y và nông sản sạch chất lượng cao">
    <meta name="keywords" content="xuất khẩu, thuốc thú y, nông sản sạch, Đông Sơn Export">
    <meta name="author" content="Đông Sơn Export">
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <title><?php echo isset($page_title) ? $page_title : 'Đông Sơn Export'; ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="./admin/images/logo.png">
    <link rel="alternate icon" type="image/png" href="./admin/images/logo.png">
    <link rel="apple-touch-icon" href="./admin/images/logo.png">
    <link rel="manifest" href="site.webmanifest">
    <meta name="theme-color" content="#D4AF37">

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-villa-agency.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/contact-buttons.css">
    
    <?php if(isset($additional_css)) echo $additional_css; ?>
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    
    <!-- Google Translate -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'vi',
                includedLanguages: 'en,ja,ko,th,vi',
                autoDisplay: false
            }, 'google_translate_element');
            
            // Khôi phục ngôn ngữ đã chọn
            restoreLanguage();
        }
        
        function changeLanguage(langCode) {
            var select = document.querySelector('.goog-te-combo');
            if (select) {
                // Nếu chuyển về tiếng Việt, reload trang để reset Google Translate
                if (langCode === 'vi') {
                    localStorage.setItem('selectedLanguage', 'vi');
                    
                    // Xóa cookie của Google Translate
                    document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                    document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=' + window.location.hostname;
                    
                    // Reload trang
                    window.location.reload();
                } else {
                    // Chuyển sang ngôn ngữ khác
                    select.value = langCode;
                    select.dispatchEvent(new Event('change'));
                    
                    // Lưu ngôn ngữ đã chọn
                    localStorage.setItem('selectedLanguage', langCode);
                    
                    // Cập nhật UI
                    updateLanguageUI(langCode);
                }
            }
        }
        
        function updateLanguageUI(langCode) {
            // Mapping code với thông tin hiển thị
            const langData = {
                '': { flag: 'vietnam.png', text: 'VN' },
                'vi': { flag: 'vietnam.png', text: 'VN' },
                'en': { flag: 'united-states.png', text: 'EN' },
                'ja': { flag: 'japan.png', text: 'JP' },
                'ko': { flag: 'south-korea.png', text: 'KR' },
                'th': { flag: 'thailand.png', text: 'TH' }
            };
            
            const data = langData[langCode];
            
            // Kiểm tra nếu không tìm thấy, dùng mặc định
            if (!data) {
                console.warn('Language code not found:', langCode);
                return;
            }
            
            // Cập nhật button hiện tại
            const currentLangBtn = document.getElementById('currentLang');
            if (currentLangBtn) {
                const flagImg = currentLangBtn.querySelector('.flag-icon');
                const langText = currentLangBtn.querySelector('.lang-text');
                
                if (flagImg) flagImg.src = 'images/' + data.flag;
                if (langText) langText.textContent = data.text;
            }
            
            // Cập nhật active state
            document.querySelectorAll('.lang-option').forEach(option => {
                const optionLang = option.getAttribute('data-lang');
                if (optionLang === langCode || (langCode === '' && optionLang === 'vi')) {
                    option.classList.add('active');
                } else {
                    option.classList.remove('active');
                }
            });
        }
        
        function restoreLanguage() {
            const savedLang = localStorage.getItem('selectedLanguage');
            if (savedLang && savedLang !== 'vi') {
                // Chỉ restore nếu không phải tiếng Việt
                // Đợi Google Translate load xong
                setTimeout(function() {
                    var select = document.querySelector('.goog-te-combo');
                    if (select) {
                        select.value = savedLang;
                        select.dispatchEvent(new Event('change'));
                        updateLanguageUI(savedLang);
                    }
                }, 1000);
            } else {
                // Nếu là tiếng Việt, đảm bảo UI đúng
                updateLanguageUI('vi');
            }
        }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    
    <style>
        /* Ẩn Google Translate banner và widget mặc định */
        .goog-te-banner-frame {
            display: none !important;
        }
        
        body {
            top: 0 !important;
        }
        
        #google_translate_element {
            display: none;
        }
        
        .skiptranslate {
            display: none !important;
        }
    </style>
<!--

TemplateMo 591 villa agency

https://templatemo.com/tm-591-villa-agency

-->
  </head>

<body>

  <!-- Hidden Google Translate Element -->
  <div id="google_translate_element"></div>

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
            <li><i class="fa fa-phone"></i> 056 821 5678</li>
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
                    <a href="index.php" class="logo" style="margin-bottom: 25px !important;">
                        <img src="./admin/images/logo.png" alt="Đông Sơn Logo" class="logo-icon">
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
                            <a href="#" class="lang-option active" data-lang="vi" onclick="changeLanguage('vi'); return false;">
                                <img src="images/vietnam.png" alt="Tiếng Việt">
                                <span>Tiếng Việt</span>
                            </a>
                            <a href="#" class="lang-option" data-lang="en" onclick="changeLanguage('en'); return false;">
                                <img src="images/united-states.png" alt="English">
                                <span>English</span>
                            </a>
                            <a href="#" class="lang-option" data-lang="ja" onclick="changeLanguage('ja'); return false;">
                                <img src="images/japan.png" alt="日本語">
                                <span>日本語</span>
                            </a>
                            <a href="#" class="lang-option" data-lang="ko" onclick="changeLanguage('ko'); return false;">
                                <img src="images/south-korea.png" alt="한국어">
                                <span>한국어</span>
                            </a>
                            <a href="#" class="lang-option" data-lang="th" onclick="changeLanguage('th'); return false;">
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
                      <li><a href="news.php" class="<?php echo (isset($page) && ($page == 'news' || $page == 'news-detail')) ? 'active' : ''; ?>">Tin Tức</a></li>
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
