<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Bật để gỡ lỗi
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: http://localhost");
header("Access-Control-Allow-Credentials: true");

// Bắt đầu bộ đệm đầu ra
ob_start();

try {
    // Kết nối đến cơ sở dữ liệu trước
    $conn = require_once '../config/database.php';
    
    if (!$conn) {
        throw new Exception("Kết nối với cơ sở dữ liệu thất bại");
    }

    // Kiểm tra phiên từ header.php
    if (!isset($isLoggedIn) || !$isLoggedIn) {
        throw new Exception("Vui lòng đăng nhập để sử dụng dịch vụ");
    }

    // Ghi log gỡ lỗi
    error_log("Dữ liệu POST: " . print_r($_POST, true));
    
    $user_id = $_SESSION['user_id'];

    // Xác thực các trường bắt buộc
    if (empty($_POST['shipping_name'])) throw new Exception("Thiếu tên người nhận");
    if (empty($_POST['shipping_phone'])) throw new Exception("Thiếu số điện thoại người nhận");
    if (empty($_POST['shipping_address'])) throw new Exception("Thiếu địa chỉ người nhận");
    if (empty($_POST['payment_method'])) throw new Exception("Thiếu phương thức thanh toán");

    // Thêm sau khi xác thực các trường bắt buộc
    if (empty($_POST['product_names']) || !is_array($_POST['product_names'])) {
        throw new Exception("Thêm ít nhất một sản phẩm");
    }

    // Ngăn chặn gửi nhanh
    if (isset($_SESSION['last_order_time'])) {
        $timeDiff = time() - $_SESSION['last_order_time'];
        if ($timeDiff < 5) { // Thời gian chờ 5 giây
            echo json_encode([
                'success' => false,
                'isDuplicate' => true // Thêm cờ để xác định gửi trùng
            ]);
            exit;
        }
    }

    $_SESSION['last_order_time'] = time();

    // Bắt đầu giao dịch
    $conn->begin_transaction();

    // Chèn đơn hàng với trạng thái thanh toán mặc định là 'chưa thanh toán'
    $stmt = $conn->prepare("
        INSERT INTO orders (
            user_id, 
            shipping_name, 
            shipping_phone, 
            shipping_address, 
            payment_method,
            notes,
            total_amount,
            payment_status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'unpaid', NOW())
    ");

    $total_amount = 0;
    foreach ($_POST['shipping_fees'] as $fee) {
        $total_amount += floatval($fee);
    }
    
    $stmt->bind_param(
        "isssssd",
        $user_id,
        $_POST['shipping_name'],
        $_POST['shipping_phone'],
        $_POST['shipping_address'],
        $_POST['payment_method'],
        $_POST['notes'],
        $total_amount
    );

    if (!$stmt->execute()) {
        throw new Exception("Tạo đơn hàng thất bại");
    }

    $order_id = $conn->insert_id;

    // Chèn các sản phẩm trong đơn hàng
    $sql = "INSERT INTO order_items (
        order_id, product_name, quantity, length, width, 
        height, weight, shipping_fee
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Chuẩn bị câu lệnh thất bại cho các sản phẩm: " . $conn->error);
    }

    foreach ($_POST['product_names'] as $i => $name) {
        // Tạo biến cho bind_param
        $item_order_id = $order_id;
        $item_name = $name;
        $item_quantity = intval($_POST['quantities'][$i] ?? 1);
        $item_length = floatval($_POST['length'][$i] ?? 0);
        $item_width = floatval($_POST['width'][$i] ?? 0);
        $item_height = floatval($_POST['height'][$i] ?? 0);
        $item_weight = floatval($_POST['weight'][$i] ?? 0);
        $item_shipping_fee = floatval($_POST['shipping_fees'][$i] ?? 0);

        $stmt->bind_param("isiddddd",
            $item_order_id,
            $item_name,
            $item_quantity,
            $item_length,
            $item_width,
            $item_height,
            $item_weight,
            $item_shipping_fee
        );

        if (!$stmt->execute()) {
            throw new Exception("Lưu sản phẩm thất bại: " . $stmt->error);
        }
    }

    // Cam kết giao dịch
    $conn->commit();

    // Xóa bộ đệm đầu ra và gửi phản hồi thành công với chuyển hướng thanh toán
    ob_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Đơn hàng đã được đặt thành công!',
        'order_id' => $order_id,
        'redirect' => "payment.php?id=" . $order_id . 
                     "&payment=" . urlencode($_POST['payment_method']) . 
                     "&amount=" . $total_amount
    ]);

} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    // Ghi log lỗi
    error_log("Lỗi đơn hàng: " . $e->getMessage());
    
    // Xóa bộ đệm đầu ra và gửi phản hồi lỗi
    // Clear output buffer and send error response
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    // Close resources
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
    
    // End output buffering
    ob_end_flush();
}
?> 