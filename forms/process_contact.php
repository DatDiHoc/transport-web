<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");

try {
    // Bắt đầu phiên
    session_start();
    error_log("Kiểm tra phiên - user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'không được thiết lập'));

    // Kiểm tra xem người dùng đã đăng nhập chưa
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng đăng nhập để gửi tin nhắn',
            'redirect' => 'login.html'
        ]);
        exit;
    }

    // Xác thực các trường bắt buộc
    if (empty($_POST['subject']) || empty($_POST['message'])) {
        throw new Exception("Vui lòng điền đầy đủ thông tin");
    }

    // Kết nối đến cơ sở dữ liệu
    require_once '../config/database.php';
    if (!isset($conn)) {
        throw new Exception("Kết nối cơ sở dữ liệu thất bại - biến conn không được thiết lập");
    }

    // Chèn tin nhắn với cấu trúc đơn giản
    $stmt = $conn->prepare("
        INSERT INTO contact_message (
            user_id, subject, message, status
        ) VALUES (?, ?, ?, 'new')
    ");

    if (!$stmt) {
        throw new Exception("Chuẩn bị thất bại: " . $conn->error);
    }

    $stmt->bind_param("iss", 
        $_SESSION['user_id'],
        $_POST['subject'],
        $_POST['message']
    );

    if (!$stmt->execute()) {
        throw new Exception("Chèn thất bại: " . $stmt->error);
    }

    // Lấy ID của tin nhắn đã chèn
    $message_id = $conn->insert_id;

    echo json_encode([
        'success' => true,
        'message' => 'Tin nhắn đã được gửi thành công!',
        'data' => [
            'message_id' => $message_id,
            'subject' => $_POST['subject'],
            'status' => 'new'
        ]
    ]);

} catch (Exception $e) {
    error_log("Lỗi liên hệ: " . $e->getMessage());
    error_log("Trace ngăn xếp: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?> 