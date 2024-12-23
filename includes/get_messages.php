<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Không tìm thấy phiên đăng nhập'
    ]);
    exit;
}

try {
    require_once '../config/database.php';

    $stmt = $conn->prepare("
        SELECT 
            id,
            subject,
            message,
            status,
            created_at,
            replied_at,
            reply_message
        FROM contact_message 
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        // Format the dates
        $row['created_at'] = date('d/m/Y H:i', strtotime($row['created_at']));
        if ($row['replied_at']) {
            $row['replied_at'] = date('d/m/Y H:i', strtotime($row['replied_at']));
        }
        $messages[] = $row;
    }

    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);

} catch (Exception $e) {
    error_log("Get messages error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra khi tải tin nhắn'
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?> 