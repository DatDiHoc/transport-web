<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$pageTitle = 'Quản lý tài xế - Gerrapp Admin';
$currentPage = 'admin-drivers';
$bodyClass = 'admin-page';

// Define inline scripts
$inlineScripts = '<script>
document.addEventListener("DOMContentLoaded", function() {
    const addDriverForm = document.getElementById("addDriverForm");
    
    addDriverForm.addEventListener("submit", async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch("forms/add_driver.php", {
                method: "POST",
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || "Có lỗi xảy ra khi thêm tài xế");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Có lỗi xảy ra khi xử lý yêu cầu");
        }
    });
});
</script>';

// Add to header
$extraHeadContent = $inlineScripts;

require_once '../config/database.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$total_records = $conn->query("SELECT COUNT(*) FROM drivers")->fetch_row()[0];
$total_pages = ceil($total_records / $per_page);

// Get drivers with pagination
$drivers = $conn->query("
    SELECT * FROM drivers 
    ORDER BY id ASC 
    LIMIT $offset, $per_page
");

include '../includes/header.php';
?>

<main class="main">
    <div class="page-title dark-background" data-aos="fade">
        <div class="container position-relative">
            <h1>Quản lý tài xế</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="../index.php">Trang chủ</a></li>
                    <li><a href="dashboard.php">Tổng quan</a></li>
                    <li class="current">Quản lý tài xế</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Danh sách tài xế</h5>
                            <button type="button" class="btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#addDriverModal"
                                    style="background-color: var(--accent-color); color: var(--contrast-color);">
                                Thêm tài xế mới
                            </button>
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
                                            <th>Ngày tạo</th>
                                            <th>Cập nhật</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($driver = $drivers->fetch_assoc()): ?>
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
                                                    <?php echo $driver['status']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($driver['created_at'])); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($driver['updated_at'])); ?></td>
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
            </div>
        </div>
    </section>
</main>

<!-- Add Driver Modal -->
<div class="modal fade" id="addDriverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm tài xế mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addDriverForm">
                    <div class="mb-3">
                        <label class="form-label">Họ tên</label>
                        <input type="text" name="name" class="form-control" required 
                               minlength="2" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" name="phone" class="form-control" required 
                               pattern="[0-9]{10}" title="Vui lòng nhập số điện thoại 10 chữ số">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Biển số xe</label>
                        <input type="text" name="license_plate" class="form-control" required 
                               maxlength="20">
                    </div>
                    <button type="submit" class="btn" 
                            style="background-color: var(--accent-color); color: var(--contrast-color);">
                        Thêm tài xế
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
if (isset($conn)) $conn->close();
include '../includes/footer.php'; 
?> 