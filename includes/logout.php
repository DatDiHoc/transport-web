<?php
session_start();
header('Content-Type: application/json');

try {
    // Clear all session data
    $_SESSION = array();
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng xuất thành công'
    ]);
} catch (Exception $e) {
    error_log("Logout error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra khi đăng xuất'
    ]);
}
?> 