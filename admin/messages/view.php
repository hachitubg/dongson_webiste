<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$id) {
    header('Location: index.php');
    exit;
}

// Get message
$stmt = $pdo->prepare('SELECT * FROM contact_messages WHERE id = ?');
$stmt->execute([$id]);
$message = $stmt->fetch();

if (!$message) {
    $_SESSION['error'] = 'Không tìm thấy tin nhắn';
    header('Location: index.php');
    exit;
}

// Mark as read
if (!$message['is_read']) {
    $updateStmt = $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?');
    $updateStmt->execute([$id]);
}
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/../_head.php'; ?>
  <title>Chi Tiết Tin Nhắn</title>
</head>
<body>
  <div class="admin-container">
  <?php include __DIR__ . '/../_nav.php'; ?>
  
  <h1><i class="fas fa-envelope-open"></i> Chi Tiết Tin Nhắn</h1>

  <div style="margin-bottom:24px">
    <a href="index.php" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i>
      Quay lại danh sách
    </a>
    <a href="delete.php?id=<?= $message['id'] ?>" class="btn btn-danger" onclick="return confirm('Xóa tin nhắn này?')">
      <i class="fas fa-trash"></i>
      Xóa
    </a>
  </div>

  <div class="message-detail-card">
    <div class="message-detail-header">
      <h2>
        <?= $message['subject'] ? htmlspecialchars($message['subject']) : 'Không có tiêu đề' ?>
      </h2>
      <span class="message-detail-date">
        <i class="fas fa-clock"></i>
        <?= date('d/m/Y H:i:s', strtotime($message['created_at'])) ?>
      </span>
    </div>

    <div class="message-detail-info">
      <div class="info-row">
        <label><i class="fas fa-user"></i> Họ tên:</label>
        <span><?= htmlspecialchars($message['name']) ?></span>
      </div>
      <div class="info-row">
        <label><i class="fas fa-envelope"></i> Email:</label>
        <span><a href="mailto:<?= htmlspecialchars($message['email']) ?>"><?= htmlspecialchars($message['email']) ?></a></span>
      </div>
      <?php if ($message['phone']): ?>
      <div class="info-row">
        <label><i class="fas fa-phone"></i> Số điện thoại:</label>
        <span><a href="tel:<?= htmlspecialchars($message['phone']) ?>"><?= htmlspecialchars($message['phone']) ?></a></span>
      </div>
      <?php endif; ?>
    </div>

    <div class="message-detail-content">
      <h3><i class="fas fa-comment-dots"></i> Nội dung tin nhắn:</h3>
      <div class="message-text">
        <?= nl2br(htmlspecialchars($message['message'])) ?>
      </div>
    </div>

    <div class="message-detail-actions">
      <a href="mailto:<?= htmlspecialchars($message['email']) ?>?subject=Re: <?= urlencode($message['subject'] ?: 'Tin nhắn từ ĐÔNG SƠN') ?>" class="btn">
        <i class="fas fa-reply"></i>
        Trả lời qua Email
      </a>
      <?php if ($message['phone']): ?>
      <a href="tel:<?= htmlspecialchars($message['phone']) ?>" class="btn btn-secondary">
        <i class="fas fa-phone"></i>
        Gọi điện
      </a>
      <?php endif; ?>
    </div>
  </div>

  </div>
  <?php include __DIR__ . '/../_footer.php'; ?>
  
  <style>
  .message-detail-card {
    background: #fff;
    border: 2px solid #e5e5e7;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
  }
  .message-detail-header {
    border-bottom: 2px solid #f5f5f7;
    padding-bottom: 20px;
    margin-bottom: 24px;
  }
  .message-detail-header h2 {
    color: #1d1d1f;
    font-size: 24px;
    margin: 0 0 12px 0;
  }
  .message-detail-date {
    color: #86868b;
    font-size: 14px;
  }
  .message-detail-info {
    background: #f5f5f7;
    padding: 24px;
    border-radius: 12px;
    margin-bottom: 24px;
  }
  .info-row {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
  }
  .info-row:last-child {
    margin-bottom: 0;
  }
  .info-row label {
    min-width: 150px;
    font-weight: 600;
    color: #1d1d1f;
    margin: 0;
  }
  .info-row label i {
    color: #06c;
    margin-right: 8px;
  }
  .info-row span {
    flex: 1;
    color: #333;
  }
  .info-row a {
    color: #06c;
    text-decoration: none;
  }
  .info-row a:hover {
    text-decoration: underline;
  }
  .message-detail-content {
    margin-bottom: 32px;
  }
  .message-detail-content h3 {
    color: #1d1d1f;
    font-size: 18px;
    margin-bottom: 16px;
  }
  .message-detail-content h3 i {
    color: #ff9500;
    margin-right: 8px;
  }
  .message-text {
    background: #fafafa;
    padding: 24px;
    border-radius: 12px;
    border: 1px solid #e5e5e7;
    color: #333;
    font-size: 15px;
    line-height: 1.8;
    white-space: pre-wrap;
  }
  .message-detail-actions {
    display: flex;
    gap: 12px;
    padding-top: 24px;
    border-top: 2px solid #f5f5f7;
  }
  @media (max-width: 768px) {
    .message-detail-card {
      padding: 20px;
    }
    .info-row {
      flex-direction: column;
      gap: 8px;
    }
    .info-row label {
      min-width: auto;
    }
    .message-detail-actions {
      flex-direction: column;
    }
  }
  </style>
</body>
</html>
