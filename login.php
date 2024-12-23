<?php 
$pageTitle = 'Đăng nhập - Gerrapp';
$currentPage = 'login';
$bodyClass = 'login-page';

// Define page-specific script files
$pageScripts = [
    'assets/vendor/php-email-form/validate.js',
    'assets/vendor/purecounter/purecounter_vanilla.js'
];

// Define page-specific inline scripts
$inlineScripts = [
    <<<EOT
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.querySelector('.php-email-form');
        if (!loginForm) {
            console.error('Login form not found');
            return;
        }

        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const form = this;
            const errorDiv = form.querySelector('.error-message');
            const successDiv = form.querySelector('.sent-message');
            const loadingModal = document.getElementById('loadingModal');
            const loadingMessageEl = loadingModal.querySelector('.loading-message');
            const submitButton = form.querySelector('button[type="submit"]');
            
            try {
                // Clear previous messages
                errorDiv.style.display = 'none';
                successDiv.style.display = 'none';
                
                // Disable submit button and show loading modal
                submitButton.disabled = true;
                loadingModal.classList.add('show');
                loadingMessageEl.textContent = 'Đang đăng nhập...';

                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || 'Có lỗi xảy ra khi đăng nhập');
                }

                // Log the response data
                console.log('Login response:', result);
                
                // Verify session data
                const sessionCheck = await fetch('includes/check_session.php', {
                    credentials: 'include'
                });
                const sessionData = await sessionCheck.json();
                console.log('Session data:', sessionData);
                
                // Show success message
                loadingMessageEl.textContent = 'Đăng nhập thành công!';
                
                // Redirect after verification
                setTimeout(() => {
                    window.location.href = result.redirect || 'index.php';
                }, 800);

            } catch (error) {
                loadingModal.classList.remove('show');
                submitButton.disabled = false;
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
                console.error('Login error:', error);
            }
        });

        // Optional: Close loading modal when clicking outside
        const loadingModal = document.getElementById('loadingModal');
        if (loadingModal) {
            loadingModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                    // Re-enable submit button if it was disabled
                    const submitButton = document.querySelector('.php-email-form button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = false;
                    }
                }
            });
        }
    });
    EOT
];

// Add custom styles
$customStyles = <<<EOT
    <style>
    .login-form {
      max-width: 400px;
      margin: 120px auto;
      padding: 30px;
      background: #fff;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
    }
    .form-control {
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 15px;
    }
    .btn-login {
        color: var(--contrast-color);
        background: var(--accent-color);
        border: 0;
        padding: 10px 30px;
        transition: 0.4s;
        border-radius: 4px;
    }
    .btn-login:hover {
        background: color-mix(in srgb, var(--accent-color), transparent 20%);
        color: var(--contrast-color);
    }
    .loading-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }

    .loading-modal.show {
        display: flex;
        opacity: 1;
    }

    .loading-content {
    background: white;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }

    .loading-spinner {
    width: 40px;
    height: 40px;
    margin: 20px auto;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--color-primary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    }

    @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
    }
    </style>
EOT;

include 'includes/header.php';
?>

<!-- Page Title -->
<div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.jpg);">
        <div class="container position-relative">
          <h1>Đăng nhập</h1>
          <p>Đăng nhập để truy cập tài khoản và sử dụng các dịch vụ của chúng tôi.</p>
          <nav class="breadcrumbs">
            <ol>
              <li><a href="index.php">Trang chủ</a></li>
              <li class="current">Đăng nhập</li>
            </ol>
          </nav>
        </div>
      </div><!-- End Page Title -->

    <main id="main">
    <div class="container" data-aos="fade-up">
      <div class="login-form">
        <div class="section-title text-center">
          <h3><strong>Đăng nhập</strong></h3>
          <p>Đăng nhập để sử dụng dịch vụ của chúng tôi</p>
        </div>

        <form action="forms/process_login.php" method="post" class="php-email-form">
          <div class="form-group mb-3">
            <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập hoặc Email" required>
          </div>
          <div class="form-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
          </div>
          <div class="form-group mb-3">
            <div class="form-check">
              <input type="checkbox" name="remember" class="form-check-input" id="remember">
              <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
            </div>
          </div>
          <div class="form-group mb-3 text-center">
            <button type="submit" class="btn btn-login">Đăng nhập</button>
          </div>
          <div class="text-center">
            <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
            <p><a href="forgot-password.php">Quên mật khẩu?</a></p>
          </div>
          <div class="my-3">
            <div class="error-message"></div>
            <div class="sent-message">Đăng nhập thành công!</div>
          </div>
        </form>
      </div>
    </div>
  </main>

<!-- Loading Modal -->
<div id="loadingModal" class="loading-modal">
    <div class="loading-content">
      <div class="loading-spinner"></div>
      <h4>Đang xử lý...</h4>
      <p class="loading-message">Vui lòng đợi trong giây lát</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 