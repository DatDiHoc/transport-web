<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$pageTitle = 'Chỉnh sửa người dùng - Gerrapp Admin';
$currentPage = 'admin-users';
$bodyClass = 'admin-page';

require_once '../config/database.php';

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$user_id) {
    header('Location: manage_users.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $status = $_POST['status'];
    $user_type = $_POST['user_type'];
    $service_tier = $_POST['service_tier'];

    $stmt = $conn->prepare("
        UPDATE users 
        SET full_name = ?, email = ?, phone = ?, address = ?, 
            status = ?, user_type = ?, service_tier = ?
        WHERE id = ?
    ");

    $stmt->bind_param("sssssssi", 
        $full_name, $email, $phone, $address, 
        $status, $user_type, $service_tier, $user_id
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Cập nhật thông tin thành công!";
        header("Location: manage_users.php");
        exit;
    } else {
        $error = "Có lỗi xảy ra khi cập nhật thông tin!";
    }
}

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

include '../includes/header.php';
?>

<main class="main">
    <div class="page-title dark-background" data-aos="fade">
        <div class="container position-relative">
            <h1>Chỉnh sửa người dùng</h1>
            <nav class="breadcrumbs">
                <ol>
                    <li><a href="../index.php">Trang chủ</a></li>
                    <li><a href="dashboard.php">Tổng quan</a></li>
                    <li><a href="manage_users.php">Quản lý người dùng</a></li>
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
                                    <label class="col-sm-3 col-form-label">Tên đăng nhập</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Họ tên</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Email</label>
                                    <div class="col-sm-9">
                                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Số điện thoại</label>
                                    <div class="col-sm-9">
                                        <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Địa chỉ</label>
                                    <div class="col-sm-9">
                                        <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Loại tài khoản</label>
                                    <div class="col-sm-9">
                                        <select name="user_type" class="form-control" required>
                                            <option value="customer" <?php echo $user['user_type'] === 'customer' ? 'selected' : ''; ?>>Khách hàng</option>
                                            <option value="admin" <?php echo $user['user_type'] === 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Gói dịch vụ</label>
                                    <div class="col-sm-9">
                                        <select name="service_tier" class="form-control" required>
                                            <option value="basic" <?php echo $user['service_tier'] === 'basic' ? 'selected' : ''; ?>>Cơ bản</option>
                                            <option value="standard" <?php echo $user['service_tier'] === 'standard' ? 'selected' : ''; ?>>Tiêu chuẩn</option>
                                            <option value="elite" <?php echo $user['service_tier'] === 'elite' ? 'selected' : ''; ?>>Elite</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label class="col-sm-3 col-form-label">Trạng thái</label>
                                    <div class="col-sm-9">
                                        <select name="status" class="form-control" required>
                                            <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                            <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Tạm khóa</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-9 offset-sm-3">
                                        <button type="submit" class="btn" style="background-color: var(--accent-color); color: var(--contrast-color);">
                                            Cập nhật
                                        </button>
                                        <a href="manage_users.php" class="btn btn-secondary">Hủy</a>
                                    </div>
                                </div>
                            </form>
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