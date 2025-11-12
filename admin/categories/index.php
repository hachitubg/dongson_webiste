<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../../includes/db.php';

$type = $_GET['type'] ?? 'product';
if (!in_array($type, ['product', 'post'])) {
    $type = 'product';
}

$pageTitle = $type === 'product' ? 'Danh Mục Sản Phẩm' : 'Danh Mục Bài Viết';

// Search and pagination
$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query - Only show parent categories (parent_id IS NULL)
$where = ['c.type = ?', 'c.parent_id IS NULL'];
$params = [$type];

if ($search) {
    $where[] = '(c.name LIKE ? OR c.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = implode(' AND ', $where);

// Get total count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM categories c WHERE $whereClause");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);

// Get parent categories with child count
$stmt = $pdo->prepare("
    SELECT c.*, 
           (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as children_count
    FROM categories c
    WHERE $whereClause 
    ORDER BY c.sort_order ASC, c.name ASC 
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$categories = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/../_head.php'; ?>
  <title><?php echo $pageTitle; ?></title>
  <style>
    .type-tabs {
      display: flex;
      gap: 8px;
      margin-bottom: 24px;
      border-bottom: 1px solid #d2d2d7;
      padding-bottom: 0;
    }
    .type-tab {
      padding: 12px 24px;
      background: none;
      border: none;
      border-bottom: 2px solid transparent;
      color: #86868b;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
    }
    .type-tab:hover {
      color: #1d1d1f;
    }
    .type-tab.active {
      color: #06c;
      border-bottom-color: #06c;
    }
    .category-row {
      cursor: move;
      transition: background-color 0.2s;
    }
    .category-row:hover {
      background-color: #f5f5f7;
    }
    .category-row.dragging {
      opacity: 0.5;
      background-color: #e8e8ed;
    }
    .drag-handle {
      cursor: grab;
      padding: 4px;
      display: inline-flex;
      align-items: center;
      color: #86868b;
    }
    .drag-handle:active {
      cursor: grabbing;
    }
    .order-number {
      font-weight: 600;
      color: #1d1d1f;
      min-width: 30px;
      text-align: center;
    }
    .category-row.is-child {
      background-color: #fafafa;
    }
    .category-row.is-child td:first-child {
      padding-left: 40px;
    }
    .category-name-cell {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .parent-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 8px;
      background: #e8f4fd;
      color: #0066cc;
      border-radius: 4px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
    }
    .child-indicator {
      color: #86868b;
      font-size: 14px;
      margin-right: 4px;
    }
    .save-order-btn {
      position: fixed;
      bottom: 24px;
      right: 24px;
      padding: 12px 24px;
      background: #0071e3;
      color: white;
      border: none;
      border-radius: 12px;
      font-weight: 600;
      box-shadow: 0 4px 12px rgba(0,113,227,0.3);
      cursor: pointer;
      display: none;
      z-index: 1000;
    }
    .save-order-btn:hover {
      background: #0077ed;
    }
    .save-order-btn.show {
      display: block;
      animation: slideUp 0.3s ease;
    }
    @keyframes slideUp {
      from { transform: translateY(100px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>
  <div class="admin-container">
    <?php include __DIR__ . '/../_nav.php'; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
      <div style="background:#fee;border:1px solid #f33;border-radius:8px;padding:16px;margin-bottom:24px">
        <strong style="color:#c00"><?php echo htmlspecialchars($_SESSION['error']); ?></strong>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="type-tabs">
      <a href="?type=product" class="type-tab <?php echo $type === 'product' ? 'active' : ''; ?>">
        <i class="fas fa-box"></i>
        Danh Mục Sản Phẩm
      </a>
      <a href="?type=post" class="type-tab <?php echo $type === 'post' ? 'active' : ''; ?>">
        <i class="fas fa-newspaper"></i>
        Danh Mục Bài Viết
      </a>
    </div>

    <div class="toolbar">
      <div class="toolbar-left">
        <a href="form.php?type=<?php echo $type; ?>" class="btn">
          <i class="fas fa-plus-circle"></i>
          Thêm Danh Mục
        </a>
      </div>
      <div class="toolbar-right">
        <form method="get" class="search-form-compact">
          <input type="hidden" name="type" value="<?php echo $type; ?>">
          <div class="search-group">
            <div class="search-box">
              <i class="fas fa-search search-icon"></i>
              <input type="search" name="search" placeholder="Tìm kiếm danh mục..." value="<?php echo htmlspecialchars($search); ?>" class="form-input">
            </div>
            <button type="submit" class="btn btn-primary">
              Tìm
            </button>
            <?php if ($search): ?>
              <a href="?type=<?php echo $type; ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i>
              </a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <?php if (empty($categories)): ?>
      <div class="empty-state">
        <svg width="64" height="64" viewBox="0 0 16 16" fill="currentColor"><path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/></svg>
        <h2>Chưa có danh mục nào</h2>
        <p>Bắt đầu bằng cách tạo danh mục đầu tiên</p>
        <a href="form.php?type=<?php echo $type; ?>" class="btn">Thêm Danh Mục</a>
      </div>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th style="width:40px">STT</th>
            <th style="width:30px"></th>
            <th>Tên Danh Mục</th>
            <th style="width:150px">Danh Mục Con</th>
            <th>Slug</th>
            <th style="width:120px">Trạng Thái</th>
            <th style="width:150px">Ngày Tạo</th>
            <th style="width:150px">Thao Tác</th>
          </tr>
        </thead>
        <tbody id="categories-tbody">
          <?php foreach ($categories as $index => $cat): ?>
          <tr class="category-row" data-id="<?php echo $cat['id']; ?>" draggable="true">
            <td class="order-number"><?php echo $index + 1; ?></td>
            <td>
              <span class="drag-handle">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                  <path d="M5 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm6 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2zM5 7a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm6 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2zM5 11a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm6 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
              </span>
            </td>
            <td>
              <div class="category-name-cell">
                <div>
                  <strong><?php echo htmlspecialchars($cat['name']); ?></strong>
                  <?php if ($cat['description']): ?>
                    <div class="muted"><?php echo htmlspecialchars(mb_substr($cat['description'], 0, 100)); ?></div>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td>
              <?php if ($type === 'product'): ?>
                <span class="badge" style="background:#e8f4fd;color:#0066cc;font-weight:600">
                  <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" style="vertical-align:middle;margin-right:4px">
                    <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
                  </svg>
                  <?php echo $cat['children_count']; ?> danh mục
                </span>
              <?php else: ?>
                <span style="color:#ccc">—</span>
              <?php endif; ?>
            </td>
            <td><code><?php echo htmlspecialchars($cat['slug']); ?></code></td>
            <td>
              <?php if ($cat['is_active']): ?>
                <span class="badge badge-success">Kích hoạt</span>
              <?php else: ?>
                <span class="badge">Ẩn</span>
              <?php endif; ?>
            </td>
            <td><?php echo date('d/m/Y H:i', strtotime($cat['created_at'])); ?></td>
            <td>
              <div class="action-buttons">
                <a href="form.php?id=<?php echo $cat['id']; ?>" class="btn-small" title="Sửa">
                  <i class="fas fa-edit"></i>
                  Sửa
                </a>
                <a href="delete.php?id=<?php echo $cat['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Xóa danh mục này? Các sản phẩm/bài viết trong danh mục sẽ không bị xóa.')" title="Xóa">
                  <i class="fas fa-trash"></i>
                  Xóa
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php if ($page > 1): ?>
          <a href="?type=<?php echo $type; ?>&page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>" class="btn">« Trang trước</a>
        <?php endif; ?>
        <span class="pagination-info">Trang <?php echo $page; ?> / <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
          <a href="?type=<?php echo $type; ?>&page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>" class="btn">Trang sau »</a>
        <?php endif; ?>
      </div>
      <?php endif; ?>
    <?php endif; ?>
    
    <button class="save-order-btn" id="saveOrderBtn" onclick="saveOrder()">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right:8px;vertical-align:middle">
        <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
      </svg>
      Lưu Thứ Tự
    </button>
  </div>
  
  <script>
  let draggedElement = null;
  let hasChanges = false;
  const currentType = '<?php echo $type; ?>';
  
  document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.category-row');
    const saveBtn = document.getElementById('saveOrderBtn');
    
    rows.forEach(row => {
      row.addEventListener('dragstart', handleDragStart);
      row.addEventListener('dragover', handleDragOver);
      row.addEventListener('drop', handleDrop);
      row.addEventListener('dragend', handleDragEnd);
    });
    
    function handleDragStart(e) {
      draggedElement = this;
      this.classList.add('dragging');
      e.dataTransfer.effectAllowed = 'move';
    }
    
    function handleDragOver(e) {
      if (e.preventDefault) {
        e.preventDefault();
      }
      e.dataTransfer.dropEffect = 'move';
      
      const afterElement = getDragAfterElement(e.currentTarget.parentNode, e.clientY);
      if (afterElement == null) {
        e.currentTarget.parentNode.appendChild(draggedElement);
      } else {
        e.currentTarget.parentNode.insertBefore(draggedElement, afterElement);
      }
      
      return false;
    }
    
    function handleDrop(e) {
      if (e.stopPropagation) {
        e.stopPropagation();
      }
      updateOrderNumbers();
      hasChanges = true;
      saveBtn.classList.add('show');
      return false;
    }
    
    function handleDragEnd(e) {
      this.classList.remove('dragging');
    }
    
    function getDragAfterElement(container, y) {
      const draggableElements = [...container.querySelectorAll('.category-row:not(.dragging)')];
      
      return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        
        if (offset < 0 && offset > closest.offset) {
          return { offset: offset, element: child };
        } else {
          return closest;
        }
      }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
    
    function updateOrderNumbers() {
      const rows = document.querySelectorAll('.category-row');
      rows.forEach((row, index) => {
        const orderCell = row.querySelector('.order-number');
        if (orderCell) {
          orderCell.textContent = index + 1;
        }
      });
    }
  });
  
  function saveOrder() {
    const rows = document.querySelectorAll('.category-row');
    const order = Array.from(rows).map(row => row.dataset.id);
    
    const saveBtn = document.getElementById('saveOrderBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right:8px;vertical-align:middle;animation:spin 1s linear infinite"><circle cx="8" cy="8" r="7" stroke="currentColor" stroke-width="2" fill="none" opacity="0.25"/><path d="M15 8a7 7 0 0 0-7-7" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round"/></svg>Đang lưu...';
    
    fetch('reorder.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ 
        order: order,
        type: currentType 
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        saveBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right:8px;vertical-align:middle"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/></svg>Đã lưu!';
        setTimeout(() => {
          saveBtn.classList.remove('show');
          saveBtn.disabled = false;
          saveBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right:8px;vertical-align:middle"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/></svg>Lưu Thứ Tự';
          hasChanges = false;
        }, 2000);
      } else {
        alert('Lỗi: ' + data.message);
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right:8px;vertical-align:middle"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/></svg>Lưu Thứ Tự';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Có lỗi xảy ra khi lưu thứ tự');
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right:8px;vertical-align:middle"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/></svg>Lưu Thứ Tự';
    });
  }
  </script>
  
  <style>
  @keyframes spin {
    to { transform: rotate(360deg); }
  }
  </style>
  
  <?php include __DIR__ . '/../_footer.php'; ?>
</body>
</html>
