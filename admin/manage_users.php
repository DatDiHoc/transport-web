<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$pageTitle = 'Quản lý người dùng - Gerrapp Admin';
$currentPage = 'admin-users';
$bodyClass = 'admin-page';

// Add any custom styles or scripts if needed
$extraHeadContent = '';

require_once '../config/database.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$total_records = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_pages = ceil($total_records / $per_page);

// Get users with pagination
$users = $conn->query("
    SELECT * FROM users 
    ORDER BY id ASC
    LIMIT $offset, $per_page
");

include '../includes/header.php';
?>

<main class="main">
    <div class="page-title dark-background" data-aos="fade">
        <div class="container position-relative">
            <h1>Quản lý người dùng</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="../index.php">Trang chủ</a></li>
                    <li><a href="dashboard.php">Tổng quan</a></li>
                    <li class="current">Quản lý người dùng</li>
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
                                <?php while ($user = $users->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['user_type'] === 'admin' ? 'danger' : 'info'; ?>">
                                            <?php echo $user['user_type']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo $user['service_tier']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'warning'; ?>">
                                            <?php echo $user['status']; ?>
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