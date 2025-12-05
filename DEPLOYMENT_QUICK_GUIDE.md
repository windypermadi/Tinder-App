# ⚡ Quick Deployment Guide

## 🚀 Deploy ke Shared Hosting (cPanel)

### 1. Persiapan
```bash
# Di local, optimize dulu
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache

# Export database
mysqldump -u root -p tinder_app > tinder_app.sql
```

### 2. Upload Files
- Login cPanel → File Manager
- Upload semua file Laravel ke folder: `/home/user/laravel-app`
- Extract jika dalam bentuk zip

### 3. Setup Database
- cPanel → MySQL Databases
- Buat database: `user_tinderapp`
- Buat user dan password
- Add user ke database (ALL PRIVILEGES)

### 4. Install Composer Dependencies
```bash
# Via SSH/Terminal
cd ~/laravel-app
composer install --optimize-autoloader --no-dev
```

### 5. Configure Environment
```bash
# Copy .env
cp .env.example .env
nano .env
```

Update:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_DATABASE=user_tinderapp
DB_USERNAME=user_dbuser
DB_PASSWORD=your_password

MAIL_ADMIN_EMAIL=admin@yourdomain.com
```

### 6. Setup Application
```bash
# Generate key
php artisan key:generate

# Set permissions
chmod -R 775 storage bootstrap/cache

# Run migrations
php artisan migrate --force
php artisan db:seed --force
php artisan l5-swagger:generate

# Clear & cache
php artisan config:cache
php artisan route:cache
```

### 7. Setup Public Access

**Option A: Change Document Root**
- cPanel → Domains
- Edit domain
- Document Root: `/home/user/laravel-app/public`

**Option B: Symlink**
```bash
cd ~/public_html
rm -rf *
ln -s ~/laravel-app/public/* .
```

### 8. Setup Cronjob
- cPanel → Cron Jobs
- Add:
```
* * * * * cd /home/user/laravel-app && php artisan schedule:run >> /dev/null 2>&1
```

### 9. Test
```
https://yourdomain.com/api/v1/people
https://yourdomain.com/api/documentation
```

---

## 🖥️ Deploy ke VPS (Ubuntu)

### 1. Install Dependencies
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx mysql-server php8.1 php8.1-fpm php8.1-mysql \
    php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip git

# Install Composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

### 2. Setup Database
```bash
sudo mysql
CREATE DATABASE tinder_app;
CREATE USER 'tinderapp'@'localhost' IDENTIFIED BY 'password';
GRANT ALL ON tinder_app.* TO 'tinderapp'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Clone & Setup Project
```bash
cd /var/www
sudo git clone https://your-repo.git tinderapp
cd tinderapp

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup .env
cp .env.example .env
nano .env  # Update database & email settings

# Setup Laravel
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan l5-swagger:generate
php artisan config:cache
php artisan route:cache

# Permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

### 4. Setup Nginx
```bash
sudo nano /etc/nginx/sites-available/tinderapp
```

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/tinderapp/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/tinderapp /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 5. Setup SSL
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

### 6. Setup Cronjob
```bash
crontab -e
# Add:
* * * * * cd /var/www/tinderapp && php artisan schedule:run >> /dev/null 2>&1
```

---

## ✅ Post-Deployment Checklist

**Test URLs:**
```bash
curl https://yourdomain.com/api/v1/people
curl https://yourdomain.com/api/documentation
```

**Check Logs:**
```bash
tail -f storage/logs/laravel.log
```

**Test Cronjob:**
```bash
php artisan people:check-popular
```

**Test Email:**
```sql
UPDATE people SET likes_count = 52, email_sent = 0 WHERE id = 1;
```
```bash
php artisan people:check-popular
# Check inbox admin email
```

---

## 🔧 Common Issues

### 500 Error
```bash
chmod -R 775 storage bootstrap/cache
php artisan key:generate
php artisan config:clear
```

### Database Error
```bash
# Check credentials di .env
php artisan tinker
DB::connection()->getPdo();
```

### Cronjob Not Working
```bash
# Test manual
php artisan schedule:run -v
# Check path php di cron sama dengan: which php
```

### Email Not Sending
```env
# Use log driver for testing
MAIL_MAILER=log
```

---

## 📚 Full Documentation

Lihat **DEPLOYMENT_GUIDE.md** untuk:
- Detailed step-by-step instructions
- Multiple hosting options
- Troubleshooting guide
- Security best practices
- Monitoring & maintenance

---

## 🎯 Quick Commands

```bash
# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Regenerate
php artisan l5-swagger:generate
php artisan key:generate

# Migrations
php artisan migrate --force
php artisan migrate:rollback
php artisan migrate:fresh --seed

# Cronjob
php artisan people:check-popular
php artisan schedule:work
php artisan schedule:run

# Permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

**Status:** ✅ Production Ready  
**Support:** See DEPLOYMENT_GUIDE.md for details

