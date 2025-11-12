<?php
session_start();
require_once '../../includes/db.php';
require_once __DIR__ . '/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get banner counts for each location
$allLocations = getAllBannerLocations();
$locationStats = [];

foreach ($allLocations as $code => $name) {
    $stmt = $pdo->prepare('SELECT COUNT(*) as total, SUM(is_active) as active FROM banners WHERE location_code = ?');
    $stmt->execute([$code]);
    $stats = $stmt->fetch();
    $locationStats[$code] = [
        'name' => $name,
        'total' => (int)$stats['total'],
        'active' => (int)$stats['active']
    ];
}
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/../_head.php'; ?>
  <title>Quản Lý Banners - Admin</title>
  <style>
    .location-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-top: 24px;
    }
    .location-card {
      background: #fff;
      border: 2px solid #d2d2d7;
      border-radius: 12px;
      padding: 24px;
      text-decoration: none;
      color: inherit;
      transition: all 0.2s;
      cursor: pointer;
    }
    .location-card:hover {
      border-color: #06c;
      box-shadow: 0 4px 12px rgba(0,102,204,0.15);
      transform: translateY(-2px);
    }
    .location-icon {
      width: 48px;
      height: 48px;
      background: #f5f5f7;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
    }
    .location-name {
      font-size: 18px;
      font-weight: 600;
      color: #1d1d1f;
      margin-bottom: 8px;
    }
    .location-stats {
      font-size: 14px;
      color: #86868b;
    }
    .location-badge {
      display: inline-block;
      background: #06c;
      color: white;
      padding: 4px 10px;
      border-radius: 12px;
      font-size: 13px;
      font-weight: 500;
      margin-top: 8px;
    }
    .location-badge.empty {
      background: #f5f5f7;
      color: #86868b;
    }
  </style>
</head>
<body>
  <div class="admin-container">
  <?php include __DIR__ . '/../_nav.php'; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <p style="color:#34c759;background:#d9f7e5;padding:12px;border-radius:8px;border:1px solid #a3e9c4"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <p style="color:#ff3b30;background:#fee;padding:12px;border-radius:8px;border:1px solid #fcc"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
  
  <p style="color:#86868b;margin-bottom:24px">
    Chọn vị trí hiển thị để quản lý banner
  </p>

  <div class="location-grid">
    <?php foreach ($locationStats as $code => $stat): ?>
    <a href="location.php?code=<?= urlencode($code) ?>" class="location-card">
      <div class="location-icon">
        <svg width="24" height="24" viewBox="0 0 16 16" fill="#06c">
          <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
          <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
        </svg>
      </div>
      <div class="location-name"><?= htmlspecialchars($stat['name']) ?></div>
      <div class="location-stats">
        <?php if ($stat['total'] > 0): ?>
          <?= $stat['total'] ?> banner • <?= $stat['active'] ?> đang hiển thị
        <?php else: ?>
          Chưa có banner
        <?php endif; ?>
      </div>
      <span class="location-badge <?= $stat['total'] == 0 ? 'empty' : '' ?>">
        <?= $stat['total'] > 0 ? 'Xem chi tiết →' : 'Thêm banner →' ?>
      </span>
    </a>
    <?php endforeach; ?>
  </div>

  </div>
  <?php include __DIR__ . '/../_footer.php'; ?>
</body>
</html>
