<?php
session_start();
require_once '../../includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'Không tìm thấy bài viết';
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

try {
    $pdo->beginTransaction();
    
    // Get featured image to delete
    $stmt = $pdo->prepare("SELECT featured_image FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    
    if ($post && $post['featured_image']) {
        $filePath = '../../' . $post['featured_image'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
    
    // Delete post
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    
    $pdo->commit();
    $_SESSION['success'] = 'Xóa bài viết thành công';
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
}

header('Location: index.php');
exit;
