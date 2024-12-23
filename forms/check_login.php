<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../config/session_config.php';
require_once '../config/cors_config.php';

header('Content-Type: application/json');

try {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

    echo json_encode([
        'success' => true,
        'logged_in' => $isLoggedIn,
        'user_id' => $isLoggedIn ? $_SESSION['user_id'] : null,
        'username' => $isLoggedIn ? $_SESSION['username'] : null
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Session check failed: ' . $e->getMessage()
    ]);
}
?> 