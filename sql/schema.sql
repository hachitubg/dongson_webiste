-- dongson-website schema (import into phpMyAdmin)
CREATE DATABASE IF NOT EXISTS dongson_website DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dongson_website;

-- users (admin)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO users (username, password_hash) VALUES 
('admin', '$2y$10$LsWaMC12vFGV8ob9P6FS.O3O6D9gEFJp1BHa7I2mxXOSXS.q1vyn6');

-- categories
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  parent_id INT DEFAULT NULL,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  type ENUM('product', 'post') NOT NULL,
  description TEXT,
  is_active TINYINT(1) DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE,
  INDEX idx_type_active (type, is_active),
  INDEX idx_slug (slug),
  INDEX idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert product categories (2-level hierarchy)
-- Level 1: Main categories (parent categories)
INSERT INTO categories (id, parent_id, name, slug, type, is_active, sort_order) VALUES
(1, NULL, 'Sản phẩm cho trâu bò dê cừu', 'san-pham-cho-trau-bo-de-cuu', 'product', 1, 1),
(2, NULL, 'Sản phẩm cho cá', 'san-pham-cho-ca', 'product', 1, 2),
(3, NULL, 'Sản phẩm cho chó mèo', 'san-pham-cho-cho-meo', 'product', 1, 3),
(4, NULL, 'Sản phẩm cho gia cầm', 'san-pham-cho-gia-cam', 'product', 1, 4),
(5, NULL, 'Sản phẩm cho heo', 'san-pham-cho-heo', 'product', 1, 5),
(6, NULL, 'Sản phẩm cho tôm', 'san-pham-cho-tom', 'product', 1, 6);

-- Level 2: Sub-categories for Trâu bò dê cừu
INSERT INTO categories (parent_id, name, slug, type, is_active, sort_order) VALUES
(1, 'Cung cấp dinh dưỡng, canxi', 'trau-bo-cung-cap-dinh-duong-canxi', 'product', 1, 1),
(1, 'Kháng sinh phòng trị bệnh', 'trau-bo-khang-sinh-phong-tri-benh', 'product', 1, 2),
(1, 'Kháng viêm, giảm đau, hạ sốt', 'trau-bo-khang-viem-giam-dau-ha-sot', 'product', 1, 3),
(1, 'Kích thích tố sinh dục', 'trau-bo-kich-thich-to-sinh-duc', 'product', 1, 4),
(1, 'Men tiêu hóa', 'trau-bo-men-tieu-hoa', 'product', 1, 5),
(1, 'Phòng trị ký sinh trùng', 'trau-bo-phong-tri-ky-sinh-trung', 'product', 1, 6),
(1, 'Thuốc khác', 'trau-bo-thuoc-khac', 'product', 1, 7),
(1, 'Thuốc khử trùng', 'trau-bo-thuoc-khu-trung', 'product', 1, 8);

-- Level 2: Sub-categories for Cá
INSERT INTO categories (parent_id, name, slug, type, is_active, sort_order) VALUES
(2, 'Nhóm dinh dưỡng và vi sinh có lợi', 'ca-dinh-duong-va-vi-sinh-co-loi', 'product', 1, 1),
(2, 'Nhóm khử trùng nước và vi sinh xử lý nước', 'ca-khu-trung-nuoc-va-vi-sinh-xu-ly-nuoc', 'product', 1, 2),
(2, 'Nhóm thuốc dành cho cá cảnh', 'ca-thuoc-danh-cho-ca-canh', 'product', 1, 3),
(2, 'Nhóm thuốc phòng, trị bệnh', 'ca-thuoc-phong-tri-benh', 'product', 1, 4),
(2, 'Vitamin và khoáng chất thiết yếu cho cá', 'ca-vitamin-va-khoang-chat-thiet-yeu', 'product', 1, 5);

-- Level 2: Sub-categories for Chó mèo
INSERT INTO categories (parent_id, name, slug, type, is_active, sort_order) VALUES
(3, 'Cung cấp dinh dưỡng, canxi', 'cho-meo-cung-cap-dinh-duong-canxi', 'product', 1, 1),
(3, 'Dầu tắm chó mèo', 'cho-meo-dau-tam', 'product', 1, 2),
(3, 'Kháng sinh phòng trị bệnh', 'cho-meo-khang-sinh-phong-tri-benh', 'product', 1, 3),
(3, 'Kháng viêm, giảm đau, hạ sốt', 'cho-meo-khang-viem-giam-dau-ha-sot', 'product', 1, 4),
(3, 'Kích thích tố sinh dục', 'cho-meo-kich-thich-to-sinh-duc', 'product', 1, 5),
(3, 'Men tiêu hóa', 'cho-meo-men-tieu-hoa', 'product', 1, 6),
(3, 'Phòng trị ký sinh trùng', 'cho-meo-phong-tri-ky-sinh-trung', 'product', 1, 7),
(3, 'Thuốc khác', 'cho-meo-thuoc-khac', 'product', 1, 8),
(3, 'Thuốc khử trùng', 'cho-meo-thuoc-khu-trung', 'product', 1, 9);

-- Level 2: Sub-categories for Gia cầm
INSERT INTO categories (parent_id, name, slug, type, is_active, sort_order) VALUES
(4, 'Cung cấp dinh dưỡng, canxi', 'gia-cam-cung-cap-dinh-duong-canxi', 'product', 1, 1),
(4, 'Kháng sinh phòng trị bệnh', 'gia-cam-khang-sinh-phong-tri-benh', 'product', 1, 2),
(4, 'Kháng viêm, giảm đau, hạ sốt', 'gia-cam-khang-viem-giam-dau-ha-sot', 'product', 1, 3),
(4, 'Men tiêu hóa', 'gia-cam-men-tieu-hoa', 'product', 1, 4),
(4, 'Phòng trị ký sinh trùng', 'gia-cam-phong-tri-ky-sinh-trung', 'product', 1, 5),
(4, 'Thuốc khác', 'gia-cam-thuoc-khac', 'product', 1, 6),
(4, 'Thuốc khử trùng', 'gia-cam-thuoc-khu-trung', 'product', 1, 7);

-- Level 2: Sub-categories for Heo
INSERT INTO categories (parent_id, name, slug, type, is_active, sort_order) VALUES
(5, 'Cung cấp dinh dưỡng, canxi', 'heo-cung-cap-dinh-duong-canxi', 'product', 1, 1),
(5, 'Kháng sinh phòng trị bệnh', 'heo-khang-sinh-phong-tri-benh', 'product', 1, 2),
(5, 'Kháng viêm, giảm đau, hạ sốt', 'heo-khang-viem-giam-dau-ha-sot', 'product', 1, 3),
(5, 'Kích thích tố sinh dục', 'heo-kich-thich-to-sinh-duc', 'product', 1, 4),
(5, 'Men tiêu hóa', 'heo-men-tieu-hoa', 'product', 1, 5),
(5, 'Phòng trị ký sinh trùng', 'heo-phong-tri-ky-sinh-trung', 'product', 1, 6),
(5, 'Thuốc khác', 'heo-thuoc-khac', 'product', 1, 7),
(5, 'Thuốc khử trùng', 'heo-thuoc-khu-trung', 'product', 1, 8);

-- Level 2: Sub-categories for Tôm
INSERT INTO categories (parent_id, name, slug, type, is_active, sort_order) VALUES
(6, 'Khoáng chất thiết yếu cho tôm', 'tom-khoang-chat-thiet-yeu', 'product', 1, 1),
(6, 'Nhóm chế phầm sinh học, vi sinh xử lý ao nuôi', 'tom-che-pham-sinh-hoc-vi-sinh-xu-ly-ao', 'product', 1, 2),
(6, 'Nhóm dinh dưỡng và vitamin', 'tom-dinh-duong-va-vitamin', 'product', 1, 3),
(6, 'Nhóm khử trùng nước', 'tom-khu-trung-nuoc', 'product', 1, 4),
(6, 'Nhóm thuốc phòng, trị bệnh', 'tom-thuoc-phong-tri-benh', 'product', 1, 5),
(6, 'Nhóm vi sinh có lợi cho acid hữu cơ', 'tom-vi-sinh-co-loi-cho-acid-huu-co', 'product', 1, 6);

-- Post categories (1-level only, no parent-child)
INSERT INTO categories (parent_id, name, slug, type, is_active, sort_order) VALUES
(NULL, 'Hoạt động - Sự kiện', 'hoat-dong-su-kien', 'post', 1, 1),
(NULL, 'Hội thảo - họp mặt', 'hoi-thao-hop-mat', 'post', 1, 2),
(NULL, 'Tham quan công ty', 'tham-quan-cong-ty', 'post', 1, 3),
(NULL, 'Tuyển dụng', 'tuyen-dung', 'post', 1, 4);

-- products
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT DEFAULT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  short_description TEXT,
  price DECIMAL(12,2) DEFAULT 0,
  promo_price DECIMAL(12,2) DEFAULT NULL,
  description MEDIUMTEXT,
  display_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_category (category_id),
  INDEX idx_display_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  sort_order INT DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- posts
CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT DEFAULT NULL,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  excerpt TEXT,
  content MEDIUMTEXT,
  featured_image VARCHAR(255),
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- banners
CREATE TABLE IF NOT EXISTS banners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  location_code VARCHAR(50) NOT NULL,
  image_path VARCHAR(255) NOT NULL,
  link_url VARCHAR(500),
  is_active TINYINT(1) DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_location (location_code, is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- contact_messages
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50) NOT NULL,
  subject VARCHAR(500),
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_is_read (is_read),
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

