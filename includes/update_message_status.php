<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy phiên đăng nhập'
    ]);
    exit;
}

try {
    require_once '../config/database.php';

    $data = json_decode(file_get_contents('php://input'), true);
    $message_id = filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT);
    
    if (!$message_id) {
        throw new Exception("ID tin nhắn không hợp lệ");
    }

    $stmt = $conn->prepare("
        UPDATE contact_message 
        SET status = 'read'
        WHERE id = ? AND user_id = ? AND status = 'new'
    ");

    $stmt->bind_param("ii", $message_id, $_SESSION['user_id']);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật trạng thái thành công'
    ]);

} catch (Exception $e) {
    error_log("Update message status error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
} 