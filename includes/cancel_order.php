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
    $order_id = isset($data['id']) ? filter_var($data['id'], FILTER_SANITIZE_NUMBER_INT) : null;

    if (!$order_id) {
        throw new Exception("ID đơn hàng không hợp lệ");
    }

    // Check if order exists and belongs to user
    $stmt = $conn->prepare("
        UPDATE orders 
        SET payment_status = 'cancel' 
        WHERE id = ? AND user_id = ? AND payment_status = 'unpaid'
    ");
    
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        throw new Exception("Không thể hủy đơn hàng này");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Đơn hàng đã được hủy thành công'
    ]);

} catch (Exception $e) {
    error_log("Cancel order error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?> 