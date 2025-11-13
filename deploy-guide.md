# HƯỚNG DẪN DEPLOY LÊN VPS
**VPS:** 103.200.20.160  
**User:** root

## BƯỚC 1: CHUẨN BỊ FILE (ĐÃ HOÀN THÀNH)
✅ Đã tạo `config.production.php`  
✅ Đã tạo `.htaccess.production`  
✅ Đã tạo `.gitignore`

---

## BƯỚC 2: CÀI ĐẶT VPS (Thực hiện trên VPS)

### 2.1. Kết nối SSH và cập nhật hệ thống
```bash
ssh root@103.200.20.160

# Update system
apt update && apt upgrade -y
```

### 2.2. Cài đặt LAMP Stack (Linux + Apache + MySQL + PHP)
```bash
# Cài Apache
apt install apache2 -y

# Cài MySQL
apt install mysql-server -y

# Cài PHP và các extension cần thiết
apt install php php-mysql php-mbstring php-xml php-gd php-curl libapache2-mod-php -y

# Enable rewrite module
a2enmod rewrite

# Restart Apache
systemctl restart apache2
```

### 2.3. Bảo mật MySQL
```bash
mysql_secure_installation
# Nhập password root MySQL mới
# Chọn Y cho tất cả các câu hỏi bảo mật
```

---

## BƯỚC 3: TẠO DATABASE

```bash
mysql -u root -p

# Trong MySQL console:
CREATE DATABASE dongson_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dongson_user'@'localhost' IDENTIFIED BY 'PASSWORD_MẠNH_Ở_ĐÂY';
GRANT ALL PRIVILEGES ON dongson_website.* TO 'dongson_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## BƯỚC 4: UPLOAD CODE

### Cách 1: Dùng Git (Khuyến nghị)
```bash
cd /var/www/html
git clone <repository-url> dongson
cd dongson
```

### Cách 2: Dùng SCP (Upload từ máy local)
```bash
# Trên máy Windows (Git Bash hoặc PowerShell)
scp -r C:/xampp/htdocs/dongson_webiste root@103.200.20.160:/var/www/html/dongson
```

### Cách 3: Dùng FTP/SFTP
- Dùng FileZilla hoặc WinSCP
- Connect to: 103.200.20.160
- Upload toàn bộ folder vào `/var/www/html/dongson`

---

## BƯỚC 5: CẤU HÌNH FILE

### 5.1. Sửa config.php để detect production
```bash
cd /var/www/html/dongson/includes

# Sửa file config.php
nano config.php
```

Sửa thành:
```php
<?php
// Auto-detect environment
$isProduction = !file_exists('/xampp'); // hoặc check domain/IP
$isDocker = getenv('DOCKER_ENV') !== false || file_exists('/.dockerenv');

if ($isProduction && !$isDocker) {
    require_once __DIR__ . '/config.production.php';
} elseif ($isDocker) {
    require_once __DIR__ . '/config.docker.php';
} else {
    require_once __DIR__ . '/config.local.php';
}
?>
```

### 5.2. Cấu hình database trong config.production.php
```bash
nano config.production.php
```
Điền đúng thông tin database đã tạo ở bước 3.

### 5.3. Copy .htaccess cho production
```bash
cd /var/www/html/dongson
cp .htaccess.production .htaccess
```

---

## BƯỚC 6: IMPORT DATABASE

```bash
mysql -u dongson_user -p dongson_website < /var/www/html/dongson/sql/dongson_website.sql
```

---

## BƯỚC 7: SET QUYỀN THƯ MỤC

```bash
cd /var/www/html/dongson

# Set owner
chown -R www-data:www-data /var/www/html/dongson

# Set permissions
chmod -R 755 /var/www/html/dongson
chmod -R 775 uploads
chmod -R 775 admin/images

# SELinux (nếu có)
# chcon -R -t httpd_sys_rw_content_t uploads/
```

---

## BƯỚC 8: CẤU HÌNH APACHE VIRTUAL HOST

```bash
nano /etc/apache2/sites-available/dongson.conf
```

Thêm nội dung:
```apache
<VirtualHost *:80>
    ServerAdmin admin@103.200.20.160
    ServerName 103.200.20.160
    DocumentRoot /var/www/html/dongson

    <Directory /var/www/html/dongson>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dongson-error.log
    CustomLog ${APACHE_LOG_DIR}/dongson-access.log combined
</VirtualHost>
```

Enable site và restart:
```bash
a2ensite dongson.conf
a2dissite 000-default.conf  # Disable default site
systemctl restart apache2
```

---

## BƯỚC 9: KIỂM TRA

Truy cập: **http://103.200.20.160**

### Kiểm tra lỗi:
```bash
# Check Apache error log
tail -f /var/log/apache2/dongson-error.log

# Check PHP errors
tail -f /var/log/apache2/error.log
```

---

## BƯỚC 10: BẢO MẬT (QUAN TRỌNG!)

### 10.1. Cài đặt Firewall
```bash
ufw allow 22/tcp    # SSH
ufw allow 80/tcp    # HTTP
ufw allow 443/tcp   # HTTPS (cho sau này)
ufw enable
```

### 10.2. Đổi password config.production.php
Dùng password mạnh cho database!

### 10.3. Cài SSL (sau khi có domain)
```bash
apt install certbot python3-certbot-apache -y
certbot --apache -d yourdomain.com
```

---

## GHI CHÚ QUAN TRỌNG:

1. **Backup trước khi deploy:** Lưu lại database và code hiện tại
2. **Không commit file nhạy cảm:** File `.gitignore` đã loại trừ các file config production
3. **Sau khi có domain:** Sửa `ServerName` trong VirtualHost và update `BASE_URL` nếu cần
4. **Database password:** Phải mạnh và khác với password root

---

## TROUBLESHOOTING:

### Lỗi 500 Internal Server Error:
- Check log: `tail -f /var/log/apache2/error.log`
- Kiểm tra quyền file/folder
- Kiểm tra `.htaccess`

### Không kết nối được database:
- Kiểm tra MySQL đang chạy: `systemctl status mysql`
- Kiểm tra thông tin trong `config.production.php`
- Test kết nối: `mysql -u dongson_user -p`

### Không load được CSS/JS:
- Kiểm tra `BASE_URL` trong config
- Kiểm tra quyền thư mục assets
