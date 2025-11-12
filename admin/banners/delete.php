<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/config.php';

$id = intval($_GET['id'] ?? 0);
$locationCode = isset($_GET['location']) ? trim($_GET['location']) : '';

if (!$id) {
    header('Location: index.php');
    exit;
}

// Get banner info to delete image file and get location
$stmt = $pdo->prepare('SELECT image_path, location_code FROM banners WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$banner = $stmt->fetch();

if ($banner) {
    // Use location from banner if not provided in URL
    if (empty($locationCode)) {
        $locationCode = $banner['location_code'];
    }
    
    // Delete image file
    if (!empty($banner['image_path'])) {
        $file = __DIR__ . '/../../' . $banner['image_path'];
        if (is_file($file)) {
            @unlink($file);
        }
    }
}

// Delete banner record
$del = $pdo->prepare('DELETE FROM banners WHERE id = ?');
$del->execute([$id]);

$_SESSION['success'] = 'Đã xóa banner';

// Redirect back to location page if we have the code
if (!empty($locationCode)) {
    header('Location: location.php?code=' . urlencode($locationCode));
} else {
    header('Location: index.php');
}
exit;
