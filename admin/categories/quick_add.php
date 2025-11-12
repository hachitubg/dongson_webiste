<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$slug = trim($_POST['slug'] ?? '');
$type = $_POST['type'] ?? 'product';
$is_active = isset($_POST['is_active']) ? 1 : 0;
$sort_order = intval($_POST['sort_order'] ?? 0);

// Validation
if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Tên danh mục không được để trống']);
    exit;
}

if (empty($slug)) {
    // Auto-generate slug
    $slug = slugify($name);
}

if (!in_array($type, ['product', 'post'])) {
    echo json_encode(['success' => false, 'message' => 'Loại danh mục không hợp lệ']);
    exit;
}

// Check unique slug
$slugCheck = $pdo->prepare('SELECT id FROM categories WHERE slug = ? LIMIT 1');
$slugCheck->execute([$slug]);
if ($slugCheck->fetch()) {
    // Make slug unique by appending timestamp
    $slug .= '-' . time();
}

try {
    $stmt = $pdo->prepare('INSERT INTO categories (name, slug, type, is_active, sort_order) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$name, $slug, $type, $is_active, $sort_order]);
    
    $categoryId = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã thêm danh mục thành công',
        'category' => [
            'id' => $categoryId,
            'name' => $name,
            'slug' => $slug,
            'type' => $type
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}

function slugify($s) {
    $vietnamese = [
        'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
        'đ' => 'd',
        'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
    ];
    $s = mb_strtolower($s, 'UTF-8');
    $s = strtr($s, $vietnamese);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    $s = trim($s, '-');
    $s = preg_replace('/-+/', '-', $s);
    if (!$s) return 'cat-' . time();
    return $s;
}
