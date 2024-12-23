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
    
    $stmt = $conn->prepare("
        SELECT username, email, phone, address
        FROM users 
        WHERE id = ?
    ");
    
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        throw new Exception('Không tìm thấy thông tin người dùng');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra khi tải thông tin người dùng',
        'debug' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?> 