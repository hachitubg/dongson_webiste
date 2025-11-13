<?php 
$page = 'properties';
$page_title = 'Đông Sơn Export - Sản phẩm';
include 'includes/header.php'; 
include 'includes/db.php';

// Enable error display for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pagination settings
$limit = 12; // Products per page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $limit;

// Get filter category
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : 0;

try {
  // Get all categories with parent/child structure
  $categoriesStmt = $pdo->query('
    SELECT id, name, slug, parent_id
    FROM categories 
    WHERE type = "product" AND is_active = 1
    ORDER BY parent_id IS NULL DESC, parent_id ASC, sort_order ASC
  ');
  $allCategories = $categoriesStmt->fetchAll();
  
  // Organize into parent/child structure
  $parentCategories = [];
  $childCategories = [];
  foreach ($allCategories as $cat) {
    if ($cat['parent_id'] === null) {
      $parentCategories[] = $cat;
    } else {
      if (!isset($childCategories[$cat['parent_id']])) {
        $childCategories[$cat['parent_id']] = [];
      }
      $childCategories[$cat['parent_id']][] = $cat;
    }
  }
  
  // Get current category name for display
  $currentCategoryName = 'Tất cả sản phẩm';
  if ($category_filter > 0) {
    $currentCatStmt = $pdo->prepare('SELECT name FROM categories WHERE id = ?');
    $currentCatStmt->execute([$category_filter]);
    $currentCat = $currentCatStmt->fetch();
    if ($currentCat) {
      $currentCategoryName = $currentCat['name'];
    }
  }

  // Build query based on filter
  $whereClause = '';
  $params = [];

  if ($category_filter > 0) {
    // Get category and its children
    $categoryIdsStmt = $pdo->prepare('
      SELECT id FROM categories 
      WHERE id = ? OR parent_id = ?
    ');
    $categoryIdsStmt->execute([$category_filter, $category_filter]);
    $categoryIds = $categoryIdsStmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($categoryIds)) {
      $placeholders = str_repeat('?,', count($categoryIds) - 1) . '?';
      $whereClause = " AND p.category_id IN ($placeholders)";
      $params = $categoryIds;
    }
  }

  // Get total count for pagination
  $countQuery = "SELECT COUNT(*) FROM products p WHERE 1=1 $whereClause";
  $countStmt = $pdo->prepare($countQuery);
  $countStmt->execute($params);
  $total_products = $countStmt->fetchColumn();
  $total_pages = ceil($total_products / $limit);

  // Get products with pagination
  $productQuery = "
    SELECT p.*, pi.image_path, c.name as category_name
    FROM products p
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.sort_order = 0
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE 1=1 $whereClause
    ORDER BY p.display_order ASC, p.created_at DESC
    LIMIT $limit OFFSET $offset
  ";
  $productStmt = $pdo->prepare($productQuery);
  $productStmt->execute($params);
  $products = $productStmt->fetchAll();
} catch (PDOException $e) {
  echo "<div style='background: red; color: white; padding: 20px; margin: 20px;'>";
  echo "<h3>Database Error:</h3>";
  echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
  echo "</div>";
  $parentCategories = [];
  $products = [];
  $total_pages = 0;
}
?>

<link rel="stylesheet" href="assets/css/properties.css">

  <div class="page-heading header-text">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <span class="breadcrumb"><a href="index.php">Trang Chủ</a> / Sản Phẩm</span>
          <h3>Sản Phẩm Xuất Khẩu</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="section properties" style="margin-top: 50px;">
    <div class="container">
      <!-- Category Dropdown Filter - 2 Levels -->
      <div class="category-filter-wrapper">
        <!-- Parent Category Dropdown -->
        <div class="category-dropdown">
          <button class="dropdown-toggle" onclick="toggleDropdown('parentDropdown')">
            <span id="selectedParent">
              <?php 
              if ($category_filter > 0) {
                // Get parent of selected category
                $selectedCatStmt = $pdo->prepare('
                  SELECT c1.id, c1.name, c2.name as parent_name
                  FROM categories c1
                  LEFT JOIN categories c2 ON c1.parent_id = c2.id
                  WHERE c1.id = ?
                ');
                $selectedCatStmt->execute([$category_filter]);
                $selectedCat = $selectedCatStmt->fetch();
                if ($selectedCat && $selectedCat['parent_name']) {
                  echo htmlspecialchars($selectedCat['parent_name']);
                } else {
                  echo htmlspecialchars($selectedCat['name'] ?? 'Chọn danh mục');
                }
              } else {
                echo 'Chọn danh mục';
              }
              ?>
            </span>
          </button>
          <div class="dropdown-menu" id="parentDropdown">
            <?php foreach ($parentCategories as $parent): ?>
              <a href="properties.php?category=<?php echo $parent['id']; ?>" 
                 class="dropdown-item <?php 
                   // Active if this parent is selected OR if child of this parent is selected
                   $isActive = false;
                   if ($category_filter == $parent['id']) {
                     $isActive = true;
                   } elseif ($category_filter > 0 && isset($childCategories[$parent['id']])) {
                     foreach ($childCategories[$parent['id']] as $child) {
                       if ($child['id'] == $category_filter) {
                         $isActive = true;
                         break;
                       }
                     }
                   }
                   echo $isActive ? 'active' : '';
                 ?>"
                 data-parent-id="<?php echo $parent['id']; ?>"
                 data-has-children="<?php echo isset($childCategories[$parent['id']]) ? '1' : '0'; ?>">
                <?php echo htmlspecialchars($parent['name']); ?>
                <?php if (isset($childCategories[$parent['id']])): ?>
                  <i class="fa fa-angle-right" style="float: right; opacity: 0.5;"></i>
                <?php endif; ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Child Category Dropdown (shown when parent has children) -->
        <?php 
        $showChildDropdown = false;
        $selectedParentId = 0;
        $selectedChildName = 'Chọn danh mục con';
        
        if ($category_filter > 0) {
          // Check if selected category is a child
          foreach ($childCategories as $parentId => $children) {
            foreach ($children as $child) {
              if ($child['id'] == $category_filter) {
                $showChildDropdown = true;
                $selectedParentId = $parentId;
                $selectedChildName = $child['name'];
                break 2;
              }
            }
          }
          
          // Or if selected category is a parent with children
          if (!$showChildDropdown && isset($childCategories[$category_filter])) {
            $showChildDropdown = true;
            $selectedParentId = $category_filter;
            $selectedChildName = 'Tất cả ' . $currentCategoryName;
          }
        }
        ?>
        
        <div class="category-dropdown child-dropdown" id="childDropdownWrapper" 
             style="<?php echo $showChildDropdown ? '' : 'display: none;'; ?>">
          <button class="dropdown-toggle" onclick="toggleDropdown('childDropdown')">
            <span id="selectedChild"><?php echo htmlspecialchars($selectedChildName); ?></span>
          </button>
          <div class="dropdown-menu" id="childDropdown">
            <!-- Will be populated by JavaScript or PHP based on parent selection -->
            <?php if ($showChildDropdown && $selectedParentId > 0 && isset($childCategories[$selectedParentId])): ?>
              <a href="properties.php?category=<?php echo $selectedParentId; ?>" 
                 class="dropdown-item <?php echo $category_filter == $selectedParentId ? 'active' : ''; ?>">
                Tất cả
              </a>
              <?php foreach ($childCategories[$selectedParentId] as $child): ?>
                <a href="properties.php?category=<?php echo $child['id']; ?>" 
                   class="dropdown-item <?php echo $category_filter == $child['id'] ? 'active' : ''; ?>">
                  <?php echo htmlspecialchars($child['name']); ?>
                </a>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($category_filter > 0): ?>
          <a href="properties.php" class="btn-reset">
            <i class="fa fa-times"></i>
            Xóa bộ lọc
          </a>
        <?php endif; ?>
      </div>

      <script>
      // Store child categories data
      const childCategoriesData = <?php echo json_encode($childCategories); ?>;
      const parentCategoriesData = <?php echo json_encode($parentCategories); ?>;

      function toggleDropdown(dropdownId) {
        event.stopPropagation();
        const dropdown = document.getElementById(dropdownId);
        const toggle = dropdown.previousElementSibling;
        
        // Close other dropdowns
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
          if (menu.id !== dropdownId) {
            menu.classList.remove('show');
          }
        });
        document.querySelectorAll('.dropdown-toggle').forEach(btn => {
          if (btn !== toggle) {
            btn.classList.remove('active');
          }
        });
        
        dropdown.classList.toggle('show');
        toggle.classList.toggle('active');
      }

      // Close dropdown when clicking outside
      document.addEventListener('click', function(event) {
        if (!event.target.closest('.category-dropdown')) {
          document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
          });
          document.querySelectorAll('.dropdown-toggle').forEach(btn => {
            btn.classList.remove('active');
          });
        }
      });

      // Handle parent category selection with children
      document.querySelectorAll('#parentDropdown .dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
          const hasChildren = this.getAttribute('data-has-children') === '1';
          const parentId = parseInt(this.getAttribute('data-parent-id'));
          
          if (hasChildren) {
            e.preventDefault(); // Prevent navigation
            
            // Show child dropdown
            const childWrapper = document.getElementById('childDropdownWrapper');
            childWrapper.style.display = '';
            
            // Update parent button text
            document.getElementById('selectedParent').textContent = this.textContent.trim();
            
            // Populate child dropdown
            const childDropdown = document.getElementById('childDropdown');
            const children = childCategoriesData[parentId] || [];
            
            let childHTML = `<a href="properties.php?category=${parentId}" class="dropdown-item">Tất cả</a>`;
            children.forEach(child => {
              childHTML += `<a href="properties.php?category=${child.id}" class="dropdown-item">${child.name}</a>`;
            });
            
            childDropdown.innerHTML = childHTML;
            
            // Update child button to show default
            const parentName = parentCategoriesData.find(p => p.id == parentId)?.name || '';
            document.getElementById('selectedChild').textContent = 'Tất cả ' + parentName;
            
            // Close parent dropdown
            document.getElementById('parentDropdown').classList.remove('show');
            document.querySelector('#parentDropdown').previousElementSibling.classList.remove('active');
            
            // Auto open child dropdown
            setTimeout(() => {
              document.getElementById('childDropdown').classList.add('show');
              document.querySelector('#childDropdown').previousElementSibling.classList.add('active');
            }, 200);
          }
          // If no children, allow normal navigation
        });
      });
      </script>

      <!-- Products Grid -->
      <div class="row properties-grid">
        <?php if (count($products) > 0): ?>
          <?php foreach ($products as $product): ?>
            <div class="col-lg-4 col-md-6 align-self-center mb-30 properties-items">
              <div class="item">
                <a href="property-details.php?slug=<?php echo urlencode($product['slug']); ?>">
                  <?php if (!empty($product['image_path'])): ?>
                    <img src="uploads/products/<?php echo htmlspecialchars($product['image_path']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>">
                  <?php else: ?>
                    <img src="assets/images/no-image.png" alt="No image">
                  <?php endif; ?>
                </a>
                <span class="category"><?php echo !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'Sản phẩm'; ?></span>
                <h4><a href="property-details.php?slug=<?php echo urlencode($product['slug']); ?>">
                  <?php echo htmlspecialchars($product['title']); ?>
                </a></h4>
                <p class="short-desc"><?php echo htmlspecialchars($product['short_description'] ?? 'Sản phẩm chất lượng cao từ Đông Sơn Export'); ?></p>
                <div class="main-button">
                  <a href="property-details.php?slug=<?php echo urlencode($product['slug']); ?>">Xem chi tiết</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="no-products">
              <i class="fa fa-inbox"></i>
              <p>Không tìm thấy sản phẩm nào trong danh mục này.</p>
              <a href="properties.php" class="btn-back">Xem tất cả sản phẩm</a>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
      <div class="row">
        <div class="col-lg-12">
          <ul class="pagination">
            <?php if ($current_page > 1): ?>
              <li><a href="?page=<?php echo $current_page - 1; ?><?php echo $category_filter > 0 ? '&category=' . $category_filter : ''; ?>">&lt;&lt;</a></li>
            <?php endif; ?>

            <?php
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++):
            ?>
              <li>
                <a class="<?php echo $i == $current_page ? 'is_active' : ''; ?>" 
                   href="?page=<?php echo $i; ?><?php echo $category_filter > 0 ? '&category=' . $category_filter : ''; ?>">
                  <?php echo $i; ?>
                </a>
              </li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
              <li><a href="?page=<?php echo $current_page + 1; ?><?php echo $category_filter > 0 ? '&category=' . $category_filter : ''; ?>">&gt;&gt;</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
    </div>
  </div>

<?php include 'includes/footer.php'; ?>