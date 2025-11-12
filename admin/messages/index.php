<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

// Get filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$where = ['1=1'];
$params = [];

if ($filter === 'unread') {
    $where[] = 'is_read = 0';
} elseif ($filter === 'read') {
    $where[] = 'is_read = 1';
}

if ($search) {
    $where[] = '(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)';
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereClause = implode(' AND ', $where);

// Get total count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM contact_messages WHERE $whereClause");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Get messages
$stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE $whereClause ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$messages = $stmt->fetchAll();

// Get counts for filters
$unreadCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$totalCount = $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn();
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/../_head.php'; ?>
  <title>Tin Nhắn Liên Hệ</title>
</head>
<body>
  <div class="admin-container">
  <?php include __DIR__ . '/../_nav.php'; ?>
  
  <h1><i class="fas fa-envelope"></i> Tin Nhắn Liên Hệ</h1>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <i class="fas fa-check-circle"></i>
      <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <div class="toolbar">
    <div class="toolbar-left">
      <div class="filter-tabs">
        <a href="?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
          <i class="fas fa-inbox"></i>
          Tất cả (<?= $totalCount ?>)
        </a>
        <a href="?filter=unread" class="filter-tab <?= $filter === 'unread' ? 'active' : '' ?>">
          <i class="fas fa-envelope"></i>
          Chưa đọc (<?= $unreadCount ?>)
        </a>
        <a href="?filter=read" class="filter-tab <?= $filter === 'read' ? 'active' : '' ?>">
          <i class="fas fa-envelope-open"></i>
          Đã đọc
        </a>
      </div>
    </div>
    
    <div class="toolbar-right">
      <form method="get" class="search-form-compact">
        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
        <div class="search-group">
          <div class="search-box">
            <i class="fas fa-search search-icon"></i>
            <input type="text" name="search" placeholder="Tìm kiếm tin nhắn..." value="<?= htmlspecialchars($search) ?>" class="form-input">
          </div>
          <button type="submit" class="btn btn-primary">
            Tìm
          </button>
          <?php if ($search): ?>
            <a href="?filter=<?= $filter ?>" class="btn btn-secondary">
              <i class="fas fa-times"></i>
            </a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>

  <?php if (empty($messages)): ?>
    <div class="empty-state">
      <svg width="64" height="64" viewBox="0 0 16 16" fill="currentColor">
        <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z"/>
      </svg>
      <h2>Không có tin nhắn nào</h2>
      <p><?= $search ? 'Không tìm thấy tin nhắn phù hợp' : 'Chưa có tin nhắn liên hệ nào' ?></p>
    </div>
  <?php else: ?>
    <div class="messages-list">
      <?php foreach ($messages as $msg): ?>
      <div class="message-item <?= !$msg['is_read'] ? 'unread' : '' ?>">
        <div class="message-header">
          <div class="message-meta">
            <?php if (!$msg['is_read']): ?>
              <span class="badge badge-info">Mới</span>
            <?php endif; ?>
            <strong class="message-name"><?= htmlspecialchars($msg['name']) ?></strong>
            <span class="message-email">&lt;<?= htmlspecialchars($msg['email']) ?>&gt;</span>
            <?php if ($msg['phone']): ?>
              <span class="message-phone">
                <i class="fas fa-phone"></i> <?= htmlspecialchars($msg['phone']) ?>
              </span>
            <?php endif; ?>
          </div>
          <div class="message-actions">
            <span class="message-date">
              <i class="fas fa-clock"></i>
              <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?>
            </span>
            <a href="view.php?id=<?= $msg['id'] ?>" class="btn-small" title="Xem chi tiết">
              <i class="fas fa-eye"></i>
              Xem
            </a>
            <a href="delete.php?id=<?= $msg['id'] ?>" class="btn-small btn-danger" onclick="return confirm('Xóa tin nhắn này?')" title="Xóa">
              <i class="fas fa-trash"></i>
            </a>
          </div>
        </div>
        <?php if ($msg['subject']): ?>
          <div class="message-subject">
            <i class="fas fa-tag"></i>
            <?= htmlspecialchars($msg['subject']) ?>
          </div>
        <?php endif; ?>
        <div class="message-preview">
          <?= htmlspecialchars(mb_substr($msg['message'], 0, 150)) ?><?= mb_strlen($msg['message']) > 150 ? '...' : '' ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?filter=<?= $filter ?>&page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">
            <i class="fas fa-chevron-left"></i>
            Trước
          </a>
        <?php endif; ?>
        
        <span class="pagination-info">Trang <?= $page ?> / <?= $totalPages ?></span>
        
        <?php if ($page < $totalPages): ?>
          <a href="?filter=<?= $filter ?>&page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">
            Sau
            <i class="fas fa-chevron-right"></i>
          </a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  </div>
  <?php include __DIR__ . '/../_footer.php'; ?>
  
  <style>
  .filter-tabs {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .filter-tab {
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    color: #1d1d1f;
    background: #f5f5f7;
    font-weight: 500;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .filter-tab:hover {
    background: #e5e5e7;
  }
  .filter-tab.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
  }
  .messages-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .message-item {
    background: #fff;
    border: 2px solid #e5e5e7;
    border-radius: 12px;
    padding: 20px;
    transition: all 0.3s;
  }
  .message-item.unread {
    border-color: #06c;
    background: #f0f8ff;
  }
  .message-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
  }
  .message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    gap: 16px;
  }
  .message-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .message-name {
    color: #1d1d1f;
    font-size: 16px;
  }
  .message-email {
    color: #06c;
    font-size: 14px;
  }
  .message-phone {
    color: #86868b;
    font-size: 14px;
  }
  .message-actions {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .message-date {
    color: #86868b;
    font-size: 13px;
    margin-right: 8px;
  }
  .message-subject {
    color: #1d1d1f;
    font-weight: 500;
    margin-bottom: 8px;
    font-size: 15px;
  }
  .message-subject i {
    color: #ff9500;
    margin-right: 6px;
  }
  .message-preview {
    color: #666;
    font-size: 14px;
    line-height: 1.6;
  }
  </style>
</body>
</html>
