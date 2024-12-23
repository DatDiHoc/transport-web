<?php
session_start();
require_once '../../config/database.php';

// Check admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

// Get recent contacts with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$recent_contacts = $conn->query("
    SELECT * FROM contact_message 
    ORDER BY id ASC 
    LIMIT $offset, $per_page
");

// Convert to array
$contacts = [];
while ($contact = $recent_contacts->fetch_assoc()) {
    // Format the data to match the dashboard structure
    $contacts[] = [
        'id' => $contact['id'],
        'subject' => $contact['subject'],
        'message' => $contact['message'],
        'created_at' => $contact['created_at'],
        'status' => $contact['status'],
        'reply_message' => $contact['reply_message'] ?? null,
        'replied_at' => $contact['replied_at'] ?? null
    ];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'contacts' => $contacts,
    'currentPage' => $page,
    'totalPages' => ceil($conn->query("SELECT COUNT(*) FROM contact_message")->fetch_row()[0] / $per_page)
]);

$conn->close(); 