<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");

ob_start();

try {
    $conn = require_once '../config/database.php';
    
    if (!$conn) {
        throw new Exception("Kết nối cơ sở dữ liệu thất bại");
    }

    // Lấy loại hành động từ yêu cầu
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'create':
            // Xác thực đầu vào
            if (empty($_POST['name'])) throw new Exception("Thiếu tên tài xế");
            if (empty($_POST['phone'])) throw new Exception("Thiếu số điện thoại");
            if (empty($_POST['license_plate'])) throw new Exception("Thiếu biển số xe");

            // Chuẩn bị câu lệnh
            $stmt = $conn->prepare("
                INSERT INTO drivers (name, phone, license_plate) 
                VALUES (?, ?, ?)
            ");

            $stmt->bind_param("sss", 
                $_POST['name'],
                $_POST['phone'],
                $_POST['license_plate']
            );

            if (!$stmt->execute()) {
                throw new Exception("Tạo tài xế thất bại: " . $stmt->error);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Tài xế đã được tạo thành công',
                'driver_id' => $conn->insert_id
            ]);
            break;

        case 'update':
            if (empty($_POST['id'])) throw new Exception("Thiếu ID tài xế");

            $updates = [];
            $types = "";
            $params = [];

            if (isset($_POST['name'])) {
                $updates[] = "name = ?";
                $types .= "s";
                $params[] = $_POST['name'];
            }
            if (isset($_POST['phone'])) {
                $updates[] = "phone = ?";
                $types .= "s";
                $params[] = $_POST['phone'];
            }
            if (isset($_POST['license_plate'])) {
                $updates[] = "license_plate = ?";
                $types .= "s";
                $params[] = $_POST['license_plate'];
            }
            if (isset($_POST['status'])) {
                $updates[] = "status = ?";
                $types .= "s";
                $params[] = $_POST['status'];
            }

            if (empty($updates)) {
                throw new Exception("Không có trường nào để cập nhật");
            }

            $sql = "UPDATE drivers SET " . implode(", ", $updates) . " WHERE id = ?";
            $types .= "i";
            $params[] = $_POST['id'];

            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);

            if (!$stmt->execute()) {
                throw new Exception("Cập nhật tài xế thất bại: " . $stmt->error);
            }

            echo json_encode([
                'success' => true,
                'message' => 'Tài xế đã được cập nhật thành công'
            ]);
            break;

        case 'list':
            $sql = "SELECT * FROM drivers";
            if (isset($_GET['status'])) {
                $sql .= " WHERE status = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $_GET['status']);
            } else {
                $stmt = $conn->prepare($sql);
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $drivers = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode([
                'success' => true,
                'drivers' => $drivers
            ]);
            break;

        default:
            throw new Exception("Hành động không hợp lệ");
    }

} catch (Exception $e) {
    error_log("Lỗi tài xế: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
    ob_end_flush();
}
?> 