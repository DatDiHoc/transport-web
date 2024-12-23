<?php
session_start();

// Authentication check
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../config/database.php';

// Validate input
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$license_plate = trim($_POST['license_plate'] ?? '');
$status = 'available'; // Always set status as available for new drivers

// Basic validation
if (empty($name) || empty($phone) || empty($license_plate)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
    exit;
}

// Validate phone number format
if (!preg_match('/^[0-9]{10}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Số điện thoại không hợp lệ']);
    exit;
}

// Check if phone number already exists
$stmt = $conn->prepare("SELECT id FROM drivers WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Số điện thoại đã tồn tại trong hệ thống']);
    exit;
}

// Check if license plate already exists
$stmt = $conn->prepare("SELECT id FROM drivers WHERE license_plate = ?");
$stmt->bind_param("s", $license_plate);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Biển số xe đã tồn tại trong hệ thống']);
    exit;
}

// Insert new driver
try {
    $stmt = $conn->prepare("
        INSERT INTO drivers (name, phone, license_plate, status, created_at, updated_at) 
        VALUES (?, ?, ?, 'available', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    ");
    
    $stmt->bind_param("sss", $name, $phone, $license_plate);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Thêm tài xế thành công',
            'driver_id' => $conn->insert_id
        ]);
    } else {
        throw new Exception("Database error: " . $conn->error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Có lỗi xảy ra khi thêm tài xế'
    ]);
}

$conn->close(); 