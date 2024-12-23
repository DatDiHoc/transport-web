<?php
session_start();
header('Content-Type: application/json');

try {
    $response = [
        'success' => true,
        'logged_in' => isset($_SESSION['user_id']),
        'user' => null
    ];

    if (isset($_SESSION['user_id'])) {
        $response['user'] = [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'phone' => $_SESSION['phone'] ?? null,
            'address' => $_SESSION['address'] ?? null,
            'user_type' => $_SESSION['user_type'] ?? 'customer',
            'service_tier' => $_SESSION['service_tier'] ?? 'basic',
            'logged_in' => $_SESSION['logged_in'] ?? false
        ];
    }

    // Add debug information
    $response['debug'] = [
        'session_id' => session_id(),
        'session_status' => session_status(),
        'session_data' => $_SESSION,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'debug' => [
            'session_id' => session_id(),
            'session_status' => session_status(),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ]);
} 