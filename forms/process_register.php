<?php
// Enable error logging but disable display
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../error.log');

require_once '../config/session_config.php';
require_once '../config/cors_config.php';

// Always set JSON content type
header('Content-Type: application/json');

try {
    require_once '../config/database.php';

    // Validate required fields
    $required_fields = ['username', 'email', 'password', 'confirm_password', 'full_name', 'phone', 'address'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Vui lòng điền đầy đủ thông tin");
        }
    }

    // Validate phone number format (Vietnam)
    if (!preg_match('/^(0|\+84)[0-9]{9}$/', $_POST['phone'])) {
        throw new Exception("Số điện thoại không hợp lệ");
    }

    // Validate password match and length
    if ($_POST['password'] !== $_POST['confirm_password']) {
        throw new Exception("Mật khẩu xác nhận không khớp");
    }
    if (strlen($_POST['password']) < 6) {
        throw new Exception("Mật khẩu phải có ít nhất 6 ký tự");
    }

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("Tên đăng nhập đã tồn tại");
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $_POST['email']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception("Email đã được sử dụng");
    }

    // Hash password
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert new user with phone and address
    $stmt = $conn->prepare("
        INSERT INTO users (
            username, 
            email, 
            password, 
            full_name, 
            phone, 
            address, 
            status, 
            user_type, 
            service_tier, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, 'active', 'customer', 'basic', NOW())
    ");

    $stmt->bind_param(
        "ssssss", 
        $_POST['username'],
        $_POST['email'],
        $hashed_password,
        $_POST['full_name'],
        $_POST['phone'],
        $_POST['address']
    );

    if (!$stmt->execute()) {
        throw new Exception("Đăng ký thất bại: " . $stmt->error);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Đăng ký thành công! Vui lòng đăng nhập.',
        'redirect' => 'login.php'
    ]);

} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?> 