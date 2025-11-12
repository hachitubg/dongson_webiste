<?php 
    $page = 'contact';
    $page_title = 'Đông Sơn Export - Xuất khẩu thuốc thú y & nông sản sạch';
    include 'includes/header.php'; 
?>


  <div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <span class="breadcrumb"><a href="#">Trang Chủ</a>  /  Liên Hệ</span>
          <h3>Liên Hệ với Đông Sơn Export</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="contact-page section">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <div class="section-heading">
            <h6>| Liên Hệ</h6>
            <h2>Hãy gửi thông tin cho chúng tôi, chúng tôi sẽ liên lạc lại để tư vấn cho bạn sớm nhất !</h2>
          </div>
          <p>ĐÔNG SƠN EXPORT., JSC — Xuất khẩu thuốc thú y và nông sản sạch. Chúng tôi hỗ trợ chứng nhận và hồ sơ xuất khẩu cho từng thị trường.</p>
          <div class="row">
            <div class="col-lg-12">
                <div class="item phone">
                <img src="assets/images/phone-icon.png" alt="" style="max-width: 52px;">
                <h6>+84 912 345 678<br><span>Hotline</span></h6>
              </div>
            </div>
            <div class="col-lg-12">
              <div class="item email">
                <img src="assets/images/email-icon.png" alt="" style="max-width: 52px;">
                <h6>info@dongson-export.com<br><span>Email</span></h6>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
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
                  <textarea name="message" id="message" placeholder="Nội dung yêu cầu / hồ sơ kỹ thuật..." required></textarea>
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
        <div class="col-lg-12">
          <div id="map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5477.546491309108!2d106.07864107714315!3d21.37293004539374!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31351532e09eaa4f%3A0xaeddb20687cfd03d!2zS2nDqm4gVGjhu6d5IEtow6FuaCBnacOgbmctIE5n4buNYyBDaMOidQ!5e0!3m2!1svi!2s!4v1762616660688!5m2!1svi!2s" width="100%" height="500px" frameborder="0" style="border:0; border-radius: 10px; box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.15);" allowfullscreen=""></iframe>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>

<script src="assets/js/contact.js"></script>