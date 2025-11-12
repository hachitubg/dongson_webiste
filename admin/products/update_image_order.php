<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

$image_id = intval($_POST['image_id'] ?? 0);
$new_order = intval($_POST['new_order'] ?? 0);

if (!$image_id || !$new_order) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
    exit;
}

// Get product_id
$stmt = $pdo->prepare('SELECT product_id FROM product_images WHERE id = ? LIMIT 1');
$stmt->execute([$image_id]);
$img = $stmt->fetch();

if (!$img) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy ảnh']);
    exit;
}

$product_id = $img['product_id'];

// Get all images for this product
$stmt = $pdo->prepare('SELECT id, sort_order FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC');
$stmt->execute([$product_id]);
$images = $stmt->fetchAll();

// Find current position (1-indexed)
$current_pos = 0;
foreach ($images as $idx => $im) {
    if ($im['id'] == $image_id) {
        $current_pos = $idx + 1;
        break;
    }
}

if ($current_pos === $new_order) {
    echo json_encode(['success' => true, 'message' => 'Không có thay đổi']);
    exit;
}

// Reorder array
$images_array = array_values($images);
$moving_image = $images_array[$current_pos - 1];
array_splice($images_array, $current_pos - 1, 1);
array_splice($images_array, $new_order - 1, 0, [$moving_image]);

// Update sort_order for all images
foreach ($images_array as $idx => $im) {
    $upd = $pdo->prepare('UPDATE product_images SET sort_order = ? WHERE id = ?');
    $upd->execute([$idx, $im['id']]);
}

echo json_encode(['success' => true, 'message' => 'Đã cập nhật thứ tự']);
