<?php
// admin/_head.php - include this inside <head> of admin pages
// Use BASE_URL from includes/config.php to build absolute paths
require_once __DIR__ . '/../includes/config.php';
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" type="image/png" href="<?php echo rtrim(BASE_URL, '/'); ?>/admin/images/logo-GMF.png">
<link rel="stylesheet" href="<?php echo rtrim(BASE_URL, '/'); ?>/admin/assets/css/admin.css">
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- Chart.js for beautiful charts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
