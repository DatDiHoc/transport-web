<?php
// Start session at the very beginning of the file, before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check admin status once at the top
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';

// Determine if we're in admin directory
$isAdminPath = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdminPath ? '../' : '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo $pageTitle ?? 'Gerrapp Bootstrap Template'; ?></title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="<?php echo $basePath; ?>assets/img/favicon.png" rel="icon">
  <link href="<?php echo $basePath; ?>assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo $basePath; ?>assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo $basePath; ?>assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo $basePath; ?>assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo $basePath; ?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="<?php echo $basePath; ?>assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?php echo $basePath; ?>assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="<?php echo $basePath; ?>assets/css/main.css" rel="stylesheet">

  <!-- Custom Page Styles -->
  <?php if (isset($customStyles)) echo $customStyles; ?>

  <!-- Page Scripts -->
  <?php if (!empty($pageScripts)): ?>
    <?php foreach ($pageScripts as $script): ?>
    <script src="<?php echo $basePath . $script; ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>

  <?php if (!empty($inlineScripts)): ?>
    <script>
        <?php 
        if (is_array($inlineScripts)) {
            echo implode("\n", $inlineScripts);
        } else {
            echo $inlineScripts;
        }
        ?>
    </script>
  <?php endif; ?>

  <!-- Admin Dashboard Styles -->
  <style>
      .navmenu .dropdown ul a i {
          margin-right: 10px;
          font-size: 16px;
          line-height: 0;
          color: var(--accent-color);
      }

      .navmenu .dropdown ul a:hover i {
          color: var(--contrast-color);
      }

      .navmenu > ul > li > a.active {
          color: var(--accent-color);
      }

      .navmenu .dropdown ul a.active {
          color: var(--accent-color);
          background: rgba(var(--accent-color-rgb), 0.1);
      }

      .navmenu .dropdown ul a.active i {
          color: var(--accent-color);
      }
  </style>

  <?php if (isset($extraHeadContent)) echo $extraHeadContent; ?>
</head>

<body class="<?php echo $bodyClass ?? ''; ?>">
  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="<?php echo $basePath; ?>index.php" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">Gerrapp</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="<?php echo $basePath; ?>index.php" <?php echo $currentPage === 'home' ? 'class="active"' : ''; ?>>Trang chủ</a></li>
          <li><a href="<?php echo $basePath; ?>about.php" <?php echo $currentPage === 'about' ? 'class="active"' : ''; ?>>Về chúng tôi</a></li>
          <li><a href="<?php echo $basePath; ?>services.php" <?php echo $currentPage === 'services' ? 'class="active"' : ''; ?>>Dịch vụ</a></li>
          <li><a href="<?php echo $basePath; ?>pricing.php" <?php echo $currentPage === 'pricing' ? 'class="active"' : ''; ?>>Giá cả</a></li>
          <li><a href="<?php echo $basePath; ?>contact.php" <?php echo $currentPage === 'contact' ? 'class="active"' : ''; ?>>Liên hệ</a></li>
          
          <?php if ($isAdmin): ?>
              <li><a href="<?php echo $basePath; ?>admin/dashboard.php" <?php echo $currentPage === 'admin-dashboard' ? 'class="active"' : ''; ?>>Quản trị</a></li>
          <?php endif; ?>
          
          <li class="dropdown">
              <a href="#" <?php echo in_array($currentPage, ['profile', 'login', 'register']) ? 'class="active"' : ''; ?>>
                  <span>Tài khoản</span> 
                  <i class="bi bi-chevron-down toggle-dropdown"></i>
              </a>
              <ul>
                  <?php if (!isset($_SESSION['user_id'])): ?>
                      <li>
                          <a href="<?php echo $basePath; ?>login.php">
                              <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                          </a>
                      </li>
                      <li>
                          <a href="<?php echo $basePath; ?>register.php">
                              <i class="bi bi-person-plus"></i> Đăng ký
                          </a>
                      </li>
                  <?php endif; ?>
              </ul>
          </li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="<?php echo $basePath; ?>order.php">Đặt hàng</a>

    </div>
  </header>