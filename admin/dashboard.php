<?php
// admin/dashboard.php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../includes/db.php';

// Get some simple stats
$counts = [];
$tables = ['products','posts','banners'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as c FROM `" . $t . "`");
        $row = $stmt->fetch();
        $counts[$t] = $row ? (int)$row['c'] : 0;
    } catch (Exception $e) {
        $counts[$t] = 0;
    }
}

// Get messages count (unread)
try {
    $stmt = $pdo->query("SELECT COUNT(*) as c FROM contact_messages WHERE is_read = 0");
    $row = $stmt->fetch();
    $counts['messages'] = $row ? (int)$row['c'] : 0;
} catch (Exception $e) {
    $counts['messages'] = 0;
}
?>
<!doctype html>
<html lang="vi">
<head>
  <?php include __DIR__ . '/_head.php'; ?>
  <title>Bảng Điều Khiển</title>
</head>
<body>
  <div class="admin-container">
  <?php include __DIR__ . '/_nav.php'; ?>
  
  <h1><i class="fas fa-chart-line"></i> Bảng Điều Khiển</h1>

  <h2>Thống Kê Tổng Quan</h2>
  <div class="stats-grid">
    <div class="stat-card stat-products">
      <div class="stat-icon">
        <i class="fas fa-box"></i>
      </div>
      <div class="stat-content">
        <strong>Sản Phẩm</strong>
        <div class="stat-number"><?php echo $counts['products']; ?></div>
      </div>
    </div>
    <div class="stat-card stat-posts">
      <div class="stat-icon">
        <i class="fas fa-newspaper"></i>
      </div>
      <div class="stat-content">
        <strong>Bài Viết</strong>
        <div class="stat-number"><?php echo $counts['posts']; ?></div>
      </div>
    </div>
    <div class="stat-card stat-banners">
      <div class="stat-icon">
        <i class="fas fa-images"></i>
      </div>
      <div class="stat-content">
        <strong>Banner</strong>
        <div class="stat-number"><?php echo $counts['banners']; ?></div>
      </div>
    </div>
    <div class="stat-card stat-messages <?php echo $counts['messages'] > 0 ? 'has-notification' : ''; ?>">
      <div class="stat-icon">
        <i class="fas fa-envelope"></i>
        <?php if ($counts['messages'] > 0): ?>
        <span class="notification-badge"><?php echo $counts['messages']; ?></span>
        <?php endif; ?>
      </div>
      <div class="stat-content">
        <strong>Tin Nhắn <?php echo $counts['messages'] > 0 ? 'Mới' : ''; ?></strong>
        <div class="stat-number"><?php echo $counts['messages']; ?></div>
      </div>
      <?php if ($counts['messages'] > 0): ?>
          <div class="stat-actions">
      <a href="<?= BASE_URL ?>/admin/messages/" class="stat-link">Xem ngay →</a>
    </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="charts-grid">
    <div class="chart-container">
      <h3><i class="fas fa-chart-bar"></i> Thống Kê Nội Dung</h3>
      <canvas id="contentChart"></canvas>
    </div>
    <div class="chart-container">
      <h3><i class="fas fa-chart-line"></i> Hoạt Động 7 Ngày Qua</h3>
      <canvas id="activityChart"></canvas>
    </div>
  </div>

  <div class="chart-container chart-full">
    <h3><i class="fas fa-chart-area"></i> Xu Hướng Tăng Trưởng</h3>
    <canvas id="growthChart"></canvas>
  </div>

  <section class="quick-actions-section">
    <h3><i class="fas fa-bolt"></i> Hành Động Nhanh</h3>
    <div class="quick-actions">
      <a href="products/form.php" class="btn btn-action">
        <i class="fas fa-plus-circle"></i>
        Thêm Sản Phẩm
      </a>
      <a href="posts/form.php" class="btn btn-action">
        <i class="fas fa-plus-circle"></i>
        Thêm Bài Viết
      </a>
      <a href="banners/form.php" class="btn btn-action">
        <i class="fas fa-plus-circle"></i>
        Thêm Banner
      </a>
      <a href="categories/form.php" class="btn btn-action btn-secondary">
        <i class="fas fa-folder-plus"></i>
        Thêm Danh Mục
      </a>
    </div>
  </section>
  </div>
  
  <script>
    // Content Chart - Doughnut
    const contentCtx = document.getElementById('contentChart').getContext('2d');
    new Chart(contentCtx, {
      type: 'doughnut',
      data: {
        labels: ['Sản Phẩm', 'Bài Viết', 'Banner'],
        datasets: [{
          data: [<?php echo $counts['products']; ?>, <?php echo $counts['posts']; ?>, <?php echo $counts['banners']; ?>],
          backgroundColor: [
            'rgba(0, 122, 255, 0.8)',
            'rgba(52, 199, 89, 0.8)',
            'rgba(255, 149, 0, 0.8)'
          ],
          borderColor: [
            'rgba(0, 122, 255, 1)',
            'rgba(52, 199, 89, 1)',
            'rgba(255, 149, 0, 1)'
          ],
          borderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              padding: 15,
              font: {
                size: 12,
                family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: {
              size: 14
            },
            bodyFont: {
              size: 13
            }
          }
        }
      }
    });

    // Activity Chart - Bar
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
      type: 'bar',
      data: {
        labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
        datasets: [{
          label: 'Lượt xem',
          data: [120, 150, 180, 170, 190, 210, 165],
          backgroundColor: 'rgba(0, 122, 255, 0.6)',
          borderColor: 'rgba(0, 122, 255, 1)',
          borderWidth: 2,
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              font: {
                size: 11
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                size: 11
              }
            }
          }
        },
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12
          }
        }
      }
    });

    // Growth Chart - Line
    const growthCtx = document.getElementById('growthChart').getContext('2d');
    new Chart(growthCtx, {
      type: 'line',
      data: {
        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
        datasets: [
          {
            label: 'Sản Phẩm',
            data: [12, 19, 25, 32, 38, 45, 52, 58, 65, 72, 78, <?php echo $counts['products']; ?>],
            borderColor: 'rgba(0, 122, 255, 1)',
            backgroundColor: 'rgba(0, 122, 255, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3
          },
          {
            label: 'Bài Viết',
            data: [8, 12, 15, 18, 22, 26, 30, 35, 40, 45, 50, <?php echo $counts['posts']; ?>],
            borderColor: 'rgba(52, 199, 89, 1)',
            backgroundColor: 'rgba(52, 199, 89, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
          mode: 'index',
          intersect: false
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)'
            },
            ticks: {
              font: {
                size: 11
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                size: 11
              }
            }
          }
        },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              padding: 15,
              font: {
                size: 12,
                family: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'
              },
              usePointStyle: true,
              pointStyle: 'circle'
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            padding: 12,
            titleFont: {
              size: 14
            },
            bodyFont: {
              size: 13
            }
          }
        }
      }
    });
  </script>

  <?php include __DIR__ . '/_footer.php'; ?>

</body>
</html>