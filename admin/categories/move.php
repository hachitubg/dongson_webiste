<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$direction = $_POST['direction'] ?? '';
$type = $_POST['type'] ?? '';

if (!$id || !in_array($direction, ['up', 'down']) || !in_array($type, ['product', 'post'])) {
    echo json_encode(['success' => false, 'message' => 'Tham số không hợp lệ']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Get current category
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ? AND type = ? LIMIT 1');
    $stmt->execute([$id, $type]);
    $current = $stmt->fetch();
    
    if (!$current) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy danh mục']);
        exit;
    }
    
    $currentOrder = $current['sort_order'];
    
    // Find the category to swap with
    if ($direction === 'up') {
        // Find previous category (smaller sort_order)
        $swapStmt = $pdo->prepare('SELECT * FROM categories WHERE type = ? AND sort_order < ? ORDER BY sort_order DESC LIMIT 1');
        $swapStmt->execute([$type, $currentOrder]);
    } else {
        // Find next category (larger sort_order)
        $swapStmt = $pdo->prepare('SELECT * FROM categories WHERE type = ? AND sort_order > ? ORDER BY sort_order ASC LIMIT 1');
        $swapStmt->execute([$type, $currentOrder]);
    }
    
    $swap = $swapStmt->fetch();
    
    if (!$swap) {
        echo json_encode(['success' => false, 'message' => 'Không thể di chuyển thêm']);
        exit;
    }
    
    $swapOrder = $swap['sort_order'];
    
    // Swap the sort_order values
    $updateCurrent = $pdo->prepare('UPDATE categories SET sort_order = ? WHERE id = ?');
    $updateCurrent->execute([$swapOrder, $current['id']]);
    
    $updateSwap = $pdo->prepare('UPDATE categories SET sort_order = ? WHERE id = ?');
    $updateSwap->execute([$currentOrder, $swap['id']]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => $direction === 'up' ? 'Đã di chuyển lên' : 'Đã di chuyển xuống'
    ]);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
