<?php
// admin/_auth.php
// Include this at top of admin pages to require login.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user_id'])) {
    // Redirect to login (relative path)
    header('Location: login.php');
    exit;
}
?>