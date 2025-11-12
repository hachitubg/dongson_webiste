<?php
// includes/config.php
// Auto-detect environment and load appropriate config

// Detect environment
$isDocker = getenv('DOCKER_ENV') !== false || file_exists('/.dockerenv');

if ($isDocker) {
    // Docker environment
    require_once __DIR__ . '/config.docker.php';
} else {
    // Local XAMPP environment
    require_once __DIR__ . '/config.local.php';
}

?>
