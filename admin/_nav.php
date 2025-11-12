<?php
// admin/_nav.php
require_once __DIR__ . '/../includes/config.php';
$adminBase = BASE_URL . '/admin';
?>
<div class="admin-header">
  <div class="admin-logo">
    <img src="<?php echo rtrim(BASE_URL, '/'); ?>/admin/images/logo.png" alt="ĐÔNG SƠN Logo">
    <span>ĐÔNG SƠN ADMIN - QUẢN TRỊ HỆ THỐNG</span>
  </div>
</div>
<nav class="admin-nav">
  <a href="<?= $adminBase ?>/dashboard.php" class="nav-link">
    <i class="fas fa-home"></i>
    <span>Trang Chủ</span>
  </a>
  <a href="<?= $adminBase ?>/products/index.php" class="nav-link">
    <i class="fas fa-box"></i>
    <span>Sản Phẩm</span>
  </a>
  <a href="<?= $adminBase ?>/posts/index.php" class="nav-link">
    <i class="fas fa-newspaper"></i>
    <span>Bài Viết</span>
  </a>
  <a href="<?= $adminBase ?>/categories/index.php" class="nav-link">
    <i class="fas fa-folder"></i>
    <span>Danh Mục</span>
  </a>
  <a href="<?= $adminBase ?>/banners/index.php" class="nav-link">
    <i class="fas fa-images"></i>
    <span>Banner</span>
  </a>
  <a href="<?= $adminBase ?>/messages/index.php" class="nav-link">
    <i class="fas fa-envelope"></i>
    <span>Tin Nhắn</span>
  </a>
  <a href="<?= $adminBase ?>/logout.php" class="nav-link logout">
    <i class="fas fa-sign-out-alt"></i>
    <span>Đăng Xuất</span>
  </a>
</nav>
