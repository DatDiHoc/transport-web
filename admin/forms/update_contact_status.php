<?php
session_start();
require_once '../../config/database.php';

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id']) || !isset($input['status'])) {
    http_response_code(400);
    exit('Missing required fields');
}

// Add validation for allowed status values
$allowed_statuses = ['new', 'read', 'replied'];
if (!in_array($input['status'], $allowed_statuses)) {
    http_response_code(400);
    exit('Invalid status value');
}

// Only update if current status is 'new' and new status is 'read'
// or if new status is 'replied'
$stmt = $conn->prepare("
    UPDATE contact_message 
    SET status = ?,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = ? 
    AND (
        (status = 'new' AND ? = 'read')
        OR ? = 'replied'
    )
");

$stmt->bind_param("siss", 
    $input['status'], 
    $input['id'],
    $input['status'],
    $input['status']
);

if (!$stmt->execute()) {
    http_response_code(500);
    exit('Update failed');
}

http_response_code(200);
echo json_encode(['success' => true]);

$conn->close(); 