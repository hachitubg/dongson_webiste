<?php
// includes/config.docker.php
// Configuration for Docker environment

// Database connection (sử dụng environment variables từ Docker)
define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'dongson_website');
define('DB_USER', getenv('DB_USER') ?: 'dongson_user');
define('DB_PASS', getenv('DB_PASS') ?: 'dongson_password_2024');

// Base URL - để trống hoặc '/' khi chạy trên domain riêng
define('BASE_URL', '');

define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

?>
