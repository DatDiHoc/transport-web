<?php 
$pageTitle = 'Giá cả - Gerrapp';
$currentPage = 'pricing';
$bodyClass = 'pricing-page';

// Define page-specific script files
$pageScripts = [
    'assets/vendor/php-email-form/validate.js',
    'assets/vendor/purecounter/purecounter_vanilla.js'
];

// Define page-specific inline scripts
$inlineScripts = [];

include 'includes/header.php';
?>

<main class="main">
    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.jpg);">
      <div class="container position-relative">
        <h1>Gói dịch vụ</h1>
        <p>Chúng tôi cung cấp các gói dịch vụ đa dạng, phù hợp với mọi nhu cầu vận chuyển của quý khách hàng.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.html">Trang chủ</a></li>
            <li class="current">Gói dịch vụ</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

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
</main>

<?php include 'includes/footer.php'; ?> 