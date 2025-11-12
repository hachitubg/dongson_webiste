<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON data from request body
$input = json_decode(file_get_contents('php://input'), true);

$locationCode = $input['location'] ?? '';
$orderData = $input['order'] ?? [];

if (empty($locationCode) || !is_array($orderData)) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Update sort_order for each banner
    $stmt = $pdo->prepare('UPDATE banners SET sort_order = ? WHERE id = ? AND location_code = ?');
    
    foreach ($orderData as $order => $bannerId) {
        $stmt->execute([$order, $bannerId, $locationCode]);
    }
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Đã lưu thứ tự banner']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
