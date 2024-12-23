<?php
// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../error.log');

require_once '../config/session_config.php';
require_once '../config/cors_config.php';
header('Content-Type: application/json');

try {
    require_once '../config/database.php';

    // Log incoming request
    error_log("Login attempt for username: " . ($_POST['username'] ?? 'not set'));

    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method");
    }

    // Validate input
    if (empty($_POST['username']) || empty($_POST['password'])) {
        throw new Exception("Vui lòng điền đầy đủ thông tin");
    }

    // Check database connection
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed: " . ($conn->connect_error ?? 'Connection not established'));
    }

    // Update SQL to include phone and address
    $stmt = $conn->prepare("SELECT id, username, password, full_name, phone, address, email, status, user_type, service_tier 
                           FROM users 
                           WHERE username = ? AND status = 'active'");
    if (!$stmt) {
        throw new Exception("Database prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $_POST['username']);
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Tên đăng nhập hoặc mật khẩu không đúng");
    }

    $user = $result->fetch_assoc();

    // Check if account is inactive
    if ($user['status'] !== 'active') {
        throw new Exception("Tài khoản của bạn đã bị vô hiệu hóa");
    }

    // Verify password
    if (!password_verify($_POST['password'], $user['password'])) {
        throw new Exception("Tên đăng nhập hoặc mật khẩu không đúng");
    }

    // Update last login time
    $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $updateStmt->bind_param("i", $user['id']);
    $updateStmt->execute();
    $updateStmt->close();

    // Set session variables with additional user data
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['phone'] = $user['phone'];
    $_SESSION['address'] = $user['address'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['service_tier'] = $user['service_tier'];
    $_SESSION['logged_in'] = true;
    $_SESSION['is_admin'] = ($user['user_type'] === 'admin');

    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công!',
        'redirect' => $user['user_type'] === 'admin' ? 'admin/dashboard.php' : 'index.php',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'address' => $user['address'],
            'user_type' => $user['user_type'],
            'service_tier' => $user['service_tier']
        ]
    ]);

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
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