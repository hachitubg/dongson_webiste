<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    $_SESSION['error'] = 'ID không hợp lệ';
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = ?');
    $stmt->execute([$id]);
    
    $_SESSION['success'] = 'Đã xóa tin nhắn thành công';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Lỗi khi xóa tin nhắn: ' . $e->getMessage();
}

header('Location: index.php');
exit;
