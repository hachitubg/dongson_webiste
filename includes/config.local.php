<?php
// includes/config.local.php
// Configuration for Local XAMPP environment

// Database connection
define('DB_HOST', 'localhost');
define('DB_NAME', 'dongson_website');
define('DB_USER', 'root');
define('DB_PASS', ''); // XAMPP mặc định không có password

// Base URL cho XAMPP
define('BASE_URL', '/dongson_webiste');

define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

?>
