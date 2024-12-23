<?php 
$pageTitle = 'Về chúng tôi - Gerrapp';
$currentPage = 'about';
$bodyClass = 'about-page';

// Define page-specific script files
$pageScripts = [
    'assets/vendor/php-email-form/validate.js',
    'assets/vendor/purecounter/purecounter_vanilla.js'
];

// Define page-specific inline scripts
$inlineScripts = [
    <<<EOT
    // FAQ list
    let faqList = document.querySelectorAll(".faq-list li");
    faqList.forEach((item) => {
      item.addEventListener("click", (e) => {
        item.classList.toggle("faq-active");
      });
    });
    EOT
];

include 'includes/header.php';
?>

<main class="main">

<div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.jpg);">
    <div class="container position-relative">
        <h1>Về chúng tôi</h1>
        <p>Gerrapp - Đối tác tin cậy trong lĩnh vực giao nhận vận tải và logistics.</p>
        <nav class="breadcrumbs">
        <ol>
            <li><a href="index.html">Trang chủ</a></li>
            <li class="current">Về chúng tôi</li>
        </ol>
        </nav>
    </div>
    </div><!-- End Page Title -->

    <!-- Về chúng tôi Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 position-relative align-self-start order-lg-last order-first" data-aos="fade-up" data-aos-delay="200">
            <img src="assets/img/about.jpg" class="img-fluid" alt="">
            <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox pulsating-play-btn"></a>
          </div>

          <div class="col-lg-6 content order-last  order-lg-first" data-aos="fade-up" data-aos-delay="100">
            <h3>Về chúng tôi</h3>
            <p>
              Gerrapp là đối tác đáng tin cậy trong lĩnh vực giao hàng và logistics. Chúng tôi cam kết mang đến dịch vụ chất lượng cao với giá cả cạnh tranh.
            </p>
            <ul>
              <li>
                <i class="bi bi-diagram-3"></i>
                <div>
                  <h5>Mạng lưới rộng khắp</h5>
                  <p>Hệ thống vận chuyển phủ sóng toàn quốc với thời gian giao hàng nhanh chóng</p>
                </div>
              </li>
              <li>
                <i class="bi bi-fullscreen-exit"></i>
                <div>
                  <h5>Dịch vụ chuyên nghiệp</h5>
                  <p>Đội ngũ nhân viên được đào tạo chuyên nghiệp, tận tâm với khách hàng</p>
                </div>
              </li>
              <li>
                <i class="bi bi-broadcast"></i>
                <div>
                  <h5>Công nghệ hiện đại</h5>
                  <p>Ứng dụng công nghệ tiên tiến trong quản lý và theo dõi đơn hàng</p>
                </div>
              </li>
            </ul>
          </div>

        </div>

      </div>

    </section><!-- /Về chúng tôi Section -->

    <!-- Stats Section -->
    <section id="stats" class="stats section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1" class="purecounter"></span>
              <p>Đối tác</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
              <p>Dự án</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="1" class="purecounter"></span>
              <p>Hỗ trợ</p>
            </div>
          </div><!-- End Stats Item -->

          <div class="col-lg-3 col-md-6">
            <div class="stats-item text-center w-100 h-100">
              <span data-purecounter-start="0" data-purecounter-end="32" data-purecounter-duration="1" class="purecounter"></span>
              <p>Nhân lực</p>
            </div>
          </div><!-- End Stats Item -->

        </div>

      </div>

    </section><!-- /Stats Section -->

    <!-- Team Section -->
    <section id="team" class="team section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Đội ngũ</span>
        <h2>Đội ngũ</h2>
        <p>Đội ngũ lãnh đạo giàu kinh nghiệm và tâm huyết với ngành logistics</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row">

          <div class="col-lg-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="100">
            <div class="member">
              <img src="assets/img/team/team-1.jpg" class="img-fluid" alt="">
              <div class="member-content">
                <h4>Nguyễn Văn A</h4>
                <span>Giám đốc điều hành</span>
                <p>
                  Hơn 15 năm kinh nghiệm trong ngành logistics và quản lý chuỗi cung ứng
                </p>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="200">
            <div class="member">
              <img src="assets/img/team/team-2.jpg" class="img-fluid" alt="">
              <div class="member-content">
                <h4>Nguyễn Thị B</h4>
                <span>Giám đốc Marketing</span>
                <p>
                  Chuyên gia marketing với hơn 10 năm kinh nghiệm trong ngành logistics
                </p>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-6 d-flex" data-aos="fade-up" data-aos-delay="300">
            <div class="member">
              <img src="assets/img/team/team-3.jpg" class="img-fluid" alt="">
              <div class="member-content">
                <h4>Trần Văn C</h4>
                <span>Giám đốc Vận hành</span>
                <p>
                  Quản lý vận hành với kinh nghiệm phong phú trong quản lý chuỗi cung ứng
                </p>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

        </div>

      </div>

    </section><!-- /Team Section -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section dark-background">

      <img src="assets/img/testimonials-bg.jpg" class="testimonials-bg" alt="">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              }
            }
          </script>
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-1.jpg" class="testimonial-img" alt="">
                <h3>Trần Văn B</h3>
                <h4>Giám đốc công ty ABC</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Dịch vụ giao hàng chuyên nghiệp, nhanh chóng và đáng tin cậy. Tôi rất hài lòng khi hợp tác với Gerrapp.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-2.jpg" class="testimonial-img" alt="">
                <h3>Nguyễn Thị D</h3>
                <h4>Chủ cửa hàng thời trang</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Dịch vụ giao hàng nhanh chóng, nhân viên thân thiện và chuyên nghiệp. Tôi rất hài lòng khi sử dụng dịch vụ của Gerrapp.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-3.jpg" class="testimonial-img" alt="">
                <h3>Lê Hoàng Cường</h3>
                <h4>Chủ cửa hàng</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Tôi đã sử dụng nhiều dịch vụ giao hàng khác nhau, nhưng Gerrapp là lựa chọn tốt nhất. Họ giúp việc kinh doanh của tôi phát triển với dịch vụ giao hàng đáng tin cậy.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-4.jpg" class="testimonial-img" alt="">
                <h3>Phạm Minh Đức</h3>
                <h4>Freelancer</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Là một freelancer, tôi thường xuyên cần gửi các sản phẩm cho khách hàng ở nhiều nơi khác nhau. Gerrapp giúp tôi tiết kiệm thời gian và chi phí với dịch vụ giao hàng hiệu quả.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-5.jpg" class="testimonial-img" alt="">
                <h3>Hoàng Thị Mai</h3>
                <h4>Doanh nhân</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Dịch vụ giao hàng chuyên nghiệp và đáng tin cậy. Đội ngũ nhân viên nhiệt tình, chu đáo. Tôi sẽ tiếp tục sử dụng dịch vụ của Gerrapp trong tương lai.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Faq Section -->
    <section id="faq" class="faq section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Câu hỏi thường gặp</span>
        <h2>Câu hỏi thường gặp</h2>
        <p>Những câu hỏi thường gặp về dịch vụ của chúng tôi</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row justify-content-center">

          <div class="col-lg-10">

            <div class="faq-container">

              <div class="faq-item faq-active" data-aos="fade-up" data-aos-delay="200">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Thời gian giao hàng mất bao lâu?</h3>
                <div class="faq-content">
                  <p>Thời gian giao hàng phụ thuộc vào khoảng cách và loại dịch vụ. Thông thường, giao hàng nội thành trong 24h và liên tỉnh từ 2-3 ngày.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Chi phí giao hàng được tính như thế nào?</h3>
                <div class="faq-content">
                  <p>Chi phí giao hàng được tính dựa trên khoảng cách, trọng lượng và kích thước của hàng hóa. Chúng tôi có nhiều gói dịch vụ khác nhau để khách hàng lựa chọn phù hợp với nhu cầu và ngân sách.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Làm thế nào để đăng ký trở thành đối tác của Gerrapp?</h3>
                <div class="faq-content">
                  <p>Để trở thành đối tác của Gerrapp, bạn có thể liên hệ với chúng tôi qua hotline hoặc email. Đội ngũ tư vấn sẽ hỗ trợ bạn trong quá trình đăng ký và cung cấp thông tin chi tiết về chính sách hợp tác.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item" data-aos="fade-up" data-aos-delay="500">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Gerrapp có dịch vụ giao hàng quốc tế không?</h3>
                <div class="faq-content">
                  <p>Có, Gerrapp cung cấp dịch vụ giao hàng quốc tế với nhiều tùy chọn về thời gian và chi phí. Chúng tôi có mạng lưới đối tác rộng khắp giúp đảm bảo hàng hóa được vận chuyển an toàn đến mọi nơi trên thế giới.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item" data-aos="fade-up" data-aos-delay="600">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Chính sách đền bù khi hàng hóa bị hư hỏng hoặc thất lạc?</h3>
                <div class="faq-content">
                  <p>Chúng tôi có chính sách bảo hiểm và đền bù rõ ràng cho mọi trường hợp hàng hóa bị hư hỏng hoặc thất lạc trong quá trình vận chuyển. Giá trị đền bù sẽ được tính dựa trên giá trị khai báo của hàng hóa và mức độ thiệt hại.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

            </div>

          </div>

        </div>

      </div>

    </section><!-- /Faq Section -->

</div>
</main>

<?php include 'includes/footer.php'; ?> 