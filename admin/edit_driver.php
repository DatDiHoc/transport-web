<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$pageTitle = 'Chỉnh sửa tài xế - Gerrapp Admin';
$currentPage = 'admin-drivers';
$bodyClass = 'admin-page';

require_once '../config/database.php';

// Get driver ID from URL
$driver_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$driver_id) {
    header('Location: manage_drivers.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $license_plate = trim($_POST['license_plate']);

    $stmt = $conn->prepare("
        UPDATE drivers 
        SET name = ?, phone = ?, license_plate = ?, 
            updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");

    $stmt->bind_param("sssi", 
        $name, $phone, $license_plate, $driver_id
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cập nhật thông tin tài xế thành công!";
        header("Location: manage_drivers.php");
        exit;
    } else {
        $error = "Có lỗi xảy ra khi cập nhật thông tin!";
    }
}

// Get driver data
$stmt = $conn->prepare("SELECT * FROM drivers WHERE id = ?");
$stmt->bind_param("i", $driver_id);
$stmt->execute();
$result = $stmt->get_result();
$driver = $result->fetch_assoc();

if (!$driver) {
    header('Location: manage_drivers.php');
    exit;
}

include '../includes/header.php';
?>

<main class="main">
    <div class="page-title dark-background" data-aos="fade">
        <div class="container position-relative">
            <h1>Chỉnh sửa tài xế</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="../index.php">Trang chủ</a></li>
                    <li><a href="dashboard.php">Tổng quan</a></li>
                    <li><a href="manage_drivers.php">Quản lý tài xế</a></li>
                    <li class="current">Chỉnh sửa</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form method="POST" class="php-email-form">
                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Họ tên</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="name" class="form-control" 
                                               value="<?php echo htmlspecialchars($driver['name']); ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Số điện thoại</label>
                                    <div class="col-sm-9">
                                        <input type="tel" name="phone" class="form-control" 
                                               value="<?php echo htmlspecialchars($driver['phone']); ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Biển số xe</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="license_plate" class="form-control" 
                                               value="<?php echo htmlspecialchars($driver['license_plate']); ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Trạng thái hiện tại</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" 
                                               value="<?php echo match($driver['status']) {
                                                   'available' => 'Đang rảnh',
                                                   'busy' => 'Đang bận',
                                                   'offline' => 'Offline',
                                                   default => $driver['status']
                                               }; ?>" 
                                               disabled>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Ngày tạo</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" 
                                               value="<?php echo date('d/m/Y H:i', strtotime($driver['created_at'])); ?>" 
                                               disabled>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Cập nhật lần cuối</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" 
                                               value="<?php echo date('d/m/Y H:i', strtotime($driver['updated_at'])); ?>" 
                                               disabled>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button type="submit" class="btn" 
                                                style="background-color: var(--accent-color); color: var(--contrast-color);">
                                            Cập nhật
                                        </button>
                                        <a href="manage_drivers.php" class="btn btn-secondary">Hủy</a>
                                    </div>
                                </div>
                            </form>

                            <!-- Driver's Current Orders -->
                            <div class="mt-5">
                                <h5 class="mb-3">Đơn hàng hiện tại</h5>
                                <?php
                                $current_orders = $conn->query("
                                    SELECT o.*, u.full_name as customer_name 
                                    FROM orders o 
                                    LEFT JOIN users u ON o.user_id = u.id 
                                    WHERE o.driver_id = $driver_id 
                                    AND o.payment_status != 'cancel'
                                    ORDER BY o.created_at DESC 
                                    LIMIT 5
                                ");
                                
                                if ($current_orders->num_rows > 0):
                                ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Khách hàng</th>
                                                <th>Địa chỉ</th>
                                                <th>Trạng thái</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($order = $current_orders->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $order['id']; ?></td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($order['shipping_address'], 0, 30)) . '...'; ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($order['payment_status']) {
                                                            'paid' => 'success',
                                                            'unpaid' => 'warning',
                                                            'cancel' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                                                        <?php echo $order['payment_status']; ?>
                                                    </span>
                                                </td>
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
                                <?php else: ?>
                                <p class="text-muted">Không có đơn hàng nào.</p>
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