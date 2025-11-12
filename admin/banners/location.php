<?php
session_start();
require_once '../../includes/db.php';
require_once __DIR__ . '/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$locationCode = isset($_GET['code']) ? trim($_GET['code']) : '';
$allLocations = getAllBannerLocations();

// Validate location code
if (empty($locationCode) || !isset($allLocations[$locationCode])) {
    $_SESSION['error'] = 'Vị trí không hợp lệ';
    header('Location: index.php');
    exit;
}

$locationName = $allLocations[$locationCode];

// Get all banners for this location
$stmt = $pdo->prepare('SELECT * FROM banners WHERE location_code = ? ORDER BY sort_order ASC, id ASC');
$stmt->execute([$locationCode]);
$banners = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <?php include __DIR__ . '/../_head.php'; ?>
  <title><?= htmlspecialchars($locationName) ?> - Banners</title>
  <style>
    .banner-row {
      cursor: move;
      transition: background-color 0.2s;
    }
    .banner-row:hover {
      background-color: #f5f5f7;
    }
    .banner-row.dragging {
      opacity: 0.5;
      background-color: #e8e8ed;
    }
    .drag-handle {
      cursor: grab;
      padding: 8px;
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
      font-size: 18px;
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
  <h1><?= htmlspecialchars($locationName) ?></h1>
  <?php include __DIR__ . '/../_nav.php'; ?>

  <?php if (isset($_SESSION['success'])): ?>
    <p style="color:#34c759;background:#d9f7e5;padding:12px;border-radius:8px;border:1px solid #a3e9c4"><?= htmlspecialchars($_SESSION['success']) ?></p>
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <p style="color:#ff3b30;background:#fee;padding:12px;border-radius:8px;border:1px solid #fcc"><?= htmlspecialchars($_SESSION['error']) ?></p>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px">
    <a href="index.php" class="btn">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 0L6.59 1.41 12.17 7H0v2h12.17l-5.58 5.59L8 16l8-8z" transform="rotate(180 8 8)"/></svg>
      Quay lại danh sách vị trí
    </a>
    
    <a href="form.php?location=<?= urlencode($locationCode) ?>" class="btn">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
      Thêm Banner
    </a>
  </div>

  <?php if (empty($banners)): ?>
    <div style="text-align:center;padding:60px 20px;background:#f5f5f7;border-radius:12px">
      <svg width="64" height="64" viewBox="0 0 16 16" fill="#d2d2d7" style="margin-bottom:16px">
        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
        <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
      </svg>
      <p style="color:#86868b;font-size:16px;margin-bottom:16px">
        Chưa có banner nào cho vị trí này
      </p>
      <a href="form.php?location=<?= urlencode($locationCode) ?>" class="btn">Thêm Banner Đầu Tiên</a>
    </div>
  <?php else: ?>
    <div id="banner-message" style="display:none;padding:12px;border-radius:8px;margin-bottom:16px"></div>
    
    <table id="banners-table">
      <thead>
        <tr>
          <th style="width:40px">STT</th>
          <th style="width:30px"></th>
          <th style="width:200px">Ảnh Banner</th>
          <th>Link URL</th>
          <th style="width:120px;text-align:center">Trạng Thái</th>
          <th style="width:150px;text-align:center">Hành Động</th>
        </tr>
      </thead>
      <tbody id="sortable-banners">
        <?php foreach ($banners as $index => $banner): ?>
        <tr class="banner-row" data-id="<?= $banner['id'] ?>" draggable="true">
          <td class="order-number"><?= $index + 1 ?></td>
          <td>
            <span class="drag-handle">
              <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M5 3a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm6 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2zM5 7a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm6 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2zM5 11a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm6 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
              </svg>
            </span>
          </td>
          <td>
            <img src="../../<?= htmlspecialchars($banner['image_path']) ?>" alt="" 
                 style="width:100%;max-width:200px;height:auto;border-radius:8px;border:1px solid #d2d2d7">
          </td>
          <td>
            <?php if ($banner['link_url']): ?>
              <a href="<?= htmlspecialchars($banner['link_url']) ?>" target="_blank" 
                 style="color:#06c;text-decoration:none;word-break:break-all;font-size:14px">
                <?= htmlspecialchars($banner['link_url']) ?>
                <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor" style="margin-left:4px">
                  <path d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5z"/>
                  <path d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0v-5z"/>
                </svg>
              </a>
            <?php else: ?>
              <span style="color:#86868b;font-size:14px">Không có link</span>
            <?php endif; ?>
          </td>
          <td style="text-align:center">
            <?php if ($banner['is_active']): ?>
              <span style="background:#d9f7e5;color:#34c759;padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;display:inline-block">
                ✓ Hiển thị
              </span>
            <?php else: ?>
              <span style="background:#f5f5f7;color:#86868b;padding:6px 12px;border-radius:6px;font-size:13px;font-weight:500;display:inline-block">
                ✕ Ẩn
              </span>
            <?php endif; ?>
          </td>
          <td style="text-align:center">
            <a href="form.php?id=<?= $banner['id'] ?>&location=<?= urlencode($locationCode) ?>" 
               style="color:#06c;text-decoration:none;font-size:14px">Sửa</a>
            <span style="color:#d2d2d7;margin:0 8px">|</span>
            <a href="delete.php?id=<?= $banner['id'] ?>&location=<?= urlencode($locationCode) ?>" 
               onclick="return confirm('Bạn có chắc muốn xóa banner này?')" 
               style="color:#ff3b30;text-decoration:none;font-size:14px">Xóa</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <button class="save-order-btn" id="saveOrderBtn" onclick="saveOrder()">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" style="margin-right:8px;vertical-align:middle">
        <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
      </svg>
      Lưu Thứ Tự
    </button>

    <div style="margin-top:24px;padding:16px;background:#f5f5f7;border-radius:8px">
      <p style="margin:0;font-size:14px;color:#86868b">
        <strong style="color:#1d1d1f">Tổng số:</strong> <?= count($banners) ?> banner
        <span style="margin:0 8px">•</span>
        <strong style="color:#1d1d1f">Đang hiển thị:</strong> <?= count(array_filter($banners, fn($b) => $b['is_active'])) ?> banner
      </p>
    </div>

    <script>
    let draggedElement = null;
    let hasChanges = false;
    const locationCode = <?= json_encode($locationCode) ?>;
    
    document.addEventListener('DOMContentLoaded', function() {
      const rows = document.querySelectorAll('.banner-row');
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
        const draggableElements = [...container.querySelectorAll('.banner-row:not(.dragging)')];
        
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
        const rows = document.querySelectorAll('.banner-row');
        rows.forEach((row, index) => {
          const orderCell = row.querySelector('.order-number');
          if (orderCell) {
            orderCell.textContent = index + 1;
          }
        });
      }
    });
    
    function saveOrder() {
      const rows = document.querySelectorAll('.banner-row');
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
          location: locationCode 
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
  <?php endif; ?>

  </div>
  <?php include __DIR__ . '/../_footer.php'; ?>
</body>
</html>
