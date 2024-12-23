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

    $message_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    
    if (!$message_id) {
        throw new Exception("ID tin nhắn không hợp lệ");
    }

    $stmt = $conn->prepare("
        SELECT 
            id,
            subject,
            message,
            status,
            created_at,
            replied_at,
            reply_message,
            updated_at
        FROM gerrapp_db.contact_message 
        WHERE id = ? AND user_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("ii", $message_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Không tìm thấy tin nhắn");
    }

    $message = $result->fetch_assoc();

    // Format dates
    $message['created_at'] = date('d/m/Y H:i', strtotime($message['created_at']));
    if ($message['replied_at']) {
        $message['replied_at'] = date('d/m/Y H:i', strtotime($message['replied_at']));
    }
    if ($message['updated_at']) {
        $message['updated_at'] = date('d/m/Y H:i', strtotime($message['updated_at']));
    }

    echo json_encode([
        'success' => true,
        'message' => $message
    ]);

} catch (Exception $e) {
    error_log("Get message detail error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
} 