<?php 
$pageTitle = 'Tài khoản - Gerrapp';
$currentPage = 'profile';
$bodyClass = 'profile-page';

// Define page-specific script files
$pageScripts = [
    'assets/vendor/php-email-form/validate.js',
    'assets/vendor/purecounter/purecounter_vanilla.js'
];

// Define page-specific inline scripts
$inlineScripts = [
    <<<'EOT'
    // Initialize global state object if it doesn't exist
    if (typeof window.appGlobals === 'undefined') {
        window.appGlobals = {
            orderId: null,
            paymentMethod: null,
            totalAmount: null,
            messageId: null
        };
    }

    // View order details function
    window.viewOrderDetails = async function(orderId) {
        if (!orderId) {
            console.error('Invalid order ID');
            return;
        }

        try {
            const response = await fetch(`includes/get_order_details.php?id=${orderId}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to load order details');
            }

            const order = data.order;
            const items = data.items;
            
            // Use appGlobals instead of globals
            window.appGlobals.orderId = orderId;
            window.appGlobals.paymentMethod = order.payment_method;
            window.appGlobals.totalAmount = order.total_amount;

            // Update order details
            document.getElementById('order-id').textContent = orderId;
            document.getElementById('order-created-at').textContent = order.created_at;
            document.getElementById('order-total-amount').textContent = formatCurrency(order.total_amount);
            document.getElementById('order-payment-method').textContent = order.payment_method;
            document.getElementById('order-payment-status').textContent = getPaymentStatusText(order.payment_status);
            document.getElementById('order-shipping-name').textContent = order.shipping_name;
            document.getElementById('order-shipping-phone').textContent = order.shipping_phone;
            document.getElementById('order-shipping-address').textContent = order.shipping_address;
            document.getElementById('order-notes').textContent = order.notes || 'Không có';

            // Update items table
            const itemsTableBody = document.getElementById('order-items');
            itemsTableBody.innerHTML = items.map(item => `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.length} × ${item.width} × ${item.height}</td>
                    <td>${item.weight}</td>
                    <td>${formatCurrency(item.shipping_fee)}</td>
                </tr>
            `).join('');

            // Show/hide action buttons based on payment status
            toggleActionButtons(order.payment_status);

            // Show the modal
            const orderModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            orderModal.show();
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Có lỗi xảy ra khi tải thông tin đơn hàng');
        }
    };

    // Helper functions
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    }

    function getPaymentStatusText(status) {
        const statusMap = {
            'unpaid': 'Chưa thanh toán',
            'paid': 'Đã thanh toán',
            'cancel': 'Đã hủy'
        };
        return statusMap[status] || status;
    }

    function toggleActionButtons(paymentStatus) {
        const paymentButton = document.getElementById('modal-payment-button');
        const cancelButton = document.getElementById('modal-cancel-button');
        
        if (paymentButton && cancelButton) {
            // Show both buttons only for unpaid orders
            if (paymentStatus === 'unpaid') {
                paymentButton.style.display = 'inline-block';
                cancelButton.style.display = 'inline-block';
            } 
            // Hide both buttons for paid or cancelled orders
            else if (paymentStatus === 'paid' || paymentStatus === 'cancel') {
                paymentButton.style.display = 'none';
                cancelButton.style.display = 'none';
            }
        }
    }

    // Document ready handler
    document.addEventListener('DOMContentLoaded', function() {
        loadProfile();
        loadOrders();
        loadMessages();
        
        // Add logout handler here
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', handleLogout);
        }

        // Add profile form submit handler
        const profileForm = document.getElementById('profile-form');
        if (profileForm) {
            profileForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    const formData = new FormData(this);
                    const response = await fetch('includes/update_profile.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'include',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server returned invalid response format');
                    }
                    
                    const data = await response.json();
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Cập nhật thất bại');
                    }
                    
                    // Show success message
                    alert('Cập nhật thông tin thành công!');
                    
                    // Silently reload profile data without showing additional alerts
                    await loadProfileSilent();
                    
                } catch (error) {
                    console.error('Update profile error:', error);
                    alert('Có lỗi xảy ra khi cập nhật thông tin: ' + (error.message || 'Unknown error'));
                }
            });
        }
    });

    // Separate logout handler function
    async function handleLogout() {
        if (!confirm('Bạn có chắc chắn muốn đăng xuất?')) {
            return;
        }
        
        try {
            const response = await fetch('includes/logout.php', {
                method: 'POST',
                credentials: 'include'
            });
            
            const data = await response.json();
            
            if (data.success) {
                window.location.href = 'login.php';
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            console.error('Logout error:', error);
            alert(error.message || 'Có lỗi xảy ra khi đăng xuất');
        }
    }

    // Load profile data
    async function loadProfile() {
        try {
            const response = await fetch('includes/get_profile.php', {
                credentials: 'include'
            });
            const data = await response.json();

            if (data.success) {
                updateProfileDisplay(data.user);
            } else {
                throw new Error(data.message || 'Failed to load profile');
            }
        } catch (error) {
            console.error('Error loading profile:', error);
            alert('Có lỗi xảy ra khi tải thông tin cá nhân');
        }
    }

    function updateProfileDisplay(user) {
        // Update avatar if exists
        if (user.avatar) {
            document.getElementById('profile-avatar').src = user.avatar;
        }
        
        // Update other profile info
        document.getElementById('profile-username').textContent = user.username;
        document.getElementById('profile-email').textContent = user.email;
        document.getElementById('username').textContent = user.username;
        document.getElementById('email').textContent = user.email;
        document.getElementById('phone').value = user.phone || '';
        document.getElementById('address').value = user.address || '';
    }

    // Load orders data
    async function loadOrders() {
        try {
            const response = await fetch('includes/get_orders.php', {
                credentials: 'include'
            });
            const data = await response.json();

            const ordersTableBody = document.getElementById('orders-table-body');
            
            if (!data.success) {
                ordersTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger">
                            ${data.message || 'Có lỗi xảy ra khi tải đơn hàng'}
                        </td>
                    </tr>`;
                return;
            }

            if (!data.orders || data.orders.length === 0) {
                ordersTableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center">
                            Chưa có đơn hàng nào
                        </td>
                    </tr>`;
                return;
            }

            ordersTableBody.innerHTML = data.orders.map(order => `
                <tr>
                    <td>#${order.id}</td>
                    <td>${order.created_at}</td>
                    <td>${order.shipping_name}</td>
                    <td>${order.shipping_address}</td>
                    <td>${formatCurrency(order.total_amount)}</td>
                    <td>
                        <span class="badge bg-${getPaymentStatusBadge(order.payment_status)}">
                            ${getPaymentStatusText(order.payment_status)}
                        </span>
                    </td>
                    <td>
                        <button 
                            type="button"
                            class="btn btn-sm"
                            onclick="viewOrderDetails(${order.id})"
                            style="background-color: var(--accent-color); color: var(--contrast-color);">
                            Xem
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading orders:', error);
            document.getElementById('orders-table-body').innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Có lỗi xảy ra khi tải đơn hàng
                    </td>
                </tr>`;
        }
    }

    // Add loadMessages function
    async function loadMessages() {
        try {
            const response = await fetch('includes/get_messages.php', {
                credentials: 'include'
            });
            const data = await response.json();

            const messagesTableBody = document.querySelector('#messages table tbody');
            
            if (!data.success) {
                messagesTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            ${data.message || 'Có lỗi xảy ra khi tải tin nhắn'}
                        </td>
                    </tr>`;
                return;
            }

            if (!data.messages || data.messages.length === 0) {
                messagesTableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="text-center">
                            Chưa có tin nhắn nào
                        </td>
                    </tr>`;
                return;
            }

            messagesTableBody.innerHTML = data.messages.map(message => `
                <tr>
                    <td>${message.created_at}</td>
                    <td>${message.subject}</td>
                    <td>
                        <span class="badge bg-${getMessageStatusBadge(message.status)}">
                            ${getMessageStatusText(message.status)}
                        </span>
                    </td>
                    <td>
                        <button 
                            type="button"
                            class="btn btn-sm"
                            onclick="viewMessageDetails(${message.id})"
                            style="background-color: var(--accent-color); color: var(--contrast-color);">
                            Xem
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error loading messages:', error);
            document.querySelector('#messages table tbody').innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-danger">
                        Có lỗi xảy ra khi tải tin nhắn
                    </td>
                </tr>`;
        }
    }

    // Add helper functions for message status
    function getMessageStatusText(status) {
        const statusMap = {
            'new': 'Mới',
            'read': 'Đã đọc',
            'replied': 'Đã trả lời',
            'closed': 'Đã đóng'
        };
        return statusMap[status] || status;
    }

    function getMessageStatusBadge(status) {
        const statusMap = {
            'new': 'warning',
            'read': 'info',
            'replied': 'success',
            'closed': 'secondary'
        };
        return statusMap[status] || 'secondary';
    }

    // Add redirectToPayment function
    function redirectToPayment() {
        if (window.appGlobals.orderId && window.appGlobals.paymentMethod && window.appGlobals.totalAmount) {
            window.location.href = `payment.php?id=${window.appGlobals.orderId}&payment=${window.appGlobals.paymentMethod}&amount=${window.appGlobals.totalAmount}`;
        }
    }

    // Add cancelOrder function
    async function cancelOrder() {
        try {
            if (!window.appGlobals.orderId) {
                throw new Error('ID đơn hàng không hợp lệ');
            }

            if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
                return;
            }

            const response = await fetch('includes/cancel_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    id: window.appGlobals.orderId 
                }),
                credentials: 'include'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Không thể hủy đơn hàng');
            }

            alert('Đơn hàng đã được hủy thành công');
            
            // Close the modal
            const orderModal = bootstrap.Modal.getInstance(document.getElementById('orderDetailsModal'));
            if (orderModal) {
                orderModal.hide();
            }
            
            // Reload orders list
            await loadOrders();

        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Có lỗi xảy ra khi hủy đơn hàng');
        }
    }

    // Update viewMessageDetails function
    async function viewMessageDetails(messageId) {
        if (!messageId) {
            console.error('Invalid message ID');
            return;
        }

        try {
            const response = await fetch(`includes/get_message_detail.php?id=${messageId}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to load message details');
            }

            const message = data.message;
            
            // Update modal content
            document.getElementById('modal-created-at').textContent = message.created_at;
            document.getElementById('modal-subject').textContent = message.subject;
            document.getElementById('modal-message').textContent = message.message;
            
            const replySection = document.querySelector('.reply-section');
            if (message.reply_message) {
                document.getElementById('modal-reply').textContent = message.reply_message;
                document.getElementById('modal-replied-at').textContent = message.replied_at;
                replySection.style.display = 'block';
            } else {
                replySection.style.display = 'none';
            }

            // Show the modal
            const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
            messageModal.show();

            // Update message status if it's new
            if (message.status === 'new') {
                await updateMessageStatus(messageId);
            }
        } catch (error) {
            console.error('Error:', error);
            alert(error.message || 'Có lỗi xảy ra khi tải tin nhắn');
        }
    }

    // Add function to update message status
    async function updateMessageStatus(messageId) {
        try {
            const response = await fetch('includes/update_message_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: messageId }),
                credentials: 'include'
            });

            const data = await response.json();
            if (data.success) {
                // Reload messages to update status
                loadMessages();
            }
        } catch (error) {
            console.error('Error updating message status:', error);
        }
    }

    function getPaymentStatusBadge(status) {
        const statusMap = {
            'unpaid': 'warning',
            'paid': 'success',
            'cancel': 'danger'
        };
        return statusMap[status] || 'secondary';
    }

    // Add a silent version of loadProfile
    async function loadProfileSilent() {
        try {
            const response = await fetch('includes/get_profile.php', {
                credentials: 'include'
            });
            const data = await response.json();

            if (data.success) {
                updateProfileDisplay(data.user);
            } else {
                throw new Error(data.message || 'Failed to load profile');
            }
        } catch (error) {
            console.error('Error loading profile:', error);
            // No alert here
        }
    }

    // Add avatar upload handlers
    document.addEventListener('DOMContentLoaded', function() {
        const avatarImg = document.getElementById('profile-avatar');
        const avatarLabel = avatarImg.parentElement;
        const fileInput = document.getElementById('avatar-upload');
        const overlayText = document.querySelector('.overlay-text');

        // Hover effects
        avatarLabel.addEventListener('mouseenter', () => {
            avatarImg.style.opacity = '0.7';
            overlayText.style.display = 'block';
        });

        avatarLabel.addEventListener('mouseleave', () => {
            avatarImg.style.opacity = '1';
            overlayText.style.display = 'none';
        });

        // Handle file selection
        fileInput.addEventListener('change', async function(e) {
            if (!this.files || !this.files[0]) return;

            const file = this.files[0];
            
            // Validate file type and size
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chọn file hình ảnh');
                return;
            }

            if (file.size > 5 * 1024 * 1024) { // 5MB limit
                alert('Kích thước file không được vượt quá 5MB');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('avatar', file);

                const response = await fetch('includes/update_avatar.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'include'
                });

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Không thể cập nhật ảnh đại diện');
                }

                // Update avatar preview
                avatarImg.src = data.avatar_url + '?t=' + new Date().getTime();
                
                // Show success message
                alert('Cập nhật ảnh đại diện thành công!');

            } catch (error) {
                console.error('Avatar upload error:', error);
                alert(error.message || 'Có lỗi xảy ra khi cập nhật ảnh đại diện');
            }
        });
    });
    EOT
];

include 'includes/header.php';
?>

<main class="main">
<div class="page-title dark-background" data-aos="fade" style="background-image: url(assets/img/page-title-bg.jpg);">
    <div class="container position-relative">
        <h1>Tài khoản</h1>
        <p>Xem và chỉnh sửa trang cá nhân của bạn.</p>
        <nav class="breadcrumbs">
        <ol>
            <li><a href="index.php">Trang chủ</a></li>
            <li class="current">Tài khoản</li>
        </ol>
        </nav>
    </div>
    </div><!-- End Page Title -->

    <!-- Profile Section -->
    <section id="profile" class="profile">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-lg-3">
                    <div class="profile-sidebar">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-center">
                                    <label for="avatar-upload" style="cursor: pointer;">
                                        <img src="assets/img/default-avatar.jpg" 
                                             alt="Avatar" 
                                             class="rounded-circle" 
                                             width="150" 
                                             id="profile-avatar"
                                             style="transition: opacity 0.3s;">
                                        <div class="overlay-text" 
                                             style="position: absolute; 
                                                    top: 50%; 
                                                    left: 50%; 
                                                    transform: translate(-50%, -50%); 
                                                    display: none;">
                                            Thay đổi ảnh
                                        </div>
                                    </label>
                                    <input type="file" 
                                           id="avatar-upload" 
                                           accept="image/*" 
                                           style="display: none;">
                                    <div class="mt-3">
                                        <h4 id="profile-username">Tên người dùng</h4>
                                        <p class="text-muted font-size-sm" id="profile-email">email@example.com</p>
                                    </div>
                                </div>
                                <!-- <hr class="my-4"> -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <!-- Profile Info -->
                    <div id="profile-info" class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--accent-color);">Thông tin cá nhân</h5>
                            <form id="profile-form" class="profile-form">
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0" style="color: var(--g-black);">Tên đăng nhập</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <span id="username"></span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0" style="color: var(--g-black);">Email</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <span id="email"></span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0" style="color: var(--g-black);">Số điện thoại</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0" style="color: var(--g-black);">Địa chỉ</h6>
                                    </div>
                                    <div class="col-sm-9 text-secondary">
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3"></div>
                                    <div class="col-sm-9">
                                        <button type="submit" class="btn" style="background-color: var(--accent-color); color: var(--contrast-color);">
                                            Cập nhật
                                        </button>
                                        <button type="button" id="logout-btn" class="btn btn-danger">
                                            Đăng xuất
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Orders -->
                    <div id="orders" class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--accent-color);">Đơn hàng của tôi</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Mã đơn</th>
                                            <th>Ngày đặt</th>
                                            <th>Người nhận</th>
                                            <th>Địa chỉ</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="orders-table-body">
                                        <!-- Orders will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div id="messages" class="card">
                        <div class="card-body">
                            <h5 class="card-title" style="color: var(--accent-color);">Tin nhắn</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Ngày</th>
                                            <th>Tiêu đề</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody id="messages-table-body">
                                        <!-- Messages will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</main>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Chi tiết đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Thông tin đơn hàng</h6>
                        <p><strong>Mã đơn hàng:</strong> #<span id="order-id"></span></p>
                        <p><strong>Ngày đặt:</strong> <span id="order-created-at"></span></p>
                        <p><strong>Tổng tiền:</strong> <span id="order-total-amount"></span></p>
                        <p><strong>Phương thức thanh toán:</strong> <span id="order-payment-method"></span></p>
                        <p><strong>Trạng thái thanh toán:</strong> <span id="order-payment-status"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Thông tin giao hàng</h6>
                        <p><strong>Người nhận:</strong> <span id="order-shipping-name"></span></p>
                        <p><strong>Số điện thoại:</strong> <span id="order-shipping-phone"></span></p>
                        <p><strong>Địa chỉ:</strong> <span id="order-shipping-address"></span></p>
                        <p><strong>Ghi chú:</strong> <span id="order-notes"></span></p>
                    </div>
                </div>
                <div class="order-items">
                    <h6>Chi tiết hàng hóa</h6>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tên hàng</th>
                                    <th>Số lượng</th>
                                    <th>Kích thước (cm)</th>
                                    <th>Khối lượng (kg)</th>
                                    <th>Phí vận chuyển</th>
                                </tr>
                            </thead>
                            <tbody id="order-items">
                                <!-- Items will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" 
                    id="modal-payment-button" 
                    class="btn" 
                    style="background-color: var(--accent-color); color: var(--contrast-color); display: none;"
                    onclick="redirectToPayment()">
                    Thanh toán
                </button>
                <button type="button" 
                    id="modal-cancel-button" 
                    class="btn btn-danger"
                    style="display: none;"
                    onclick="cancelOrder()">
                    Hủy đơn hàng
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Chi tiết tin nhắn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Ngày gửi:</strong> <span id="modal-created-at"></span></p>
                <p><strong>Tiêu đề:</strong> <span id="modal-subject"></span></p>
                <p><strong>Nội dung:</strong></p>
                <div id="modal-message" class="border p-3 mb-3"></div>
                
                <div class="reply-section" style="display: none;">
                    <hr>
                    <h6>Phản hồi</h6>
                    <p><strong>Thời gian:</strong> <span id="modal-replied-at"></span></p>
                    <div id="modal-reply" class="border p-3"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 