<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Forbidden');
}

$funcNum = $_GET['CKEditorFuncNum'] ?? 0;
$message = '';
$url = '';

if (isset($_FILES['upload']) && $_FILES['upload']['error'] == UPLOAD_ERR_OK) {
    $uploadDir = '../uploads/content/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $tmpName = $_FILES['upload']['tmp_name'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpName);
    finfo_close($finfo);
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    if (in_array($mimeType, $allowedTypes)) {
        $extension = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('content_') . '.' . $extension;
        $destination = $uploadDir . $newFileName;
        
        if (move_uploaded_file($tmpName, $destination)) {
            require_once __DIR__ . '/../includes/config.php';
            $url = UPLOAD_URL . '/content/' . $newFileName;
            $message = 'Upload thành công';
        } else {
            $message = 'Lỗi khi lưu file';
        }
    } else {
        $message = 'File không hợp lệ. Chỉ chấp nhận JPG, PNG, GIF, WebP';
    }
} else {
    $message = 'Không có file được upload';
}

// CKEditor callback
echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
