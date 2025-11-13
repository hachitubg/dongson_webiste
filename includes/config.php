<?php
// includes/config.php
// Auto-detect environment and load appropriate config

// Detect environment
$isDocker = getenv('DOCKER_ENV') !== false || file_exists('/.dockerenv');
$isProduction = !file_exists('C:/xampp') && !file_exists('/xampp') && !$isDocker;

if ($isProduction) {
    // Production VPS environment
    require_once __DIR__ . '/config.production.php';
} elseif ($isDocker) {
    // Docker environment
    require_once __DIR__ . '/config.docker.php';
} else {
    // Local XAMPP environment
    require_once __DIR__ . '/config.local.php';
}

?>
