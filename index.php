<?php 
$pageTitle = 'Trang chủ - Gerrapp Bootstrap Template';
$currentPage = 'home';
$bodyClass = 'index-page';

// Define page-specific script files
$pageScripts = [
    'assets/vendor/purecounter/purecounter_vanilla.js',
    'assets/vendor/php-email-form/validate.js'
];

// Define page-specific inline scripts
$inlineScripts = [
    <<<EOT
    // Hero carousel indicators
    let heroCarouselIndicators = document.querySelector("#hero .carousel-indicators");
    let heroCarouselItems = document.querySelectorAll('#hero .carousel-item')
    
    heroCarouselItems.forEach((item, index) => {
      (index === 0) ?
      heroCarouselIndicators.innerHTML += "<button type='button' data-bs-target='#hero' data-bs-slide-to='" + index + "' class='active' aria-current='true' aria-label='Slide " + (index + 1) + "'></button>" :
      heroCarouselIndicators.innerHTML += "<button type='button' data-bs-target='#hero' data-bs-slide-to='" + index + "' aria-current='true' aria-label='Slide " + (index + 1) + "'></button>"
    });
    EOT
];

include 'includes/header.php';
?>

<main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <img src="assets/img/world-dotted-map.png" alt="" class="hero-bg" data-aos="fade-in">

      <div class="container">
        <div class="row gy-4 d-flex justify-content-between">
          <div class="col-lg-6 order-2 order-lg-1 d-flex flex-column justify-content-center">
            <h2 data-aos="fade-up">Dịch vụ vận chuyển siêu tốc</h2>
            <p data-aos="fade-up" data-aos-delay="100">
              Gerrapp Corporation là nhà cung cấp dịch vụ chuyển phát tổng thể hàng đầu Việt nam có khả năng đáp ứng mọi nhu cầu của khách hàng. 
              Với hơn xx năm kinh nghiệm trong lĩnh vực giao nhận vận tải kết hợp với cơ sở hạ tầng hiện đại, công nghệ tiên tiến và dịch vụ chuyên nghiệp cho phép chúng tôi cung cấp một chuỗi cung ứng hiệu quả cho tất cả khách hàng.
            </p>

            <div class="row gy-4" data-aos="fade-up" data-aos-delay="300">

              <div class="col-lg-3 col-6">
                <div class="stats-item text-center w-100 h-100">
                  <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="0" class="purecounter">232</span>
                  <p>Đối tác</p>
                </div>
              </div><!-- End Stats Item -->

              <div class="col-lg-3 col-6">
                <div class="stats-item text-center w-100 h-100">
                  <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="0" class="purecounter">521</span>
                  <p>Dự án</p>
                </div>
              </div><!-- End Stats Item -->

              <div class="col-lg-3 col-6">
                <div class="stats-item text-center w-100 h-100">
                  <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="0" class="purecounter">1453</span>
                  <p>Hỗ trợ</p>
                </div>
              </div><!-- End Stats Item -->

              <div class="col-lg-3 col-6">
                <div class="stats-item text-center w-100 h-100">
                  <span data-purecounter-start="0" data-purecounter-end="32" data-purecounter-duration="0" class="purecounter">32</span>
                  <p>Nhân lực</p>
                </div>
              </div><!-- End Stats Item -->

            </div>

          </div>

          <div class="col-lg-5 order-1 order-lg-2 hero-img" data-aos="zoom-out">
            <img src="assets/img/hero-img.svg" class="img-fluid mb-3 mb-lg-0" alt="">
          </div>

        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- Featured Services Section -->
    <section id="featured-services" class="featured-services section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="100">
            <div class="icon flex-shrink-0"><i class="fa-solid fa-cart-flatbed"></i></div>
            <div>
              <h4 class="title">Kho bãi</h4>
              <p class="description">Hệ thống kho bãi hiện đại, rộng rãi với công nghệ quản lý tiên tiến</p>
              <a href="#" class="readmore stretched-link"><span>Tìm hiểu thêm</span><i class="bi bi-arrow-right"></i></a>
            </div>
          </div>
          <!-- End Service Item -->

          <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="200">
            <div class="icon flex-shrink-0"><i class="fa-solid fa-truck"></i></div>
            <div>
              <h4 class="title">Vận chuyển</h4>
              <p class="description">Dịch vụ vận chuyển nhanh chóng, an toàn với đội ngũ tài xế chuyên nghiệp</p>
              <a href="#" class="readmore stretched-link"><span>Tìm hiểu thêm</span><i class="bi bi-arrow-right"></i></a>
            </div>
          </div><!-- End Service Item -->

          <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="300">
            <div class="icon flex-shrink-0"><i class="fa-solid fa-truck-ramp-box"></i></div>
            <div>
              <h4 class="title">Đóng gói</h4>
              <p class="description">Dịch vụ đóng gói chuyên nghiệp, bảo vệ hàng hóa an toàn</p>
              <a href="#" class="readmore stretched-link"><span>Tìm hiểu thêm</span><i class="bi bi-arrow-right"></i></a>
            </div>
          </div><!-- End Service Item -->

        </div>

      </div>

    </section><!-- /Featured Services Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-6 position-relative align-self-start order-lg-last order-first" data-aos="fade-up" data-aos-delay="200">
            <img src="assets/img/about.jpg" class="img-fluid" alt="">
            <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox pulsating-play-btn"></a>
          </div>

          <div class="col-lg-6 content order-last order-lg-first" data-aos="fade-up" data-aos-delay="100">
            <h3>Sứ mệnh: </h3>
            <p>
              Trở thành doanh nghiệp cung cấp dịch vụ logistics trọn gói hàng đầu Việt Nam:
            </p>
            <ul>
              <li>
                <i class="bi bi-diagram-3"></i>
                <div>
                  <h5>Sở hữu, vận hành và khai thác những trung tâm logistics hàng đầu cả nước</h5>
                  <!-- <p>Hoàn thiện chuỗi cung ứng dịch vụ Logistics trên phạm vi toàn quốc</p> -->
                </div>
              </li>
              <li>
                <i class="bi bi-fullscreen-exit"></i>
                <div>
                  <h5>Hoàn thiện chuỗi cung ứng dịch vụ Logistics trên phạm vi toàn quốc</h5>
                  <!-- <p>Quo totam dolorum at pariatur aut distinctio dolorum laudantium illo direna pasata redi</p> -->
                </div>
              </li>
              <li>
                <i class="bi bi-broadcast"></i>
                <div>
                  <h5>Tạo ra giải pháp Logistics tổng thể, trọn gói, tích hợp, đáp ứng yêu cầu của khách hàng</h5>
                  <!-- <p>Et velit et eos maiores est tempora et quos dolorem autem tempora incidunt maxime veniam</p> -->
                </div>
              </li>
            </ul>
          </div>

        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Services Section -->
    <section id="services" class="services section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Dịch vụ</span>
        <h2>Dịch vụ</h2>
        <!-- <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p> -->
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="card">
              <div class="card-img">
                <img src="assets/img/service-1.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="#" class="stretched-link">Kho bãi</a></h3>
              <!-- <p>Cumque eos in qui numquam. Aut aspernatur perferendis sed atque quia voluptas quisquam repellendus temporibus itaqueofficiis odit</p> -->
            </div>
          </div><!-- End Card Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="card">
              <div class="card-img">
                <img src="assets/img/service-2.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="#" class="stretched-link">Logistics</a></h3>
              <!-- <p>Asperiores provident dolor accusamus pariatur dolore nam id audantium ut et iure incidunt molestiae dolor ipsam ducimus occaecati nisi</p> -->
            </div>
          </div><!-- End Card Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="card">
              <div class="card-img">
                <img src="assets/img/service-3.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="#" class="stretched-link">Vận tải biển</a></h3>
              <!-- <p>Dicta quam similique quia architecto eos nisi aut ratione aut ipsum reiciendis sit doloremque oluptatem aut et molestiae ut et nihil</p> -->
            </div>
          </div><!-- End Card Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
            <div class="card">
              <div class="card-img">
                <img src="assets/img/service-4.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="#" class="stretched-link">Vận tải bộ</a></h3>
              <!-- <p>Dicta quam similique quia architecto eos nisi aut ratione aut ipsum reiciendis sit doloremque oluptatem aut et molestiae ut et nihil</p> -->
            </div>
          </div><!-- End Card Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="card">
              <div class="card-img">
                <img src="assets/img/service-5.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="#" class="stretched-link">Đóng gói</a></h3>
              <!-- <p>Illo consequuntur quisquam delectus praesentium modi dignissimos facere vel cum onsequuntur maiores beatae consequatur magni voluptates</p> -->
            </div>
          </div><!-- End Card Item -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="card">
              <div class="card-img">
                <img src="assets/img/service-6.jpg" alt="" class="img-fluid">
              </div>
              <h3><a href="#" class="stretched-link">Kho vận</a></h3>
              <!-- <p>Quas assumenda non occaecati molestiae. In aut earum sed natus eatae in vero. Ab modi quisquam aut nostrum unde et qui est non quo nulla</p> -->
            </div>
          </div><!-- End Card Item -->

        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- Call To Action Section -->
    <section id="call-to-action" class="call-to-action section dark-background">

      <img src="assets/img/cta-bg.jpg" alt="">

      <div class="container">
        <div class="row justify-content-center" data-aos="zoom-in" data-aos-delay="100">
          <div class="col-xl-10">
            <div class="text-center">
              <h3>Đặt hàng ngay</h3>
              <p>Bắt đầu sử dụng ngay dịch vụ chuyển phát nhanh của chúng tôi.</p>
              <a class="cta-btn" href="order.php">Đặt hàng ngay</a>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Call To Action Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Tiện ích</span>
        <h2>Tiện ích</h2>
        <!-- <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p> -->
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4 align-items-center features-item">
          <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="100">
            <img src="assets/img/features-1.jpg" class="img-fluid" alt="">
          </div>
          <div class="col-md-7" data-aos="fade-up" data-aos-delay="100">
            <h3>Giao hàng đến tận tay cho khách</h3>
            <p class="fst-italic">
              Dịch vụ giao hàng tận nơi chuyên nghiệp, nhanh chóng và an toàn
            </p>
            <ul>
              <li><i class="bi bi-check"></i><span>Đội ngũ nhân viên giao hàng chuyên nghiệp</span></li>
              <li><i class="bi bi-check"></i><span>Theo dõi đơn hàng trực tuyến</span></li>
              <li><i class="bi bi-check"></i><span>Bảo hiểm hàng hóa</span></li>
            </ul>
          </div>
        </div><!-- Features Item -->

        <div class="row gy-4 align-items-center features-item">
          <div class="col-md-5 order-1 order-md-2 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
            <img src="assets/img/features-2.jpg" class="img-fluid" alt="">
          </div>
          <div class="col-md-7 order-2 order-md-1" data-aos="fade-up" data-aos-delay="200">
            <h3>Dịch vụ chuyển phát siêu tốc</h3>
            <p class="fst-italic">
              Dịch vụ chuyển phát nhanh với thời gian giao hàng nhanh chóng
            </p>
            <p>
              Chúng tôi cam kết thời gian giao hàng nhanh nhất có thể với chất lượng dịch vụ tốt nhất. Đội ngũ nhân viên chuyên nghiệp và hệ thống vận chuyển hiện đại giúp đảm bảo hàng hóa được giao đến tay khách hàng an toàn và đúng hẹn.
            </p>
          </div>
        </div><!-- Features Item -->

        <div class="row gy-4 align-items-center features-item">
          <div class="col-md-5 d-flex align-items-center" data-aos="zoom-out">
            <img src="assets/img/features-3.jpg" class="img-fluid" alt="">
          </div>
          <div class="col-md-7" data-aos="fade-up">
            <h3>Liên kết hợp tác với các nhãn hàng nổi tiếng</h3>
            <p>Chúng tôi tự hào là đối tác tin cậy của nhiều thương hiệu lớn trong và ngoài nước</p>
            <ul>
              <li><i class="bi bi-check"></i><span>Hợp tác với các thương hiệu hàng đầu</span></li>
              <li><i class="bi bi-check"></i><span>Dịch vụ chuyên biệt cho từng đối tác</span></li>
              <li><i class="bi bi-check"></i><span>Giải pháp logistics tổng thể</span></li>
            </ul>
          </div>
        </div><!-- Features Item -->

        <div class="row gy-4 align-items-center features-item">
          <div class="col-md-5 order-1 order-md-2 d-flex align-items-center" data-aos="zoom-out">
            <img src="assets/img/features-4.jpg" class="img-fluid" alt="">
          </div>
          <div class="col-md-7 order-2 order-md-1" data-aos="fade-up">
            <h3>Bộ phận chăm sóc khách hàng chuyên nghiệp</h3>
            <p class="fst-italic">
              Đội ngũ chăm sóc khách hàng 24/7 luôn sẵn sàng hỗ trợ mọi thắc mắc
            </p>
            <p>
              Chúng tôi luôn đặt sự hài lòng của khách hàng lên hàng đầu. Đội ngũ chăm sóc khách hàng chuyên nghiệp của chúng tôi luôn sẵn sàng hỗ trợ và giải đáp mọi thắc mắc của quý khách 24/7.
            </p>
          </div>
        </div><!-- Features Item -->

      </div>

    </section><!-- /Features Section -->

    <!-- Pricing Section -->
    <section id="pricing" class="pricing section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <span>Gói dịch vụ</span>
        <h2>Gói dịch vụ</h2>
        <!-- <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p> -->
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="100">
            <div class="pricing-item d-flex flex-column">
              <h3>Cơ bản</h3>
              <h4><sup>VND</sup><span style="font-size: 0.8em">0</span><span> / tháng</span></h4>
              <ul>
                <li><i class="bi bi-check"></i>Giao hàng nội thành</li>
                <li><i class="bi bi-check"></i>Theo dõi đơn hàng cơ bản</li>
                <li><i class="bi bi-check"></i>Hỗ trợ trong giờ hành chính</li>
                <li class="na"><i class="bi bi-x"></i>Bảo hiểm hàng hóa</li>
                <li class="na"><i class="bi bi-x"></i>Giao hàng ưu tiên</li>
              </ul>
              <a href="order.php" class="buy-btn mt-auto w-auto text-center">Đặt hàng</a>
            </div>
          </div><!-- End Pricing Item -->

          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="200">
            <div class="pricing-item d-flex flex-column">
              <h3>Tiêu chuẩn</h3>
              <h4><sup>VND</sup><span style="font-size: 0.8em">299.000</span><span> / tháng</span></h4>
              <ul>
                <li><i class="bi bi-check"></i>Giao hàng toàn quốc</li>
                <li><i class="bi bi-check"></i>Theo dõi đơn hàng chi tiết</li>
                <li><i class="bi bi-check"></i>Hỗ trợ 24/7</li>
                <li><i class="bi bi-check"></i>Bảo hiểm hàng hóa cơ bản</li>
                <li><i class="bi bi-check"></i>Giao hàng ưu tiên</li>
              </ul>
              <a href="order.php" class="buy-btn mt-auto w-auto text-center">Đặt hàng</a>
            </div>
          </div><!-- End Pricing Item -->

          <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="300">
            <div class="pricing-item d-flex flex-column">
              <h3>Cao cấp</h3>
              <h4><sup>VND</sup><span style="font-size: 0.8em">499.000</span><span> / tháng</span></h4>
              <ul>
                <li><i class="bi bi-check"></i>Giao hàng quốc tế</li>
                <li><i class="bi bi-check"></i>Theo dõi đơn hàng thời gian thực</li>
                <li><i class="bi bi-check"></i>Hỗ tr 24/7 ưu tiên</li>
                <li><i class="bi bi-check"></i>Bảo hiểm hàng hóa toàn diện</li>
                <li><i class="bi bi-check"></i>Giao hàng siêu tốc</li>
              </ul>
              <a href="order.php" class="buy-btn mt-auto w-auto text-center">Đặt hàng</a>
            </div>
          </div><!-- End Pricing Item -->

        </div>

      </div>

    </section><!-- /Pricing Section -->

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
                <h3>Nguy���n Văn An</h3>
                <h4>Giám đốc điều hành</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Dịch vụ giao hàng chuyên nghiệp, nhanh chóng và đáng tin cậy. Tôi rất hài lòng với chất lượng dịch vụ và sự tận tâm của đội ngũ nhân viên.</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <img src="assets/img/testimonials/testimonials-2.jpg" class="testimonial-img" alt="">
                <h3>Trần Thị Bình</h3>
                <h4>Nhà thiết kế</h4>
                <div class="stars">
                  <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                </div>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>Là một nhà thiết kế, tôi thường xuyên cần gửi các sản phẩm mẫu cho khách hàng. Gerrapp luôn đảm bảo hàng hóa của tôi được vận chuyển an toàn và đúng hẹn.</span>
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
        <!-- <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p> -->
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row justify-content-center">

          <div class="col-lg-10">

            <div class="faq-container">

              <div class="faq-item faq-active" data-aos="fade-up" data-aos-delay="200">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Làm thế nào để đăng ký sử dụng dịch vụ của Gerrapp?</h3>
                <div class="faq-content">
                  <p>Bạn có thể đăng ký sử dụng dịch vụ của Gerrapp bằng cách truy cập trang web của chúng tôi và điền vào biểu mẫu đăng ký. Sau khi hoàn tất đăng ký, đội ngũ của chúng tôi sẽ liên hệ với bạn để hướng dẫn chi tiết.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Chi phí giao hàng được tính như thế nào?</h3>
                <div class="faq-content">
                  <p>Chi phí giao hàng được tính dựa trên khoảng cách vận chuyển, trọng lượng và kích thước của hàng hóa. Chúng tôi cam kết mức giá cạnh tranh và minh bạch, bạn có thể dễ dàng tính toán chi phí thông qua công cụ ước tính trên website.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item" data-aos="fade-up" data-aos-delay="400">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Thời gian giao hàng trung bình là bao lâu?</h3>
                <div class="faq-content">
                  <p>Thời gian giao hàng phụ thuộc vào khoảng cách và loại dịch vụ bạn chọn. Với giao hàng nội thành, thường mất 2-4 giờ. Giao hàng liên tỉnh có thể mất 1-3 ngày tùy khu vực. Chúng tôi luôn cố gắng giao hàng nhanh nhất có thể.</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div><!-- End Faq item-->

              <div class="faq-item" data-aos="fade-up" data-aos-delay="500">
                <i class="faq-icon bi bi-question-circle"></i>
                <h3>Làm sao để theo dõi đơn hàng của tôi?</h3>
                <div class="faq-content">
                  <p>Bạn có thể theo dõi đơn hàng của mình thông qua mã đơn hàng được cung cấp. Truy cập vào trang web của chúng tôi, nhập mã đơn hàng vào công cụ theo dõi để biết vị trí và trạng thái hiện tại của đơn hàng.</p>
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

  </main>

<?php include 'includes/footer.php'; ?> 