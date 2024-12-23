<?php
header('Content-Type: application/json');
session_start();

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in');
    }

    require_once '../config/database.php';

    $user_id = $_SESSION['user_id'];

    // Updated query to match new table structure
    $query = "
        SELECT 
            id,
            created_at,
            shipping_name,
            shipping_phone,
            shipping_address,
            payment_method,
            payment_status,
            total_amount,
            notes
        FROM orders 
        WHERE user_id = ?
        ORDER BY created_at DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $orders = [];
    while ($row = $result->fetch_assoc()) {
        // Format the data
        $orders[] = [
            'id' => $row['id'],
            'created_at' => date('d/m/Y H:i', strtotime($row['created_at'])),
            'shipping_name' => htmlspecialchars($row['shipping_name']),
            'shipping_phone' => htmlspecialchars($row['shipping_phone']),
            'shipping_address' => htmlspecialchars($row['shipping_address']),
            'payment_method' => htmlspecialchars($row['payment_method']),
            'payment_status' => $row['payment_status'],
            'total_amount' => floatval($row['total_amount']),
            'notes' => htmlspecialchars($row['notes'])
        ];
    }

    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra khi tải danh sách đơn hàng: ' . $e->getMessage()
    ]);
}
?> 