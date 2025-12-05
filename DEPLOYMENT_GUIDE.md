# 🚀 Panduan Deploy ke Hosting

## 📋 Daftar Isi
1. [Persiapan Sebelum Deploy](#persiapan-sebelum-deploy)
2. [Deploy ke Shared Hosting (cPanel)](#deploy-ke-shared-hosting-cpanel)
3. [Deploy ke VPS (Linux Server)](#deploy-ke-vps-linux-server)
4. [Setup Cronjob di Hosting](#setup-cronjob-di-hosting)
5. [Testing Setelah Deploy](#testing-setelah-deploy)
6. [Troubleshooting](#troubleshooting)

---

## Persiapan Sebelum Deploy

### 1. Checklist File yang Dibutuhkan

✅ Semua file project Laravel  
✅ File `.env.example` (akan di-copy jadi `.env` di hosting)  
✅ `composer.json` dan `composer.lock`  
✅ Database export/backup (jika ada data)  

### 2. File yang TIDAK Perlu Di-upload

❌ `node_modules/` (tidak digunakan di backend)  
❌ `vendor/` (akan di-generate ulang via composer)  
❌ `.env` (buat baru di hosting)  
❌ `storage/logs/*.log` (file log lokal)  
❌ `.git/` (optional, tergantung workflow)  

### 3. Generate Optimized Files (Optional)

Di local, jalankan:
```bash
# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Cache routes
php artisan route:cache

# Cache config
php artisan config:cache

# Cache views
php artisan view:cache
```

### 4. Export Database (Jika Ada Data)

```bash
# Via command line
mysqldump -u root -p tinder_app > tinder_app.sql

# Atau via phpMyAdmin:
# Export -> SQL -> OK
```

---

## Deploy ke Shared Hosting (cPanel)

### Step 1: Persiapan Hosting

**Requirements Minimum:**
- PHP 8.0+ atau 7.3+
- MySQL 5.7+ atau MariaDB
- Composer (biasanya sudah tersedia)
- SSH Access (optional tapi sangat membantu)

**Rekomendasi Hosting Indonesia:**
- Niagahoster
- Hostinger
- Dewaweb
- IDCloudHost
- Rumahweb

### Step 2: Buat Database di cPanel

1. Login ke **cPanel**
2. Cari **MySQL Databases**
3. Buat database baru:
   - Database name: `cpaneluser_tinderapp`
   - Buat user baru dengan password
   - Add user ke database dengan privileges **ALL PRIVILEGES**
4. Catat informasi:
   ```
   DB_HOST: localhost
   DB_DATABASE: cpaneluser_tinderapp
   DB_USERNAME: cpaneluser_dbuser
   DB_PASSWORD: [password yang dibuat]
   ```

### Step 3: Upload Files

#### Option A: Via File Manager (Mudah)

1. Buka **File Manager** di cPanel
2. Navigasi ke `public_html/` atau `domains/yourdomain.com/`
3. Buat folder baru: `laravel-app` (di luar public_html)
4. Upload semua file Laravel ke `laravel-app/`
5. Extract file zip (jika upload dalam bentuk zip)

**Struktur folder yang benar:**
```
/home/cpaneluser/
├── laravel-app/           ← Upload semua file Laravel di sini
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/           ← Folder public Laravel
│   ├── routes/
│   ├── storage/
│   ├── .env.example
│   ├── artisan
│   └── composer.json
│
└── public_html/          ← Root web (akan kita pointing ke laravel-app/public)
    └── (akan diisi nanti)
```

#### Option B: Via FTP (Lebih Cepat)

1. Install FTP client (FileZilla)
2. Connect ke hosting:
   - Host: ftp.yourdomain.com
   - Username: cpanel username
   - Password: cpanel password
   - Port: 21
3. Upload semua file ke `/home/cpaneluser/laravel-app/`

#### Option C: Via Git (Paling Profesional)

```bash
# SSH ke hosting
ssh cpaneluser@yourdomain.com

# Clone repository
cd ~
git clone https://github.com/yourusername/Tinder-App.git laravel-app
cd laravel-app
```

### Step 4: Install Dependencies

#### Via SSH (Recommended)

```bash
# SSH ke hosting
ssh cpaneluser@yourdomain.com

# Masuk ke folder project
cd ~/laravel-app

# Install composer dependencies
composer install --optimize-autoloader --no-dev

# Jika composer tidak tersedia global, gunakan composer.phar
php composer.phar install --optimize-autoloader --no-dev
```

#### Via cPanel Terminal

1. Buka **Terminal** di cPanel
2. Jalankan command yang sama seperti di atas

#### Jika Composer Tidak Tersedia

Download vendor dari local:
```bash
# Di local computer
composer install --optimize-autoloader --no-dev
zip -r vendor.zip vendor/

# Upload vendor.zip ke hosting
# Extract di folder laravel-app
```

### Step 5: Setup Environment

```bash
# Via SSH atau Terminal cPanel
cd ~/laravel-app

# Copy .env.example
cp .env.example .env

# Edit .env
nano .env
# atau gunakan File Manager editor di cPanel
```

**Isi `.env` untuk production:**

```env
APP_NAME="Tinder App API"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=cpaneluser_tinderapp
DB_USERNAME=cpaneluser_dbuser
DB_PASSWORD=your_database_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Email Configuration (IMPORTANT!)
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
MAIL_ADMIN_EMAIL=admin@yourdomain.com
```

**Generate APP_KEY:**
```bash
php artisan key:generate
```

### Step 6: Setup Permissions

```bash
cd ~/laravel-app

# Set permission untuk storage dan cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (jika perlu)
chown -R cpaneluser:cpaneluser storage
chown -R cpaneluser:cpaneluser bootstrap/cache
```

### Step 7: Run Migrations

```bash
cd ~/laravel-app

# Jalankan migrasi
php artisan migrate --force

# Seed data (optional)
php artisan db:seed --force

# Generate Swagger docs
php artisan l5-swagger:generate
```

### Step 8: Setup Public Folder (PENTING!)

Ada 2 cara:

#### Option A: Symlink (Recommended)

```bash
# Hapus isi public_html (backup dulu jika ada)
cd ~/public_html
rm -rf *

# Buat symlink ke laravel public folder
ln -s ~/laravel-app/public/* ~/public_html/

# Atau copy semua isi public folder
cp -r ~/laravel-app/public/* ~/public_html/
```

#### Option B: Ubah Document Root

1. Di cPanel, cari **Domains** atau **Addon Domains**
2. Edit domain utama
3. Ubah **Document Root** dari `/public_html` menjadi `/laravel-app/public`
4. Save

#### Option C: .htaccess Redirect (Fallback)

Jika tidak bisa ubah document root, buat file `.htaccess` di `public_html`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ /laravel-app/public/$1 [L]
</IfModule>
```

### Step 9: Setup .htaccess di Laravel Public

Pastikan file `laravel-app/public/.htaccess` ada:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### Step 10: Clear & Optimize Cache

```bash
cd ~/laravel-app

# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Deploy ke VPS (Linux Server)

### Requirements
- Ubuntu 20.04+ / CentOS 7+ / Debian 10+
- Root or sudo access
- Domain pointing ke VPS IP

### Step 1: Install Dependencies

#### Ubuntu/Debian

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1 dan extensions
sudo apt install -y php8.1 php8.1-fpm php8.1-mysql php8.1-mbstring \
    php8.1-xml php8.1-bcmath php8.1-curl php8.1-zip php8.1-gd

# Install MySQL
sudo apt install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo apt install -y nginx

# Install Git
sudo apt install -y git
```

#### CentOS/RHEL

```bash
# Update system
sudo yum update -y

# Install PHP 8.1
sudo yum install -y epel-release
sudo yum install -y php81 php81-php-fpm php81-php-mysqlnd php81-php-mbstring \
    php81-php-xml php81-php-bcmath php81-php-curl php81-php-zip php81-php-gd

# Install MySQL
sudo yum install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Nginx
sudo yum install -y nginx

# Install Git
sudo yum install -y git
```

### Step 2: Setup Database

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Login ke MySQL
sudo mysql -u root -p

# Buat database dan user
CREATE DATABASE tinder_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'tinderapp_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON tinder_app.* TO 'tinderapp_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Clone & Setup Project

```bash
# Buat user untuk aplikasi (optional)
sudo useradd -m -s /bin/bash tinderapp

# Clone project
cd /var/www
sudo git clone https://github.com/yourusername/Tinder-App.git
sudo mv Tinder-App tinderapp

# Set ownership
sudo chown -R tinderapp:tinderapp /var/www/tinderapp
cd /var/www/tinderapp

# Install dependencies
sudo -u tinderapp composer install --optimize-autoloader --no-dev

# Setup environment
sudo -u tinderapp cp .env.example .env
sudo -u tinderapp nano .env
```

**Edit `.env`:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=tinder_app
DB_USERNAME=tinderapp_user
DB_PASSWORD=strong_password_here

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_ADMIN_EMAIL=admin@yourdomain.com
```

```bash
# Generate key
sudo -u tinderapp php artisan key:generate

# Set permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R tinderapp:www-data storage bootstrap/cache

# Run migrations
sudo -u tinderapp php artisan migrate --force
sudo -u tinderapp php artisan db:seed --force
sudo -u tinderapp php artisan l5-swagger:generate

# Optimize
sudo -u tinderapp php artisan config:cache
sudo -u tinderapp php artisan route:cache
sudo -u tinderapp php artisan view:cache
```

### Step 4: Setup Nginx

```bash
# Buat config file
sudo nano /etc/nginx/sites-available/tinderapp
```

**Isi config:**
```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/tinderapp/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/tinderapp /etc/nginx/sites-enabled/

# Test config
sudo nginx -t

# Restart nginx
sudo systemctl restart nginx

# Enable on boot
sudo systemctl enable nginx
```

### Step 5: Setup SSL dengan Let's Encrypt

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Generate SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal akan disetup otomatis
sudo certbot renew --dry-run
```

---

## Setup Cronjob di Hosting

### Shared Hosting (cPanel)

1. Login ke **cPanel**
2. Cari **Cron Jobs**
3. Tambahkan cron job baru:

**Setiap Menit (untuk Laravel Scheduler):**
```bash
* * * * * cd /home/cpaneluser/laravel-app && php artisan schedule:run >> /dev/null 2>&1
```

**Atau Setiap Jam (langsung command):**
```bash
0 * * * * cd /home/cpaneluser/laravel-app && php artisan people:check-popular >> /dev/null 2>&1
```

4. Pilih email untuk notifikasi (optional)
5. **Add New Cron Job**

### VPS (Linux)

```bash
# Edit crontab
crontab -e

# Tambahkan (pilih salah satu):

# Option 1: Laravel Scheduler (Recommended)
* * * * * cd /var/www/tinderapp && php artisan schedule:run >> /dev/null 2>&1

# Option 2: Direct command (setiap jam)
0 * * * * cd /var/www/tinderapp && php artisan people:check-popular >> /dev/null 2>&1
```

**Verify cron job:**
```bash
# Lihat cron jobs yang aktif
crontab -l

# Monitor cron log
sudo tail -f /var/log/cron
# atau
sudo tail -f /var/log/syslog | grep CRON
```

---

## Testing Setelah Deploy

### 1. Test Website Bisa Diakses

```bash
# Buka browser
https://yourdomain.com
```

**Expected:** Halaman Laravel default atau blank page (normal karena kita hanya buat API)

### 2. Test API Endpoints

```bash
# Test basic endpoint
curl https://yourdomain.com/api/v1/people

# Test Swagger documentation
https://yourdomain.com/api/documentation
```

**Expected:** Response JSON dengan data people

### 3. Test Database Connection

```bash
# SSH ke hosting
cd /path/to/laravel-app

# Test connection
php artisan tinker

# Di tinker console:
DB::connection()->getPdo();
# Expected: PDO object

\App\Models\Person::count();
# Expected: angka (misal: 15)

exit
```

### 4. Test Cronjob

```bash
# Jalankan manual
cd /path/to/laravel-app
php artisan people:check-popular

# Expected output jika ada orang populer:
# Checking for popular people...
# Found X popular people.
# Email sent for: ...
# Done checking popular people.
```

### 5. Test Email

```bash
# Update likes count secara manual
mysql -u username -p

USE tinder_app;
UPDATE people SET likes_count = 52, email_sent = 0 WHERE id = 1;
EXIT;

# Jalankan command
php artisan people:check-popular

# Check email di inbox admin
```

### 6. Monitoring Logs

```bash
# Lihat Laravel logs
tail -f /path/to/laravel-app/storage/logs/laravel.log

# Lihat Nginx logs (VPS)
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log

# Lihat PHP-FPM logs (VPS)
sudo tail -f /var/log/php8.1-fpm.log
```

---

## Troubleshooting

### Problem 1: 500 Internal Server Error

**Penyebab:**
- Permission salah
- .env tidak ada atau salah
- APP_KEY tidak di-generate

**Solusi:**
```bash
# Set permission
chmod -R 775 storage bootstrap/cache

# Generate key
php artisan key:generate

# Check .env
cat .env | grep APP_KEY
# Harus ada value, bukan kosong

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Problem 2: CSRF Token Mismatch

**Solusi:**
Di `.env` pastikan:
```env
SESSION_DRIVER=file
SESSION_DOMAIN=yourdomain.com
```

### Problem 3: Database Connection Error

**Solusi:**
```bash
# Test connection
php artisan tinker
DB::connection()->getPdo();

# Jika error, check:
# 1. DB credentials di .env
# 2. Database exists
# 3. User punya permission

# Di MySQL:
SHOW GRANTS FOR 'username'@'localhost';
```

### Problem 4: Storage Link Not Working

**Solusi:**
```bash
# Buat symlink
php artisan storage:link
```

### Problem 5: Composer Install Gagal

**Penyebab:** Memory limit

**Solusi:**
```bash
# Increase memory limit
php -d memory_limit=-1 /usr/local/bin/composer install --no-dev
```

### Problem 6: Cronjob Tidak Jalan

**Check:**
```bash
# Lihat cron log
tail -f /var/log/cron

# Test manual
cd /path/to/project
php artisan schedule:run -v

# Check permission
which php
# Pastikan path php sama dengan yang di cron
```

### Problem 7: Email Tidak Terkirim

**Solusi:**
```bash
# Test SMTP connection
telnet smtp.yourdomain.com 587

# Gunakan log driver untuk testing
MAIL_MAILER=log

# Check Laravel log
tail -f storage/logs/laravel.log
```

### Problem 8: Swagger UI Not Loading

**Solusi:**
```bash
# Regenerate docs
php artisan l5-swagger:generate

# Check permission
chmod -R 775 storage/api-docs

# Check Nginx config allows .json files
```

---

## 🔒 Security Checklist

✅ **Set APP_DEBUG=false** di production  
✅ **Set APP_ENV=production**  
✅ **Generate strong APP_KEY**  
✅ **Set proper file permissions** (775 storage, 755 others)  
✅ **Enable HTTPS** (SSL certificate)  
✅ **Hide .env file** (jangan bisa diakses public)  
✅ **Disable directory listing**  
✅ **Use strong database password**  
✅ **Limit database user privileges**  
✅ **Setup firewall** (VPS)  
✅ **Regular backup** database dan files  
✅ **Monitor logs** secara berkala  

---

## 📊 Post-Deployment Monitoring

### Daily Tasks
```bash
# Check logs
tail -n 100 storage/logs/laravel.log

# Check disk space
df -h

# Check cronjob status
tail -n 50 /var/log/cron
```

### Weekly Tasks
- Backup database
- Check API response time
- Review error logs
- Update dependencies (if needed)

### Monthly Tasks
- Security updates
- Performance optimization
- Database optimization
- SSL certificate renewal check

---

## 🎯 Quick Deployment Checklist

**Sebelum Deploy:**
- [ ] Test lokal berjalan sempurna
- [ ] Database export ready
- [ ] .env.example updated
- [ ] Documentation lengkap

**Di Hosting:**
- [ ] Database created
- [ ] Files uploaded
- [ ] Composer install
- [ ] .env configured
- [ ] APP_KEY generated
- [ ] Permissions set
- [ ] Migrations run
- [ ] Public folder setup
- [ ] Cache optimized
- [ ] Cronjob configured

**Testing:**
- [ ] Homepage accessible
- [ ] API endpoints working
- [ ] Swagger UI loading
- [ ] Database connection OK
- [ ] Cronjob running
- [ ] Email sending
- [ ] Logs recording

**Security:**
- [ ] APP_DEBUG=false
- [ ] SSL enabled
- [ ] .env protected
- [ ] Firewall configured

---

## 📞 Support Resources

**Hosting Issues:**
- Contact hosting support
- Check hosting documentation
- Search hosting knowledge base

**Laravel Issues:**
- Laravel Documentation: https://laravel.com/docs
- Laravel Forums: https://laracasts.com/discuss
- Stack Overflow: tag [laravel]

**Database Issues:**
- Check MySQL/MariaDB logs
- Use phpMyAdmin for GUI management
- Backup before making changes

---

## 🎉 Congratulations!

Jika semua step berhasil, aplikasi Anda sudah live di:

🌐 **Website:** https://yourdomain.com  
📚 **API Docs:** https://yourdomain.com/api/documentation  
🔗 **API Base:** https://yourdomain.com/api/v1  

---

**File dibuat:** December 5, 2025  
**Untuk:** Tinder App Backend API  
**Status:** Production Ready  

