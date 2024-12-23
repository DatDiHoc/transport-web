<?php
$pageTitle = 'Dashboard - Gerrapp';
$currentPage = 'admin-dashboard';
$bodyClass = 'admin-page';

// Start session and check login status at the very beginning
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Define page-specific script files
$pageScripts = [
    // Add any specific scripts if needed
];

// Add any dashboard-specific inline scripts if needed
$inlineScripts = [
    <<<'EOT'
    document.addEventListener('DOMContentLoaded', function() {
        const contactModal = document.getElementById('contactModal');
        const replyForm = document.getElementById('replyForm');
        const contactIdInput = document.getElementById('contactId');
        const contactSubject = document.getElementById('contactSubject');
        const contactMessage = document.getElementById('contactMessage');
        const contactDate = document.getElementById('contactDate');
        const previousReplySection = document.getElementById('previousReplySection');
        const previousReply = document.getElementById('previousReply');
        const previousReplyDate = document.getElementById('previousReplyDate');
        const replyMessage = document.getElementById('replyMessage');
        
        if (!contactModal || !replyForm) {
            console.error('Required elements not found');
            return;
        }

        let bsModal = new bootstrap.Modal(contactModal);
        
        // Add modal cleanup handler
        contactModal.addEventListener('hidden.bs.modal', function() {
            replyForm.reset();
            previousReplySection.style.display = 'none';
            // Remove any lingering backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            // Remove modal-open class and styles from body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Reinitialize modal
            bsModal.dispose();
            bsModal = new bootstrap.Modal(contactModal);
        });

        // Define handleContactView function first
        async function handleContactView() {
            const id = this.dataset.id;
            try {
                // Update status to 'read'
                const statusResponse = await fetch('forms/update_contact_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        id: id, 
                        status: 'read'
                    })
                });

                // Fetch contact details
                const response = await fetch(`forms/get_contact.php?id=${id}`);
                const data = await response.json();
                
                contactIdInput.value = id;
                contactSubject.textContent = data.subject;
                contactMessage.textContent = data.message;
                contactDate.textContent = data.created_at;
                
                if (data.reply_message) {
                    previousReply.textContent = data.reply_message;
                    previousReplyDate.textContent = data.replied_at;
                    previousReplySection.style.display = 'block';
                } else {
                    previousReplySection.style.display = 'none';
                }
                
                bsModal.show();
                
                // Refresh the contacts table
                await loadContacts();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Define loadContacts function after handleContactView
        async function loadContacts() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const page = urlParams.get('page') || 1;
                const response = await fetch(`forms/get_contacts_table.php?page=${page}`);
                const data = await response.json();
                
                const tbody = document.querySelector('.table-responsive table tbody');
                if (!tbody) return;
                
                tbody.innerHTML = data.contacts.map(contact => `
                    <tr>
                        <td>${contact.id}</td>
                        <td>${escapeHtml(contact.subject)}</td>
                        <td>${escapeHtml(contact.message.substring(0, 50))}...</td>
                        <td>${formatDate(contact.created_at)}</td>
                        <td>
                            <span class="badge bg-${getStatusBadgeClass(contact.status)}">
                                ${getStatusText(contact.status)}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm view-contact" 
                                    data-id="${contact.id}"
                                    style="background-color: var(--accent-color); color: var(--contrast-color);">
                                Chi tiết
                            </button>
                        </td>
                    </tr>
                `).join('');

                // Update pagination if it exists
                const pagination = document.querySelector('.pagination');
                if (pagination && data.totalPages > 1) {
                    pagination.innerHTML = Array.from({ length: data.totalPages }, (_, i) => i + 1)
                        .map(i => `
                            <li class="page-item ${i === data.currentPage ? 'active' : ''}">
                                <a class="page-link" href="?page=${i}">${i}</a>
                            </li>
                        `).join('');
                }
                
                // Reattach event listeners
                document.querySelectorAll('.view-contact').forEach(button => {
                    button.addEventListener('click', handleContactView);
                });
            } catch (error) {
                console.error('Error loading contacts:', error);
            }
        }

        // Initial attachment of event listeners
        document.querySelectorAll('.view-contact').forEach(button => {
            button.addEventListener('click', handleContactView);
        });

        // Update reply form submission handler
        replyForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = contactIdInput.value;
            const reply = replyMessage.value;
            
            try {
                const response = await fetch('forms/reply_contact.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id, reply })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Phản hồi đã được gửi thành công');
                    replyMessage.value = '';
                    bsModal.hide();
                    
                    // Clean up modal
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    // Wait for modal to fully close before refreshing
                    setTimeout(async () => {
                        await loadContacts();
                        // Reinitialize modal
                        bsModal.dispose();
                        bsModal = new bootstrap.Modal(contactModal);
                    }, 300);
                } else {
                    throw new Error(data.message || 'Failed to send reply');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi phản hồi');
            }
        });

        // Helper functions for the contact table rendering
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('vi-VN', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function getStatusBadgeClass(status) {
            switch(status) {
                case 'new': return 'primary';
                case 'read': return 'info';
                case 'replied': return 'success';
                default: return 'secondary';
            }
        }

        function getStatusText(status) {
            switch(status) {
                case 'new': return 'Mới';
                case 'read': return 'Đã đọc';
                case 'replied': return 'Đã trả lời';
                default: return status;
            }
        }
    });
    EOT
];

require_once '../config/database.php';

// Get statistics
$stats = [
    'contacts' => $conn->query("SELECT COUNT(*) FROM contact_message")->fetch_row()[0],
    'new_contacts' => $conn->query("SELECT COUNT(*) FROM contact_message WHERE status='new'")->fetch_row()[0],
    'users' => $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0],
    'active_users' => $conn->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetch_row()[0],
    'orders' => $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0],
    'unpaid_orders' => $conn->query("SELECT COUNT(*) FROM orders WHERE payment_status='unpaid'")->fetch_row()[0],
    'drivers' => $conn->query("SELECT COUNT(*) FROM drivers")->fetch_row()[0],
    'available_drivers' => $conn->query("SELECT COUNT(*) FROM drivers WHERE status='available'")->fetch_row()[0]
];

// Get paginated contacts
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$total_records = $conn->query("SELECT COUNT(*) FROM contact_message")->fetch_row()[0];
$total_pages = ceil($total_records / $per_page);

$recent_contacts = $conn->query("SELECT * FROM contact_message ORDER BY id ASC LIMIT $offset, $per_page");

include '../includes/header.php';

if (!$isAdmin) {  // $isAdmin is set in header.php
    header('Location: ../login.php');
    exit;
}
?>

<main class="main">
    <!-- Page Title -->
    <div class="page-title dark-background" data-aos="fade">
        <div class="container position-relative">
            <h1>Tổng quan</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="../index.php">Trang chủ</a></li>
                    <li class="current">Tổng quan</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Dashboard Content -->
    <section class="dashboard-section section">
        <div class="container">
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card" data-aos="fade-up">
                        <div class="card-body">
                            <h5>Tổng số liên hệ</h5>
                            <h3><?php echo $stats['contacts']; ?></h3>
                            <p class="text-muted">Chưa đọc: <?php echo $stats['new_contacts']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card" data-aos="fade-up" data-aos-delay="100">
                        <div class="card-body">
                            <h5>Người dùng</h5>
                            <h3><?php echo $stats['users']; ?></h3>
                            <p class="text-muted">Đang hoạt động: <?php echo $stats['active_users']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card" data-aos="fade-up" data-aos-delay="200">
                        <div class="card-body">
                            <h5>Đơn hàng</h5>
                            <h3><?php echo $stats['orders']; ?></h3>
                            <p class="text-muted">Chưa thanh toán: <?php echo $stats['unpaid_orders']; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card" data-aos="fade-up" data-aos-delay="300">
                        <div class="card-body">
                            <h5>Tài xế</h5>
                            <h3><?php echo $stats['drivers']; ?></h3>
                            <p class="text-muted">Đang rảnh: <?php echo $stats['available_drivers']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Contacts Table -->
            <div class="col-12 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Quản lý liên hệ</h5>
                        <a href="manage_contacts.php" class="btn btn-sm" 
                           style="background-color: var(--accent-color); color: var(--contrast-color);">
                            Xem tất cả
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tiêu đề</th>
                                        <th>Nội dung</th>
                                        <th>Ngày gửi</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($contact = $recent_contacts->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $contact['id']; ?></td>
                                        <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($contact['message'], 0, 50)) . '...'; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo match($contact['status']) {
                                                    'new' => 'primary',
                                                    'read' => 'info',
                                                    'replied' => 'success',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php 
                                                echo match($contact['status']) {
                                                    'new' => 'Mới',
                                                    'read' => 'Đã đọc',
                                                    'replied' => 'Đã trả lời',
                                                    default => $contact['status']
                                                }; 
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm view-contact" 
                                                    data-id="<?php echo $contact['id']; ?>"
                                                    style="background-color: var(--accent-color); color: var(--contrast-color);">
                                                Chi tiết
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page === $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Full Users Table -->
            <div class="col-12 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Quản lý người dùng</h5>
                        <a href="manage_users.php" class="btn btn-sm" style="background-color: var(--accent-color); color: var(--contrast-color);">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên đăng nhập</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Số điện thoại</th>
                                        <th>Loại</th>
                                        <th>Gói dịch vụ</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $users = $conn->query("SELECT * FROM users ORDER BY id ASC LIMIT 10");
                                    while ($user = $users->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['user_type'] === 'admin' ? 'danger' : 'info'; ?>">
                                                <?php 
                                                echo match($user['user_type']) {
                                                    'admin' => 'Quản trị',
                                                    'customer' => 'Khách hàng',
                                                    default => $user['user_type']
                                                }; 
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php 
                                                echo match($user['service_tier']) {
                                                    'basic' => 'Cơ bản',
                                                    'standard' => 'Tiêu chuẩn',
                                                    'premium' => 'Cao cấp',
                                                    default => $user['service_tier']
                                                }; 
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'warning'; ?>">
                                                <?php 
                                                echo match($user['status']) {
                                                    'active' => 'Hoạt động',
                                                    'inactive' => 'Tạm khóa',
                                                    default => $user['status']
                                                }; 
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm"
                                               style="background-color: var(--accent-color); color: var(--contrast-color);">
                                               Sửa
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Full Orders Table -->
            <div class="col-12 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Quản lý đơn hàng</h5>
                        <a href="manage_orders.php" class="btn btn-sm" style="background-color: var(--accent-color); color: var(--contrast-color);">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Khách hàng</th>
                                        <th>Người nhận</th>
                                        <th>SĐT nhận</th>
                                        <th>Địa chỉ nhận</th>
                                        <th>Tổng tiền</th>
                                        <th>PT thanh toán</th>
                                        <th>Trạng thái TT</th>
                                        <th>Tài xế</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $orders = $conn->query("
                                        SELECT o.*, u.username, u.full_name as customer_name, d.name as driver_name 
                                        FROM orders o 
                                        LEFT JOIN users u ON o.user_id = u.id
                                        LEFT JOIN drivers d ON o.driver_id = d.id 
                                        ORDER BY o.id ASC 
                                        LIMIT 5
                                    ");
                                    while ($order = $orders->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td><?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['shipping_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['shipping_phone']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($order['shipping_address'], 0, 30)) . '...'; ?></td>
                                        <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo match($order['payment_status']) {
                                                    'paid' => 'success',
                                                    'unpaid' => 'warning',
                                                    'cancel' => 'danger',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php 
                                                echo match($order['payment_status']) {
                                                    'paid' => 'Đã thanh toán',
                                                    'unpaid' => 'Chưa thanh toán',
                                                    'cancel' => 'Đã hủy',
                                                    default => $order['payment_status']
                                                }; 
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo match($order['payment_status']) {
                                                    'paid' => 'success',
                                                    'unpaid' => 'warning',
                                                    'cancel' => 'danger',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php 
                                                echo match($order['payment_status']) {
                                                    'paid' => 'Đã thanh toán',
                                                    'unpaid' => 'Chưa thanh toán',
                                                    'cancel' => 'Đã hủy',
                                                    default => $order['payment_status']
                                                }; 
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo $order['driver_name'] ?? 'Chưa phân công'; ?></td>
                                        <td>
                                            <a href="edit_order.php?id=<?php echo $order['id']; ?>" 
                                               class="btn btn-sm"
                                               style="background-color: var(--accent-color); color: var(--contrast-color);">
                                               Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Drivers Table -->
            <div class="col-12 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Quản lý tài xế</h5>
                        <a href="manage_drivers.php" class="btn btn-sm" style="background-color: var(--accent-color); color: var(--contrast-color);">Xem tất cả</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Họ tên</th>
                                        <th>Số điện thoại</th>
                                        <th>Biển số xe</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $drivers = $conn->query("
                                        SELECT * FROM drivers 
                                        ORDER BY id ASC 
                                        LIMIT 5
                                    ");
                                    while ($driver = $drivers->fetch_assoc()): 
                                    ?>
                                    <tr>
                                        <td><?php echo $driver['id']; ?></td>
                                        <td><?php echo htmlspecialchars($driver['name']); ?></td>
                                        <td><?php echo htmlspecialchars($driver['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($driver['license_plate']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo match($driver['status']) {
                                                    'available' => 'success',
                                                    'busy' => 'warning',
                                                    'offline' => 'secondary',
                                                    default => 'secondary'
                                                };
                                            ?>">
                                                <?php 
                                                echo match($driver['status']) {
                                                    'available' => 'Sẵn sàng',
                                                    'busy' => 'Bận',
                                                    'offline' => 'Ngoại tuyến',
                                                    default => $driver['status']
                                                }; 
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit_driver.php?id=<?php echo $driver['id']; ?>" 
                                               class="btn btn-sm"
                                               style="background-color: var(--accent-color); color: var(--contrast-color);">
                                               Sửa
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Contact View Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết liên hệ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="contactId">
                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <p id="contactSubject" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <p id="contactMessage" class="form-control-plaintext"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngày gửi</label>
                    <p id="contactDate" class="form-control-plaintext"></p>
                </div>
                <div id="previousReplySection" style="display:none">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">Phản hồi trước đó</h6>
                            <p id="previousReply" class="card-text"></p>
                            <small id="previousReplyDate" class="text-muted"></small>
                        </div>
                    </div>
                </div>
                <form id="replyForm">
                    <div class="mb-3">
                        <label class="form-label">Phản hồi</label>
                        <textarea id="replyMessage" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi phản hồi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
if (isset($conn)) $conn->close();
include '../includes/footer.php'; 
?> 