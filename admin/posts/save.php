<?php
session_start();
require_once '../../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$isEdit = isset($_POST['id']) && !empty($_POST['id']);
$errors = [];

$title = trim($_POST['title'] ?? '');
$excerpt = trim($_POST['excerpt'] ?? '');
$content = $_POST['content'] ?? '';
$slug = trim($_POST['slug'] ?? '');
$category_id = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? (int)$_POST['category_id'] : null;
$isActive = isset($_POST['is_active']) ? 1 : 0;

// Validation
if (empty($title)) $errors[] = 'Vui lòng nhập tiêu đề';
if (empty($content)) $errors[] = 'Vui lòng nhập nội dung';
if (!$category_id) $errors[] = 'Vui lòng chọn danh mục';

// Auto-generate slug
if (empty($slug)) {
    $slug = slugify($title);
} else {
    $slug = slugify($slug);
}

// Check unique slug
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $_POST['id']]);
} else {
    $stmt = $pdo->prepare("SELECT id FROM posts WHERE slug = ?");
    $stmt->execute([$slug]);
}
if ($stmt->fetch()) {
    $errors[] = 'Slug đã tồn tại';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    header('Location: form.php' . ($isEdit ? '?id=' . $_POST['id'] : ''));
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Handle featured image upload
    $featuredImage = null;
    if (!empty($_FILES['featured_image']['tmp_name'])) {
        $uploadDir = '../../uploads/posts/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $tmpName = $_FILES['featured_image']['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);
        
        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            $extension = pathinfo($_FILES['featured_image']['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid('post_') . '.' . $extension;
            
            if (move_uploaded_file($tmpName, $uploadDir . $newFileName)) {
                $featuredImage = 'uploads/posts/' . $newFileName;
                
                // Delete old image if editing
                if ($isEdit) {
                    $stmt = $pdo->prepare("SELECT featured_image FROM posts WHERE id = ?");
                    $stmt->execute([$_POST['id']]);
                    $oldPost = $stmt->fetch();
                    if ($oldPost && $oldPost['featured_image']) {
                        $oldFile = '../../' . $oldPost['featured_image'];
                        if (file_exists($oldFile)) unlink($oldFile);
                    }
                }
            }
        }
    }
    
    if ($isEdit) {
        // Update
        if ($featuredImage) {
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, slug = ?, category_id = ?, excerpt = ?, content = ?, featured_image = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$title, $slug, $category_id, $excerpt, $content, $featuredImage, $isActive, $_POST['id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, slug = ?, category_id = ?, excerpt = ?, content = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$title, $slug, $category_id, $excerpt, $content, $isActive, $_POST['id']]);
        }
        $message = 'Cập nhật bài viết thành công';
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO posts (title, slug, category_id, excerpt, content, featured_image, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$title, $slug, $category_id, $excerpt, $content, $featuredImage, $isActive]);
        $message = 'Thêm bài viết thành công';
    }
    
    $pdo->commit();
    $_SESSION['success'] = $message;
    header('Location: index.php');
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = 'Có lỗi: ' . $e->getMessage();
    header('Location: form.php' . ($isEdit ? '?id=' . $_POST['id'] : ''));
}

function slugify($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $vietnamese = [
        'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
        'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
        'đ' => 'd'
    ];
    $text = strtr($text, $vietnamese);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}
