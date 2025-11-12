<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/config.php';

// Support both AJAX (POST) and legacy GET redirect flow
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $image_id = intval($_POST['image_id'] ?? 0);
    $product_id = intval($_POST['product_id'] ?? 0);
} else {
    $image_id = intval($_GET['image_id'] ?? 0);
    $product_id = intval($_GET['product_id'] ?? 0);
}

if (!$image_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'image_id không hợp lệ']);
        exit;
    }
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT image_path FROM product_images WHERE id = ? LIMIT 1');
$stmt->execute([$image_id]);
$im = $stmt->fetch();
if ($im) {
    $uploadDir = rtrim(UPLOAD_DIR, '\\\/') . DIRECTORY_SEPARATOR . 'products';
    $file = $uploadDir . DIRECTORY_SEPARATOR . $im['image_path'];
    if (is_file($file)) @unlink($file);
    $del = $pdo->prepare('DELETE FROM product_images WHERE id = ?');
    $del->execute([$image_id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}

// Legacy GET redirect for browsers/users who call the URL directly
if ($product_id) header('Location: form.php?id=' . $product_id);
else header('Location: index.php');
exit;
?>