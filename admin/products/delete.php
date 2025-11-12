<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

// get images to delete files
$stmt = $pdo->prepare('SELECT image_path FROM product_images WHERE product_id = ?');
$stmt->execute([$id]);
$images = $stmt->fetchAll();
$uploadDir = rtrim(UPLOAD_DIR, '\\\/') . DIRECTORY_SEPARATOR . 'products';
foreach ($images as $im) {
    $file = $uploadDir . DIRECTORY_SEPARATOR . $im['image_path'];
    if (is_file($file)) @unlink($file);
}

// delete product (product_images rows will cascade if FK set)
$del = $pdo->prepare('DELETE FROM products WHERE id = ?');
$del->execute([$id]);

header('Location: index.php');
exit;
?>