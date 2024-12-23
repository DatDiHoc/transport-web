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
    
    // Validate order ID
    $order_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    if (!$order_id) {
        throw new Exception("ID đơn hàng không hợp lệ");
    }

    // First get the order details
    $stmt = $conn->prepare("
        SELECT 
            id,
            user_id,
            shipping_name,
            shipping_phone,
            shipping_address,
            payment_method,
            payment_status,
            notes,
            total_amount,
            created_at
        FROM gerrapp_db.orders 
        WHERE id = ? AND user_id = ?
    ");
    
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Không tìm thấy đơn hàng");
    }

    $order = $result->fetch_assoc();
    $stmt->close();

    // Then get the order items
    $stmt = $conn->prepare("
        SELECT 
            id,
            product_name,
            quantity,
            length,
            width,
            height,
            weight,
            shipping_fee
        FROM gerrapp_db.order_items 
        WHERE order_id = ?
    ");
    
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();
    
    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }

    // Format dates
    $order['created_at'] = date('d/m/Y H:i', strtotime($order['created_at']));

    echo json_encode([
        'success' => true,
        'order' => $order,
        'items' => $items
    ]);

} catch (Exception $e) {
    error_log("Order details error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?> 