<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['order']) && is_array($data['order']) && isset($data['type'])) {
        $type = $data['type'];
        
        if (!in_array($type, ['product', 'post'])) {
            echo json_encode(['success' => false, 'message' => 'Loại danh mục không hợp lệ']);
            exit;
        }
        
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare('UPDATE categories SET sort_order = ? WHERE id = ? AND type = ?');
            
            foreach ($data['order'] as $index => $categoryId) {
                $stmt->execute([$index, $categoryId, $type]);
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Đã cập nhật thứ tự danh mục']);
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
