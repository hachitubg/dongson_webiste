# 🚀 HƯỚNG DẪN DEPLOY WEBSITE ĐÔNG SƠN

## 📋 MỤC LỤC
1. [Yêu cầu hệ thống](#yêu-cầu-hệ-thống)
2. [Deploy lần đầu](#deploy-lần-đầu)
3. [Thêm tên miền](#thêm-tên-miền)
4. [Update code mới](#update-code-mới)
5. [Backup & Restore Database](#backup--restore-database)
6. [Troubleshooting](#troubleshooting)

---

## ⚙️ YÊU CẦU HỆ THỐNG

- **VPS:** Ubuntu 22.04 hoặc mới hơn
- **RAM:** Tối thiểu 1GB
- **Disk:** Tối thiểu 10GB
- **Software:** Nginx, MySQL, PHP 8.1+, Git

---

## 🎯 DEPLOY LẦN ĐẦU

### Bước 1: Kết nối VPS
```bash
ssh root@YOUR_VPS_IP
```

### Bước 2: Cài đặt LEMP Stack

```bash
# Update hệ thống
apt update && apt upgrade -y

# Cài Nginx (nếu chưa có)
apt install nginx -y
systemctl start nginx
systemctl enable nginx

# Cài MySQL
apt install mysql-server -y
mysql_secure_installation
# - Nhập password root MySQL mới
# - Chọn Y cho tất cả các câu hỏi

# Cài PHP 8.1 và extensions
apt install php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-gd php8.1-curl php8.1-zip php8.1-cli -y

# Cài Git
apt install git -y
```

### Bước 3: Tạo Database

```bash
# Đăng nhập MySQL
sudo mysql

# Trong MySQL console:
CREATE DATABASE dongson_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dongson_user'@'localhost' IDENTIFIED BY 'Dongson@2024#VPS';
GRANT ALL PRIVILEGES ON dongson_website.* TO 'dongson_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Bước 4: Clone Code từ GitHub

```bash
# Tạo thư mục
cd /var/www
git clone https://github.com/hachitubg/dongson_webiste.git dongson

# Vào thư mục
cd dongson
```

### Bước 5: Import Database

```bash
mysql -u dongson_user -p dongson_website < /var/www/dongson/sql/dongson_website.sql
# Password: Dongson@2024#VPS
```

### Bước 6: Set Quyền Thư Mục

```bash
cd /var/www/dongson
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 775 uploads
chmod -R 775 admin/images
```

### Bước 7: Cấu hình Nginx

```bash
# Tạo file config
nano /etc/nginx/sites-available/dongson
```

**Nội dung file (chạy trên PORT 9000):**

```nginx
server {
    listen 9000;
    listen [::]:9000;

    server_name YOUR_VPS_IP;
    root /var/www/dongson;
    index index.php index.html;

    client_max_body_size 20M;

    access_log /var/log/nginx/dongson-access.log;
    error_log /var/log/nginx/dongson-error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(htaccess|git|env) {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

```bash
# Enable site
ln -s /etc/nginx/sites-available/dongson /etc/nginx/sites-enabled/

# Test config
nginx -t

# Restart Nginx
systemctl restart nginx

# Mở port firewall
ufw allow 9000/tcp
```

### Bước 8: Test Website

Truy cập: `http://YOUR_VPS_IP:9000`

Debug (nếu cần): `http://YOUR_VPS_IP:9000/test_db.php`

---

## 🌐 THÊM TÊN MIỀN

### Bước 1: Trỏ Domain về VPS

Tại nhà cung cấp domain (GoDaddy, Namecheap, etc), thêm DNS records:

```
Type: A
Name: @
Value: YOUR_VPS_IP
TTL: 3600

Type: A
Name: www
Value: YOUR_VPS_IP
TTL: 3600
```

### Bước 2: Sửa Nginx Config

```bash
nano /etc/nginx/sites-available/dongson
```

**Sửa dòng `server_name` và `listen`:**

```nginx
server {
    listen 80;
    listen [::]:80;

    server_name yourdomain.com www.yourdomain.com;
    
    # ... giữ nguyên phần còn lại
}
```

```bash
# Test và restart
nginx -t
systemctl restart nginx

# Mở port 80
ufw allow 80/tcp
```

### Bước 3: Cài SSL Certificate (Let's Encrypt)

```bash
# Cài Certbot
apt install certbot python3-certbot-nginx -y

# Tạo SSL certificate
certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Chọn:
# - Email: your@email.com
# - Agree to terms: Yes
# - Redirect HTTP to HTTPS: Yes (option 2)
```

Certificate sẽ tự động gia hạn!

---

## 🔄 UPDATE CODE MỚI

### Trên Localhost (Máy Windows)

```bash
# Commit và push code
git add .
git commit -m "Update features"
git push origin main
```

### Trên VPS

```bash
# SSH vào VPS
ssh root@YOUR_VPS_IP

# Vào thư mục website
cd /var/www/dongson

# Pull code mới
git pull origin main

# Set lại quyền (nếu cần)
chown -R www-data:www-data .
chmod -R 755 .
chmod -R 775 uploads admin/images

# Clear cache PHP (nếu dùng opcache)
systemctl restart php8.1-fpm
```

**Lưu ý:** 
- Nếu có thay đổi database schema, cần chạy thêm file migration/SQL
- Nếu pull bị lỗi conflict, dùng: `git reset --hard origin/main`

---

## 💾 BACKUP & RESTORE DATABASE

### 1. BACKUP DATABASE TỪ PRODUCTION (VPS)

#### Backup và tải về máy local

```bash
# Trên VPS: Tạo file backup
cd /var/www/dongson/sql
mysqldump -u dongson_user -p dongson_website > backup_$(date +%Y%m%d_%H%M%S).sql
# Password: Dongson@2024#VPS

# Liệt kê file backup
ls -lh backup_*.sql
```

**Tải file backup về Windows:**

```bash
# Trên máy Windows (Git Bash hoặc PowerShell)
scp root@YOUR_VPS_IP:/var/www/dongson/sql/backup_20251114_*.sql C:/xampp/htdocs/dongson_webiste/sql/
```

#### Import vào XAMPP Localhost

```bash
# Trên Windows
cd C:/xampp/htdocs/dongson_webiste/sql

# Import vào MySQL localhost
mysql -u root -p dongson_website < backup_20251114_*.sql
# Password: (để trống nếu XAMPP không có password)
```

**Hoặc dùng phpMyAdmin:**
1. Mở `http://localhost/phpmyadmin`
2. Chọn database `dongson_website`
3. Tab **Import** → Chọn file backup → **Go**

---

### 2. BACKUP TỪ LOCALHOST LÊN PRODUCTION

#### Tạo backup từ XAMPP

```bash
# Trên Windows
cd C:/xampp/htdocs/dongson_webiste/sql

# Tạo backup (nếu có mysqldump trong PATH)
C:/xampp/mysql/bin/mysqldump -u root dongson_website > localhost_backup_$(date +%Y%m%d).sql
```

**Hoặc dùng phpMyAdmin:**
1. Mở `http://localhost/phpmyadmin`
2. Chọn database `dongson_website`
3. Tab **Export** → **Quick** → **Go**
4. Lưu file vào `sql/localhost_backup_YYYYMMDD.sql`

#### Upload lên VPS

```bash
# Trên Windows
scp C:/xampp/htdocs/dongson_webiste/sql/localhost_backup_*.sql root@YOUR_VPS_IP:/var/www/dongson/sql/
```

#### Import vào Production

```bash
# Trên VPS
cd /var/www/dongson/sql

# Backup database hiện tại trước khi import (quan trọng!)
mysqldump -u dongson_user -p dongson_website > before_import_backup_$(date +%Y%m%d_%H%M%S).sql

# Import database mới
mysql -u dongson_user -p dongson_website < localhost_backup_*.sql
# Password: Dongson@2024#VPS
```

---

### 3. BACKUP TỰ ĐỘNG (Khuyến nghị cho Production)

#### Tạo script backup tự động

```bash
# Trên VPS
nano /root/backup_dongson.sh
```

**Nội dung:**

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/dongson"
DATE=$(date +%Y%m%d_%H%M%S)
DB_USER="dongson_user"
DB_PASS="Dongson@2024#VPS"
DB_NAME="dongson_website"

# Tạo thư mục backup
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# Backup files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/dongson

# Xóa backup cũ hơn 7 ngày
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Set quyền thực thi
chmod +x /root/backup_dongson.sh

# Test chạy
/root/backup_dongson.sh
```

#### Tạo Cron Job (chạy hàng ngày lúc 2h sáng)

```bash
crontab -e

# Thêm dòng này:
0 2 * * * /root/backup_dongson.sh >> /var/log/dongson_backup.log 2>&1
```

#### Tải backup về local định kỳ

```bash
# Trên Windows (Git Bash) - Tạo file download_backup.sh
#!/bin/bash
VPS_IP="YOUR_VPS_IP"
BACKUP_DIR="C:/backups/dongson"
mkdir -p $BACKUP_DIR

# Tải backup mới nhất
scp root@$VPS_IP:/var/backups/dongson/db_*.sql $BACKUP_DIR/
scp root@$VPS_IP:/var/backups/dongson/files_*.tar.gz $BACKUP_DIR/

echo "Backup downloaded to $BACKUP_DIR"
```

---

## 🐛 TROUBLESHOOTING

### Website hiển thị lỗi 500

```bash
# Xem log lỗi
tail -f /var/log/nginx/dongson-error.log
tail -f /var/log/php8.1-fpm.log

# Kiểm tra quyền file
ls -la /var/www/dongson
```

### Database không kết nối được

```bash
# Test connection
php /var/www/dongson/test_db.php

# Hoặc truy cập:
http://YOUR_VPS_IP:9000/test_db.php

# Kiểm tra MySQL user
sudo mysql -e "SELECT user, host FROM mysql.user WHERE user='dongson_user';"
```

### Pull code bị conflict

```bash
cd /var/www/dongson

# Xem file nào bị conflict
git status

# Reset về version Git (MẤT thay đổi local!)
git reset --hard origin/main

# Hoặc stash thay đổi local
git stash
git pull origin main
```

### Nginx không khởi động được

```bash
# Kiểm tra cú pháp config
nginx -t

# Xem log
tail -f /var/log/nginx/error.log

# Kiểm tra port đã bị chiếm chưa
netstat -tulpn | grep :80
```

### Upload file quá lớn bị lỗi

```bash
# Sửa Nginx config
nano /etc/nginx/sites-available/dongson

# Thêm/sửa dòng:
client_max_body_size 50M;

# Sửa PHP config
nano /etc/php/8.1/fpm/php.ini

# Tìm và sửa:
upload_max_filesize = 50M
post_max_size = 50M

# Restart services
systemctl restart nginx
systemctl restart php8.1-fpm
```

---
