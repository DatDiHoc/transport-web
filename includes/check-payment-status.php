<?php
header('Content-Type: application/json');
session_start();

// Prevent any HTML error output
error_reporting(0);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../config/database.php';
    
    if (!isset($_GET['id'])) {
        throw new Exception('Order ID is required');
    }

    $orderId = $_GET['id'];
    
    // Updated query to match new table structure
    $stmt = $conn->prepare("
        SELECT payment_method, payment_status, total_amount 
        FROM orders 
        WHERE id = ?
    ");
    
    if (!$stmt) {
        throw new Exception('Database query preparation failed');
    }
    
    $stmt->bind_param("i", $orderId);
    
    if (!$stmt->execute()) {
        throw new Exception('Database query execution failed');
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'status' => $order['payment_status'],
            'payment_method' => $order['payment_method'],
            'total_amount' => $order['total_amount']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Order not found'
        ]);
    }

} catch (Exception $e) {
    // Log error for debugging
    error_log("Payment status check error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Ensure no additional output
exit();
?> 