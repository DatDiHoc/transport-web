<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gerrapp_db');

// Create connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Set charset
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error loading character set utf8mb4: " . $conn->error);
    }

    return $conn;

} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]));
}
?>
