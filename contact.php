<?php 
$pageTitle = 'Liên hệ - Gerrapp';
$currentPage = 'contact';
$bodyClass = 'contact-page';

// Define page-specific script files
$pageScripts = [
    'assets/vendor/php-email-form/validate.js',
    'assets/vendor/purecounter/purecounter_vanilla.js'
];

include 'includes/header.php';
?>

<main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.jpg);">
      <div class="container position-relative">
        <h1>Liên hệ</h1>
        <p>Hãy liên hệ với chúng tôi nếu bạn cần hỗ trợ hoặc có bất kỳ thắc mắc nào. Đội ngũ của chúng tôi luôn sẵn sàng phục vụ quý khách.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Trang chủ</a></li>
            <li class="current">Liên hệ</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Liên hệ Section -->
    <section id="contact" class="contact section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="mb-4" data-aos="fade-up" data-aos-delay="200">
          <iframe 
            style="border:0; width: 100%; height: 270px;" 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3725.146595746947!2d105.78033147471336!3d20.986759989220904!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135addbfec179fb%3A0x1960313a0087edc!2sTSQ%20EuroLand%20Building!5e0!3m2!1sen!2s!4v1728751163582!5m2!1sen!2s" 
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div><!-- End Google Maps -->

        <div class="row gy-4">

          <div class="col-lg-4">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Địa chỉ</h3>
                <p>25 P. Nguyễn Văn Lộc, P. Mộ Lao, Hà Đông, Hà Nội 100000, Việt Nam</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Điện thoại</h3>
                <p>+84 081 969 1312</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
              <i class="bi bi-envelope flex-shrink-0"></i>
              <div>
                <h3>Email</h3>
                <p>ttdat171203@gmail.com</p>
              </div>
            </div><!-- End Info Item -->

          </div>

          <div class="col-lg-8">
            <form action="http://localhost/Gerrapp/forms/process_contact.php" method="POST" class="php-email-form">
              <div class="form-group mt-3">
                <input type="text" class="form-control" name="subject" placeholder="Tiêu đề" required>
              </div>
              <div class="form-group mt-3">
                <textarea class="form-control" name="message" rows="5" placeholder="Nội dung" required></textarea>
              </div>
              <div class="my-3">
                <div class="loading">Đang gửi...</div>
                <div class="error-message"></div>
                <div class="sent-message">Tin nhắn của bạn đã được gửi. Cảm ơn!</div>
              </div>
              <div class="text-center"><button type="submit">Gửi tin nhắn</button></div>
            </form>
          </div><!-- End Liên hệ Form -->

        </div>

      </div>

    </section><!-- /Liên hệ Section -->

</main>

<?php include 'includes/footer.php'; ?> 