<?php
// Determine if we're in admin directory
$isAdminPath = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdminPath ? '../' : '';
?>

  <footer id="footer" class="footer dark-background">
    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="<?php echo $basePath; ?>index.php" class="logo d-flex align-items-center">
            <span class="sitename">Gerrapp</span>
          </a>
          <p>Gerrapp là đối tác đáng tin cậy trong lĩnh vực giao hàng và logistics. Chúng tôi cam kết mang đến dịch vụ chất lượng cao với giá cả cạnh tranh, đáp ứng mọi nhu cầu vận chuyển của khách hàng.</p>
          <div class="social-links d-flex mt-4">
            <a href="<?php echo $basePath; ?>not-found.php"><i class="bi bi-twitter-x"></i></a>
            <a href="#"><i class="bi bi-facebook"></i></a>
            <a href="<?php echo $basePath; ?>not-found.php"><i class="bi bi-instagram"></i></a>
            <a href="<?php echo $basePath; ?>not-found.php"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Đường dẫn</h4>
          <ul>
            <li><a href="<?php echo $basePath; ?>index.php">Trang chủ</a></li>
            <li><a href="<?php echo $basePath; ?>about.php">Về chúng tôi</a></li>
            <li><a href="<?php echo $basePath; ?>services.php">Dịch vụ</a></li>
            <li><a href="<?php echo $basePath; ?>terms.php">Điều khoản dịch vụ</a></li>
            <li><a href="<?php echo $basePath; ?>privacy.php">Chính sách bảo mật</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Dịch vụ</h4>
          <ul>
            <li><a href="#">Giao hàng nhanh</a></li>
            <li><a href="#">Giao hàng tiết kiệm</a></li>
            <li><a href="#">Giao hàng quốc tế</a></li>
            <li><a href="#">Dịch vụ kho bãi</a></li>
            <li><a href="#">Dịch vụ đóng gói</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
          <h4>Liên hệ</h4>
          <p>25 P. Nguyễn Văn Lộc</p>
          <p>Mộ Lao, Hà Đông, Hà Nội 100000, </p>
          <p>Việt Nam</p>
          <p class="mt-4"><strong>Điện thoại:</strong> <span>+84 081 969 1312</span></p>
          <p><strong>Email:</strong> <span>ttdat171203@gmail.com</span></p>
        </div>
      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Bản quyền</span> <strong class="px-1 sitename">Gerrapp</strong> <span>Đã đăng ký Bản quyền</span></p>
      <div class="credits">
        Thiết kế bởi <a href="https://bootstrapmade.com/">BootstrapMade</a>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="<?php echo $basePath; ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo $basePath; ?>assets/vendor/aos/aos.js"></script>
  <script src="<?php echo $basePath; ?>assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="<?php echo $basePath; ?>assets/vendor/swiper/swiper-bundle.min.js"></script>

  <!-- Main JS File -->
  <script src="<?php echo $basePath; ?>assets/js/main.js"></script>

  <!-- Chart.js for admin pages -->
  <?php if (strpos($currentPage, 'admin-') !== false): ?>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <?php endif; ?>

  <!-- Common Session Check Script -->
  <script>
    document.addEventListener('DOMContentLoaded', async function() {
        try {
            const response = await fetch('<?php echo $basePath; ?>includes/check_session.php', {
                method: 'GET',
                credentials: 'include'
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            
            // Get elements - add null checks
            const accountLink = document.querySelector('.dropdown > a');
            if (accountLink) {
                const dropdownIcon = accountLink.querySelector('.toggle-dropdown');
                const loggedOutItems = document.querySelectorAll('.logged-out');
                
                if (data.logged_in) {
                    // User is logged in
                    accountLink.href = '<?php echo $basePath; ?>profile.php';
                    if (dropdownIcon) {
                        dropdownIcon.style.display = 'none';
                    }
                    loggedOutItems.forEach(item => item.style.display = 'none');
                } else {
                    // User is not logged in
                    accountLink.href = '#';
                    if (dropdownIcon) {
                        dropdownIcon.style.display = 'inline-block';
                    }
                    loggedOutItems.forEach(item => item.style.display = 'block');
                }
            }
        } catch (error) {
            console.error('Kiểm tra phiên đăng nhập thất bại:', error);
            // Only try to show logged-out items if they exist
            const loggedOutItems = document.querySelectorAll('.logged-out');
            if (loggedOutItems.length > 0) {
                loggedOutItems.forEach(item => item.style.display = 'block');
            }
        }
    });
  </script>

  <!-- Page Specific Scripts -->
  <?php 
  if (isset($pageScripts) && is_array($pageScripts)) {
      foreach ($pageScripts as $script): ?>
          <script src="<?php echo $basePath . $script; ?>"></script>
      <?php endforeach;
  }
  ?>

  <!-- Page Specific Inline Scripts -->
  <?php 
  if (isset($inlineScripts) && is_array($inlineScripts)) {
      foreach ($inlineScripts as $script): ?>
          <script><?php echo $script; ?></script>
      <?php endforeach;
  }
  ?>

</body>
</html> 