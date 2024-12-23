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

if (!isset($input['id']) || !isset($input['reply'])) {
    http_response_code(400);
    exit('Missing required fields');
}

$stmt = $conn->prepare("
    UPDATE contact_message 
    SET reply_message = ?, 
        replied_at = CURRENT_TIMESTAMP, 
        replied_by = ?,
        status = 'replied',
        updated_at = CURRENT_TIMESTAMP
    WHERE id = ?
");

$stmt->bind_param("sii", 
    $input['reply'], 
    $_SESSION['user_id'], 
    $input['id']
);

if (!$stmt->execute()) {
    http_response_code(500);
    exit('Reply failed');
}

http_response_code(200);
echo json_encode(['success' => true]);

$conn->close(); 