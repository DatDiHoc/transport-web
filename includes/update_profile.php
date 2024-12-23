<?php
session_start();
require_once '../config/database.php';

// Set JSON header
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    // Get form data
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $userId = $_SESSION['user_id'];

    // Update user profile
    $stmt = $conn->prepare("UPDATE users SET phone = ?, address = ? WHERE id = ?");
    $stmt->bind_param("ssi", $phone, $address, $userId);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    } else {
        throw new Exception($stmt->error);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating profile: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 