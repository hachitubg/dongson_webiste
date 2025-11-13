<?php
// includes/config.production.php
// Configuration for Production VPS environment

// Database connection - Thông tin database production
define('DB_HOST', 'localhost');
define('DB_NAME', 'dongson_website');
define('DB_USER', 'dongson_user');
define('DB_PASS', 'Dongson@2024#VPS'); // Password cho production database

// Base URL - Để trống khi chạy trực tiếp trên IP hoặc domain root
define('BASE_URL', '');

define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Production settings
ini_set('display_errors', 0); // Tắt hiển thị lỗi trên production
ini_set('log_errors', 1);
error_reporting(E_ALL);

?>
