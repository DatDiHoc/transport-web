<?php
session_start();
define('DEBUG_MODE', true);

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$pageTitle = 'Đặt hàng - Gerrapp';
$currentPage = 'order';
$bodyClass = 'order-page';

// Define page-specific styles
$customStyles = <<<EOT
<style>
    .input-group input[type="number"] {
        width: 33.33%;
        border-radius: 0;
    }
    .input-group input[type="number"]:first-child {
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    .input-group input[type="number"]:last-child {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }
    .loading {
        display: none;
    }
    .loading.active {
        display: block;
    }
    button[type="submit"]:disabled {
        cursor: not-allowed;
        opacity: 0.7;
    }
</style>
EOT;

// Define page-specific script files
$pageScripts = [
    'assets/vendor/php-email-form/validate.js',
    'assets/vendor/purecounter/purecounter_vanilla.js'
];

// Define page-specific inline scripts
$inlineScripts = [<<<'EOT'
'use strict';

console.log('Order form script loaded');

// Make functions available to event handlers
window.OrderForm = {
    addProduct: function() {
        const productRow = `
            <tr>
                <td><input type="text" name="product_names[]" class="form-control" required maxlength="255"></td>
                <td><input type="number" name="quantities[]" class="form-control" value="1" min="1" max="999"></td>
                <td>
                    <div class="input-group">
                        <input type="number" name="length[]" class="form-control" placeholder="D" min="0" max="999">
                        <input type="number" name="width[]" class="form-control" placeholder="R" min="0" max="999">
                        <input type="number" name="height[]" class="form-control" placeholder="C" min="0" max="999">
                    </div>
                </td>
                <td><input type="number" name="weight[]" class="form-control" step="0.1" min="0" max="999"></td>
                <td>
                    <input type="hidden" name="shipping_fees[]" class="shipping-fee-raw">
                    <input type="text" class="form-control shipping-fee-display" disabled>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-product">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('productList').insertAdjacentHTML('beforeend', productRow);
        attachEventListeners();
    },

    removeProduct: function(button) {
        button.closest('tr').remove();
        updateTotalAmount();
    },

    calculateShippingFee: function(input) {
        const row = input.closest('tr');
        const quantity = parseFloat(row.querySelector('[name="quantities[]"]').value) || 0;
        const length = parseFloat(row.querySelector('[name="length[]"]').value) || 0;
        const width = parseFloat(row.querySelector('[name="width[]"]').value) || 0;
        const height = parseFloat(row.querySelector('[name="height[]"]').value) || 0;
        const weight = parseFloat(row.querySelector('[name="weight[]"]').value) || 0;
        
        const volumetricWeight = (length * width * height) / 5000;
        const chargeableWeight = Math.max(weight, volumetricWeight);
        const baseFee = 20000; // Base fee in VND
        const fee = Math.round(baseFee * chargeableWeight * quantity);
        
        // Format with commas for display
        const formatNumber = (num) => {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        };
        
        // Update both hidden and display inputs
        row.querySelector('.shipping-fee-raw').value = fee;
        row.querySelector('.shipping-fee-display').value = formatNumber(fee);
        updateTotalAmount();
    }
};

function updateTotalAmount() {
    const formatNumber = (num) => {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };

    const fees = [...document.getElementsByClassName('shipping-fee-raw')]
        .map(input => parseFloat(input.value) || 0);
    const total = fees.reduce((sum, fee) => sum + fee, 0);
    document.getElementById('total_amount').textContent = formatNumber(total);
}

function attachEventListeners() {
    const rows = document.getElementById('productList').getElementsByTagName('tr');
    const lastRow = rows[rows.length - 1];
    
    if (lastRow) {
        lastRow.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('change', () => OrderForm.calculateShippingFee(input));
        });

        const removeBtn = lastRow.querySelector('.remove-product');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                OrderForm.removeProduct(this);
            });
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    const addProductBtn = document.getElementById('addProductBtn');
    if (addProductBtn) {
        addProductBtn.addEventListener('click', OrderForm.addProduct);
    }

    // Add first product row (only one)
    OrderForm.addProduct();

    // Remove any additional rows that might exist
    const productList = document.getElementById('productList');
    while (productList.children.length > 1) {
        productList.removeChild(productList.lastChild);
    }

    // Initialize form submission handler
    const form = document.querySelector('.php-email-form');
    if (form) {
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Silently prevent double submission
            if (isSubmitting) {
                console.log('Preventing duplicate submission');
                return;
            }
            
            isSubmitting = true;
            let formData = new FormData(this);
            let submitButton = this.querySelector('button[type="submit"]');
            let loadingDiv = this.querySelector('.loading');
            let errorDiv = this.querySelector('.error-message');
            let successDiv = this.querySelector('.sent-message');
            
            // Disable submit button and show loading state
            submitButton.disabled = true;
            loadingDiv.style.display = 'block';
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.style.display = 'none';
                
                if (data.success) {
                    successDiv.innerHTML = `Đặt hàng thành công!`;
                    successDiv.style.display = 'block';
                    form.reset();
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    // Only show error if it's not a duplicate submission error
                    if (!data.isDuplicate) {
                        throw new Error(data.message || 'Có lỗi xảy ra');
                    }
                }
            })
            .catch(error => {
                loadingDiv.style.display = 'none';
                errorDiv.textContent = error.message;
                errorDiv.style.display = 'block';
            })
            .finally(() => {
                setTimeout(() => {
                    isSubmitting = false;
                    submitButton.disabled = false;
                }, 2000); // Add a 2-second delay before allowing resubmission
            });
        });

        // Also disable the submit button on click
        document.querySelector('button[type="submit"]').addEventListener('click', function(e) {
            if (isSubmitting) {
                e.preventDefault();
            }
        });
    }
});

function validateForm() {
    const productRows = document.querySelectorAll('#productList tr');
    if (productRows.length === 0) {
        alert('Vui lòng thêm ít nhất một sản phẩm');
        return false;
    }
    return true;
}
EOT
];

include 'includes/header.php';
?>

<main class="main">

    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.jpg);">
      <div class="container position-relative">
        <h1>Đặt hàng</h1>
        <p>Điền thông tin đơn hàng của bạn để chúng tôi có thể phục vụ bạn tốt nhất.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="index.php">Trang chủ</a></li>
            <li class="current">Đặt hàng</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Starter Section Section -->
    <section id="order" class="order section py-5">
      <div class="container">
        <div class="order-form">
          <h2 class="text-center mb-4">Thông Tin Đơn Hàng</h2>
          <form action="forms/process_order.php" method="post" class="php-email-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <!-- Add after form opening tag -->
            <noscript>
                <div class="alert alert-warning">
                    Vui lòng bật JavaScript để sử dụng tính năng đặt hàng.
                </div>
            </noscript>

            <!-- Add validation feedback -->
            <div class="invalid-feedback">
                Vui lòng điền đầy đủ thông tin này
            </div>
            <!-- Shipping Information -->
            <div class="row">
              <div class="col-md-6">
                <h4>Thông tin giao hàng</h4>
                <div class="form-group mb-3">
                  <input type="text" name="shipping_name" class="form-control" placeholder="Tên người nhận" required>
                </div>
                <div class="form-group mb-3">
                  <input type="tel" 
                         name="shipping_phone" 
                         class="form-control" 
                         placeholder="Số điện thoại" 
                         required 
                         pattern="(84|0[35789])[0-9]{8}"
                         title="Vui lòng nhập số điện thoại hợp lệ (VD: 0912345678)">
                </div>
                <div class="form-group mb-3">
                  <textarea name="shipping_address" class="form-control" rows="3" placeholder="Địa chỉ giao hàng" required></textarea>
                </div>
              </div>

              <!-- Order Details -->
              <div class="col-md-6">
                <h4>Chi tiết đơn hàng</h4>
                <div class="form-group mb-3">
                  <select name="payment_method" class="form-control" required>
                    <option value="">Chọn phương thức thanh toán</option>
                    <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                    <option value="banking">Chuyển khoản ngân hàng</option>
                    <option value="momo">Ví MoMo</option>
                  </select>
                </div>
                <div class="form-group mb-3">
                  <textarea name="notes" class="form-control" rows="3" placeholder="Ghi chú đơn hàng"></textarea>
                </div>
              </div>
            </div>

            <!-- Products Section -->
            <div class="row mt-4">
              <div class="col-12">
                <h4>Sản phẩm</h4>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Kích thước (cm)</th>
                        <th>Khối lượng (kg)</th>
                        <th>Phí vận chuyển</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody id="productList">
                      <!-- Products will be added here dynamically - leave empty -->
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="4" class="text-end"><strong>Tổng phí vận chuyển:</strong></td>
                        <td colspan="2"><span id="total_amount">0</span> VNĐ</td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>

            <!-- Add Product Button -->
            <div class="row mt-3">
              <div class="col-12">
                <button type="button" class="btn btn-secondary" id="addProductBtn">
                  <i class="bi bi-plus"></i> Thêm sản phẩm
                </button>
              </div>
            </div>

            <div class="my-3">
              <div class="loading">Đang xử lý...</div>
              <div class="error-message"></div>
              <div class="sent-message">Đơn hàng của bạn đã được đặt thành công!</div>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary">Đặt hàng</button>
            </div>
          </form>
        </div>
      </div>
    </section>

  </main>

<?php include 'includes/footer.php'; ?> 