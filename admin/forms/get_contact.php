<?php
session_start();
require_once '../../config/database.php';

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('Missing ID');
}

$stmt = $conn->prepare("SELECT * FROM contact_message WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
$contact = $result->fetch_assoc();

if (!$contact) {
    http_response_code(404);
    exit('Contact not found');
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($contact);

$conn->close(); 