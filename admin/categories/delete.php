<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Get category to check type for redirect
$stmt = $pdo->prepare('SELECT type FROM categories WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: index.php');
    exit;
}

$type = $category['type'];

// Check if category is being used
if ($type === 'product') {
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
    $checkStmt->execute([$id]);
    $productCount = $checkStmt->fetchColumn();
    
    if ($productCount > 0) {
        $_SESSION['error'] = "Không thể xóa danh mục này vì đang có {$productCount} sản phẩm sử dụng. Vui lòng chuyển các sản phẩm sang danh mục khác trước.";
        header('Location: index.php?type=' . $type);
        exit;
    }
} else {
    $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM posts WHERE category_id = ?');
    $checkStmt->execute([$id]);
    $postCount = $checkStmt->fetchColumn();
    
    if ($postCount > 0) {
        $_SESSION['error'] = "Không thể xóa danh mục này vì đang có {$postCount} bài viết sử dụng. Vui lòng chuyển các bài viết sang danh mục khác trước.";
        header('Location: index.php?type=' . $type);
        exit;
    }
}

// Delete category
$del = $pdo->prepare('DELETE FROM categories WHERE id = ?');
$del->execute([$id]);

header('Location: index.php?type=' . $type);
exit;
