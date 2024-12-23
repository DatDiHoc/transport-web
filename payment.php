<?php 
if (!isset($_GET['id'])) {
    header('Location: profile.php');
    exit;
}

$pageTitle = 'Thanh toán - Gerrapp';
$currentPage = 'payment';
$bodyClass = 'payment-page';

// Define page-specific script files
$pageScripts = [
    'assets/vendor/php-email-form/validate.js'
];

// Define page-specific inline scripts
$inlineScripts = [
    <<<'EOT'
    document.addEventListener('DOMContentLoaded', async function() {
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('id');
        if (!orderId) {
            window.location.href = 'profile.php';
            return;
        }

        try {
            const response = await fetch(`/Gerrapp/includes/get_order_details.php?id=${orderId}`, {
                credentials: 'include'
            });
            const data = await response.json();
            
            if (data.success) {
                // Update order details
                document.getElementById('order-id').textContent = data.order.id;
                document.getElementById('total-amount').textContent = 
                    new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' })
                    .format(data.order.total_amount);
                
                // Set payment method display
                let paymentMethodText;
                switch(data.order.payment_method) {
                    case 'banking':
                        paymentMethodText = 'Chuyển khoản ngân hàng';
                        document.getElementById('bank-transfer-info').style.display = 'block';
                        document.getElementById('transfer-order-id').textContent = data.order.id;
                        break;
                    case 'cod':
                        paymentMethodText = 'Thanh toán khi nhận hàng';
                        break;
                    case 'momo':
                        paymentMethodText = 'Ví MoMo';
                        document.getElementById('momo-qr').style.display = 'block';
                        document.getElementById('momo-order-id').textContent = data.order.id;
                        break;
                }
                document.getElementById('payment-method').textContent = paymentMethodText;
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Failed to load order details:', error);
            document.getElementById('error-message').style.display = 'block';
            document.getElementById('error-message').querySelector('span').textContent = error.message;
        }

        // Start checking payment status
        checkPaymentStatus();
    });
    
    // Your existing checkPaymentStatus function here
    function checkPaymentStatus() {
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('id');
        if (!orderId) return;

        fetch(`/Gerrapp/includes/check-payment-status.php?id=${orderId}`, {
            credentials: 'include'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Hide all status messages first
                document.getElementById('status-pending').style.display = 'none';
                document.getElementById('status-completed').style.display = 'none';
                document.getElementById('status-failed').style.display = 'none';

                if (data.success) {
                    // Show relevant status message
                    switch(data.status) {
                        case 'unpaid':
                            document.getElementById('status-pending').style.display = 'block';
                            // Check again in 30 seconds
                            setTimeout(checkPaymentStatus, 30000);
                            break;
                        case 'paid':
                            document.getElementById('status-completed').style.display = 'block';
                            break;
                        case 'cancel':
                            document.getElementById('status-failed').style.display = 'block';
                            break;
                    }
                } else {
                    console.error('Error checking payment status:', data.error);
                }
            })
            .catch(error => {
                console.error('Failed to check payment status:', error);
            });
    }

    // Start checking payment status when page loads
    document.addEventListener('DOMContentLoaded', checkPaymentStatus);
    EOT
];

include 'includes/header.php';
?>

<main id="main">
    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.jpg);">
      <div class="container position-relative">
        <h1>Thanh toán</h1>
        <p>Hoàn tất đơn hàng của bạn</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Trang chủ</a></li>
            <li><a href="order.php">Đặt hàng</a></li>
            <li class="current">Thanh toán</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Payment Section -->
    <section class="payment-section">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-8">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title text-center mb-2">Thông tin thanh toán</h5>
                
                <!-- Payment Status -->
                <div class="payment-status mb-3">
                  <div id="status-pending" class="alert alert-warning" style="display: none;">
                    <i class="bi bi-clock"></i> Chưa thanh toán
                  </div>
                  <div id="status-completed" class="alert alert-success" style="display: none;">
                    <i class="bi bi-check-circle"></i> Đã thanh toán
                  </div>
                  <div id="status-failed" class="alert alert-danger" style="display: none;">
                    <i class="bi bi-x-circle"></i> Đã hủy
                  </div>
                </div>

                <!-- Add this after the payment-status div -->
                <div id="error-message" class="alert alert-danger" style="display: none;">
                    <i class="bi bi-exclamation-circle"></i> <span></span>
                </div>

                <!-- Order Summary -->
                <div class="order-summary mb-3">
                  <h6 class="mb-1">Thông tin đơn hàng #<span id="order-id"></span></h6>
                  <div class="table-responsive">
                    <table class="table table-sm mb-1">
                      <tr>
                        <td><strong>Tổng tiền:</strong></td>
                        <td class="text-end"><span id="total-amount"></span></td>
                      </tr>
                      <tr>
                        <td><strong>Phương thức thanh toán:</strong></td>
                        <td class="text-end"><span id="payment-method"></span></td>
                      </tr>
                    </table>
                  </div>
                </div>

                <!-- Payment Instructions -->
                <!-- <div class="payment-instructions">
                  <h6 class="mb-1">Hướng dẫn thanh toán</h6>
                  <div id="bank-transfer-info" style="display: none;">
                    <p class="mb-1">Vui lòng chuyển khoản theo thông tin sau:</p>
                    <ul class="list-unstyled mb-0">
                      <li><strong>Ngân hàng:</strong> VietcomBank</li>
                      <li><strong>Số tài khoản:</strong> 1234567890</li>
                      <li><strong>Chủ tài khoản:</strong> CÔNG TY TNHH GERRAPP</li>
                      <li><strong>Nội dung:</strong> GR<span id="transfer-order-id"></span></li>
                    </ul>
                  </div>
                  <div id="cod-info" style="display: none;">
                    <p class="mb-0">Bạn sẽ thanh toán khi nhận hàng.</p>
                  </div>
                  <div id="momo-info" style="display: none;">
                    <p class="mb-1">Vui lòng thanh toán qua ví MoMo theo thông tin:</p>
                    <ul class="list-unstyled mb-0">
                      <li><strong>Số điện thoại:</strong> 0819691312</li>
                      <li><strong>Tên tài khoản:</strong> CÔNG TY TNHH GERRAPP</li>
                      <li><strong>Nội dung:</strong> GR<span id="momo-order-id"></span></li>
                    </ul>
                  </div>
                </div> -->
                <div class="payment-qr mb-3">
                    <!-- Banking Info -->
                    <div id="bank-transfer-info" style="display: none;">
                        <h6 class="text-center mb-3">Thông tin chuyển khoản</h6>
                        <div class="text-center">
                            <img src="assets/img/banking-qr.jpg" alt="Banking QR Code" class="img-fluid mb-3" style="max-width: 300px; width: 300px; height: 300px; object-fit: contain;">
                            <div class="bank-info">
                                <p><strong>Số tài khoản:</strong> 123456789</p>
                                <p><strong>Tên tài khoản:</strong> GERRAPP COMPANY</p>
                                <p><strong>Ngân hàng:</strong> BIDV</p>
                                <p><strong>Nội dung CK:</strong> GERRAPP<span id="transfer-order-id"></span></p>
                            </div>
                        </div>
                    </div>

                    <!-- MoMo QR -->
                    <div id="momo-qr" style="display: none;">
                        <h6 class="text-center mb-3">Quét mã QR để thanh toán qua MoMo</h6>
                        <div class="text-center">
                            <img src="assets/img/momo-qr.jpg" 
                                 alt="MoMo QR Code" 
                                 class="img-fluid mb-3" 
                                 style="max-width: 300px; width: 300px; height: 300px; object-fit: contain;">
                            <div class="momo-info">
                                <p><strong>Số điện thoại:</strong> 0123456789</p>
                                <p><strong>Tên tài khoản:</strong> GERRAPP</p>
                                <p><strong>Nội dung:</strong> GERRAPP<span id="momo-order-id"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="text-center mt-2">
                  <a href="profile.php" class="btn" style="background-color: var(--accent-color); color: var(--contrast-color);">Xem đơn hàng của tôi</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

<?php include 'includes/footer.php'; ?> 