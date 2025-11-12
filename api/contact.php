<?php
header('Content-Type: application/json; charset=utf-8');

// Allow CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../includes/db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận phương thức POST']);
    exit;
}

try {
    // Get POST data
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validation
    $errors = [];

    if (empty($name)) {
        $errors[] = 'Vui lòng nhập họ và tên';
    } elseif (mb_strlen($name) < 2) {
        $errors[] = 'Họ và tên phải có ít nhất 2 ký tự';
    }

    if (empty($email)) {
        $errors[] = 'Vui lòng nhập email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email không hợp lệ';
    }

    if (empty($phone)) {
        $errors[] = 'Vui lòng nhập số điện thoại';
    } elseif (!preg_match('/^[0-9+\-\s()]{8,20}$/', $phone)) {
        $errors[] = 'Số điện thoại không hợp lệ';
    }

    if (empty($message)) {
        $errors[] = 'Vui lòng nhập nội dung';
    } elseif (mb_strlen($message) < 10) {
        $errors[] = 'Nội dung phải có ít nhất 10 ký tự';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Vui lòng kiểm tra lại thông tin',
            'errors' => $errors
        ]);
        exit;
    }

    // Insert into database
    $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, is_read, created_at) 
            VALUES (:name, :email, :phone, :subject, :message, 0, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':subject' => $subject,
        ':message' => $message
    ]);

    if ($result) {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi trong vòng 24 giờ.'
        ]);
    } else {
        throw new Exception('Không thể lưu dữ liệu');
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Đã xảy ra lỗi khi lưu thông tin. Vui lòng thử lại sau.',
        'error' => $e->getMessage() // Remove in production
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
