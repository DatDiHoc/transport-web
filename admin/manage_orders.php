<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$pageTitle = 'Quản lý đơn hàng - Gerrapp Admin';
$currentPage = 'admin-orders';
$bodyClass = 'admin-page';

// Define custom styles
$customStyles = '<style>
    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
    }
    .status-available { background-color: #28a745; }
    .status-busy { background-color: #ffc107; }
    .status-offline { background-color: #dc3545; }
    .status-default { background-color: #ffffff; border: 1px solid #6c757d; }
</style>';

// Define inline scripts
$inlineScripts = '<script>
async function assignDriver(orderId, driverId) {
    try {
        const response = await fetch("forms/assign_driver.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ 
                order_id: orderId, 
                driver_id: driverId 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload(); // Refresh to show updated status
        } else {
            alert(data.message || "Có lỗi xảy ra khi phân công tài xế");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Có lỗi xảy ra khi xử lý yêu cầu");
    }
}

async function removeDriver(orderId) {
    if (!confirm("Bạn có chắc muốn bỏ phân công tài xế này?")) return;
    
    try {
        const response = await fetch("forms/assign_driver.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ 
                order_id: orderId, 
                driver_id: null 
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload(); // Refresh to show updated status
        } else {
            alert(data.message || "Có lỗi xảy ra khi bỏ phân công tài xế");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("Có lỗi xảy ra khi xử lý yêu cầu");
    }
}
</script>';

// Add these to the header
$extraHeadContent = $customStyles . $inlineScripts;

require_once '../config/database.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$total_records = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
$total_pages = ceil($total_records / $per_page);

// Get orders with pagination
$orders = $conn->query("
    SELECT o.*, u.username, u.full_name as customer_name, d.name as driver_name, d.status as driver_status 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id
    LEFT JOIN drivers d ON o.driver_id = d.id 
    ORDER BY o.id ASC
    LIMIT $offset, $per_page
");

// Get available drivers for assignment
$available_drivers = $conn->query("
    SELECT id, name, license_plate, status 
    FROM drivers 
    ORDER BY 
        CASE status
            WHEN 'available' THEN 1
            WHEN 'busy' THEN 2
            WHEN 'offline' THEN 3
        END,
        name ASC
");

$drivers_list = [];
while ($driver = $available_drivers->fetch_assoc()) {
    $drivers_list[] = $driver;
}

include '../includes/header.php';
?>

<main class="main">
    <div class="page-title dark-background" data-aos="fade">
        <div class="container position-relative">
            <h1>Quản lý đơn hàng</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="../index.php">Trang chủ</a></li>
                    <li><a href="dashboard.php">Tổng quan</a></li>
                    <li class="current">Quản lý đơn hàng</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="card">
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
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['shipping_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['shipping_phone']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($order['shipping_address'], 0, 30)) . '...'; ?></td>
                                    <td><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</td>
                                    <td>
                                        <?php 
                                        $paymentMethod = match($order['payment_method']) {
                                            'cod' => 'COD',
                                            'banking' => 'Banking',
                                            'momo' => 'MoMo',
                                            default => $order['payment_method']
                                        };
                                        echo htmlspecialchars($paymentMethod); 
                                        ?>
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
                                            <?php echo $order['payment_status']; ?>
                                        </span>
                                    </td>
                                    <td id="driver-cell-<?php echo $order['id']; ?>">
                                        <?php if ($order['driver_name']): ?>
                                            <div class="d-flex align-items-center">
                                                <span class="status-indicator status-<?php echo $order['driver_status']; ?>"></span>
                                                <span class="me-2"><?php echo htmlspecialchars($order['driver_name']); ?></span>
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        onclick="removeDriver(<?php echo $order['id']; ?>)">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <select id="driver-select-<?php echo $order['id']; ?>" 
                                                    class="form-select form-select-sm"
                                                    onchange="assignDriver(<?php echo $order['id']; ?>, this.value)"
                                                    <?php echo ($order['payment_status'] !== 'paid' && $order['payment_method'] !== 'cod') ? 'disabled' : ''; ?>>
                                                <option value="" selected>
                                                    <?php echo ($order['payment_status'] !== 'paid' && $order['payment_method'] !== 'cod') 
                                                        ? 'Chưa thể phân công' 
                                                        : 'Chọn tài xế...'; ?>
                                                </option>
                                                <?php if ($order['payment_status'] === 'paid' || $order['payment_method'] === 'cod'): ?>
                                                    <?php foreach ($drivers_list as $driver): ?>
                                                        <option value="<?php echo $driver['id']; ?>">
                                                            <?php 
                                                            $statusSymbol = match($driver['status']) {
                                                                'available' => '🟢',
                                                                'busy' => '🟡',
                                                                'offline' => '🔴',
                                                                default => '⚪'
                                                            };
                                                            echo $statusSymbol . ' ' . htmlspecialchars($driver['name']) . 
                                                                 ' (' . htmlspecialchars($driver['license_plate']) . ')';
                                                            ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        <?php endif; ?>
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
    </section>
</main>

<?php 
if (isset($conn)) $conn->close();
include '../includes/footer.php'; 
?> 