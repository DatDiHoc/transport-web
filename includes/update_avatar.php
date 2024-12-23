<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

try {
    if (!isset($_FILES['avatar'])) {
        throw new Exception('No file uploaded');
    }

    $file = $_FILES['avatar'];
    $userId = $_SESSION['user_id'];
    
    // Validate file
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        throw new Exception('File size too large. Maximum size is 5MB.');
    }

    // Create uploads directory if it doesn't exist
    $uploadDir = '../uploads/avatars/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception('Failed to save file');
    }

    // Update database with new avatar path
    $avatarUrl = 'uploads/avatars/' . $filename;
    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE id = ?");
    $stmt->bind_param("si", $avatarUrl, $userId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to update database');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Avatar updated successfully',
        'avatar_url' => $avatarUrl
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error updating avatar: ' . $e->getMessage()
    ]);
}

$conn->close(); 