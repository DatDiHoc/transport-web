<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$pageTitle = 'Chi tiết đơn hàng - Gerrapp Admin';
$currentPage = 'admin-orders';
$bodyClass = 'admin-page';

require_once '../config/database.php';

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    header('Location: manage_orders.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_status = $_POST['payment_status'];

    $stmt = $conn->prepare("
        UPDATE orders 
        SET payment_status = ?
        WHERE id = ?
    ");

    $stmt->bind_param("si", $payment_status, $order_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cập nhật đơn hàng thành công!";
        header("Location: manage_orders.php");
        exit;
    } else {
        $error = "Có lỗi xảy ra khi cập nhật đơn hàng!";
    }
}

// Get order data with customer and driver info
$stmt = $conn->prepare("
    SELECT o.*, u.username, u.full_name as customer_name, 
           d.id as driver_id, d.name as driver_name
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN drivers d ON o.driver_id = d.id 
    WHERE o.id = ?
");

$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: manage_orders.php');
    exit;
}

// Get order items
$stmt = $conn->prepare("
    SELECT * FROM order_items 
    WHERE order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get available drivers
$drivers = $conn->query("SELECT id, name FROM drivers WHERE status = 'active'")->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
?>

<main class="main">
    <div class="page-title dark-background" data-aos="fade">
        <div class="container position-relative">
            <h1>Chi tiết đơn hàng #<?php echo $order_id; ?></h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="../index.php">Trang chủ</a></li>
                    <li><a href="dashboard.php">Tổng quan</a></li>
                    <li><a href="manage_orders.php">Quản lý đơn hàng</a></li>
                    <li class="current">Chi tiết</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Order Details -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Thông tin đơn hàng</h5>
                            
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                    <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['shipping_name']); ?></p>
                                    <p><strong>SĐT:</strong> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                                    <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                    <p><strong>PT thanh toán:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                                    <p><strong>Tổng tiền:</strong> <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</p>
                                    <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes'] ?: 'Không có'); ?></p>
                                </div>
                            </div>

                            <form method="POST" class="php-email-form">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Trạng thái thanh toán</label>
                                        <select name="payment_status" class="form-control" required>
                                            <option value="unpaid" <?php echo $order['payment_status'] === 'unpaid' ? 'selected' : ''; ?>>Chưa thanh toán</option>
                                            <option value="paid" <?php echo $order['payment_status'] === 'paid' ? 'selected' : ''; ?>>Đã thanh toán</option>
                                            <option value="cancel" <?php echo $order['payment_status'] === 'cancel' ? 'selected' : ''; ?>>Đã hủy</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn" style="background-color: var(--accent-color); color: var(--contrast-color);">
                                        Cập nhật
                                    </button>
                                    <a href="manage_orders.php" class="btn btn-secondary">Quay lại</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Chi tiết sản phẩm</h5>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Số lượng</th>
                                            <th>Kích thước (cm)</th>
                                            <th>Khối lượng (kg)</th>
                                            <th>Phí vận chuyển</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo $item['length'] . ' × ' . $item['width'] . ' × ' . $item['height']; ?></td>
                                            <td><?php echo $item['weight']; ?></td>
                                            <td><?php echo number_format($item['shipping_fee'], 0, ',', '.'); ?>đ</td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Lịch sử đơn hàng</h5>
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6>Đơn hàng được tạo</h6>
                                        <p class="text-muted"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                                    </div>
                                </div>
                                <?php if ($order['payment_status'] === 'paid'): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6>Đã thanh toán</h6>
                                        <p class="text-muted">Thanh toán thành công</p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if ($order['driver_id']): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <h6>Đã phân công tài xế</h6>
                                        <p class="text-muted"><?php echo htmlspecialchars($order['driver_name']); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php 
if (isset($conn)) $conn->close();
include '../includes/footer.php'; 
?> 