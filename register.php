<?php 
$pageTitle = 'Đăng ký - Gerrapp';
$currentPage = 'register';
$bodyClass = 'register-page';

// Define page-specific script files
$pageScripts = [
    'assets/vendor/php-email-form/validate.js',
    'assets/vendor/purecounter/purecounter_vanilla.js'
];

// Define custom styles
$customStyles = <<<EOT
    <style>
    .register-form {
        max-width: 600px;
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
    .btn-register {
        color: var(--contrast-color);
        background: var(--accent-color);
        border: 0;
        padding: 10px 30px;
        transition: 0.4s;
        border-radius: 4px;
        width: 100%;
    }
    .btn-register:hover {
        background: color-mix(in srgb, var(--accent-color), transparent 20%);
        color: var(--contrast-color);
    }
    .form-label {
        font-weight: 500;
        margin-bottom: 8px;
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
        backdrop-filter: blur(3px);
    }
    .loading-content {
        background: white;
        padding: 25px 40px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }
    .loading-spinner {
        width: 40px;
        height: 40px;
        margin: 15px auto;
        border: 3px solid #f3f3f3;
        border-top: 3px solid var(--accent-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    </style>
EOT;

// Define page-specific inline scripts
$inlineScripts = [
    <<<EOT
    document.querySelector('.php-email-form').addEventListener('submit', async function(e) {
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
            
            // Show loading modal
            submitButton.disabled = true;
            loadingModal.classList.add('show');
            loadingMessageEl.textContent = 'Đang xử lý đăng ký...';

            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                credentials: 'include'
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Có lỗi xảy ra khi đăng ký');
            }

            // Show success message
            loadingMessageEl.textContent = 'Đăng ký thành công!';
            successDiv.textContent = result.message;
            successDiv.style.display = 'block';
            
            // Redirect after short delay
            setTimeout(() => {
                window.location.href = result.redirect || 'login.php';
            }, 1500);

        } catch (error) {
            loadingModal.classList.remove('show');
            submitButton.disabled = false;
            errorDiv.textContent = error.message;
            errorDiv.style.display = 'block';
            console.error('Registration error:', error);
        }
    });
    EOT
];

include 'includes/header.php';
?>

<main id="main">
    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.jpg);">
        <div class="container position-relative">
          <h1>Đăng ký</h1>
          <p>Tạo tài khoản mới để trải nghiệm dịch vụ của chúng tôi một cách thuận tiện và nhanh chóng.</p>
          <nav class="breadcrumbs">
            <ol>
              <li><a href="index.html">Trang chủ</a></li>
              <li class="current">Đăng ký</li>
            </ol>
          </nav>
        </div>
      </div><!-- End Page Title -->

    <div class="container" data-aos="fade-up">
      <div class="register-form">
        <div class="section-title text-center">
          <h3><strong>Đăng ký tài khoản</strong></h3>
          <p>Tạo tài khoản để sử dụng dịch vụ của chúng tôi</p>
        </div>

        <form action="forms/process_register.php" method="post" class="php-email-form">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" name="username" id="username" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="full_name" class="form-label">Họ và tên</label>
                <input type="text" name="full_name" id="full_name" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="tel" name="phone" id="phone" class="form-control">
              </div>
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="address" class="form-label">Địa chỉ</label>
            <textarea name="address" id="address" class="form-control" rows="3"></textarea>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" name="password" id="password" class="form-control" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
              </div>
            </div>
          </div>

          <div class="form-group mb-4">
            <div class="form-check">
              <input type="checkbox" name="terms" class="form-check-input" id="terms" required>
              <label class="form-check-label" for="terms">
                Tôi đồng ý với <a href="#">điều khoản sử dụng</a> và <a href="#">chính sách bảo mật</a>
              </label>
            </div>
          </div>

          <div class="form-group mb-4 text-center">
            <button type="submit" class="btn btn-register">Đăng ký</button>
          </div>

          <div class="text-center">
            <p>Đã có tài khoản? <a href="login.html">Đăng nhập</a></p>
          </div>

          <div class="my-3">
            <div class="error-message"></div>
            <div class="sent-message">Đăng ký thành công!</div>
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
</div> 

<?php include 'includes/footer.php'; ?> 