<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../config/database.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'] ?? null;
$driver_id = $data['driver_id'] ?? null;

if (!$order_id) {
    echo json_encode(['success' => false, 'message' => 'Missing order ID']);
    exit;
}

try {
    $conn->begin_transaction();

    // If removing driver assignment
    if ($driver_id === null) {
        // Get current driver ID before removing
        $stmt = $conn->prepare("SELECT driver_id FROM orders WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_driver = $result->fetch_assoc();
        
        if ($current_driver && $current_driver['driver_id']) {
            // Check if driver has other active orders
            $stmt = $conn->prepare("
                SELECT COUNT(*) as order_count 
                FROM orders 
                WHERE driver_id = ? AND id != ?
            ");
            $stmt->bind_param("ii", $current_driver['driver_id'], $order_id);
            $stmt->execute();
            $order_count = $stmt->get_result()->fetch_assoc()['order_count'];

            // Update driver status to available if less than 10 orders
            if ($order_count < 9) {
                $stmt = $conn->prepare("
                    UPDATE drivers 
                    SET status = 'available' 
                    WHERE id = ?
                ");
                $stmt->bind_param("i", $current_driver['driver_id']);
                $stmt->execute();
            }
        }

        // Update order
        $stmt = $conn->prepare("UPDATE orders SET driver_id = NULL WHERE id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
    } else {
        // Check if order is eligible for driver assignment
        $stmt = $conn->prepare("
            SELECT payment_status, payment_method 
            FROM orders 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        // Validate payment conditions
        if ($order['payment_status'] !== 'paid' && $order['payment_method'] !== 'cod') {
            throw new Exception("Chỉ có thể phân công tài xế cho đơn hàng đã thanh toán hoặc COD");
        }

        // Check if driver exists and is available or busy
        $stmt = $conn->prepare("
            SELECT status, 
                   (SELECT COUNT(*) FROM orders WHERE driver_id = drivers.id) as current_orders
            FROM drivers 
            WHERE id = ? AND (status = 'available' OR status = 'busy')
        ");
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $driver = $result->fetch_assoc();

        if (!$driver) {
            throw new Exception("Tài xế không khả dụng");
        }

        // Check if driver has reached order limit
        if ($driver['current_orders'] >= 9 && $driver['status'] === 'available') {
            // Update driver status to busy
            $stmt = $conn->prepare("UPDATE drivers SET status = 'busy' WHERE id = ?");
            $stmt->bind_param("i", $driver_id);
            $stmt->execute();
        }

        // Update order
        $stmt = $conn->prepare("UPDATE orders SET driver_id = ? WHERE id = ?");
        $stmt->bind_param("ii", $driver_id, $order_id);
        $stmt->execute();
    }

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();