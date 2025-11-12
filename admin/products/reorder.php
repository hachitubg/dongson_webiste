<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['order']) && is_array($data['order'])) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare('UPDATE products SET display_order = ? WHERE id = ?');
            
            foreach ($data['order'] as $index => $productId) {
                $stmt->execute([$index, $productId]);
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Đã cập nhật thứ tự sản phẩm']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method không được hỗ trợ']);
}
