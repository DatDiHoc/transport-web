<?php
// Prevent any output before intended JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Set session cookie parameters
    session_set_cookie_params([
        'lifetime' => 86400, // 24 hours
        'path' => '/',
        'domain' => 'localhost',
        'secure' => false,    // Set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'  // Or 'Strict' for more security
    ]);

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Session configuration error: ' . $e->getMessage()
    ]);
    exit;
}
?> 