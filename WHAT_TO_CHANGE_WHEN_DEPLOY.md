# 🔄 Checklist: Apa yang Harus Diganti Saat Pindah ke Hosting

## 📝 File .env (PALING PENTING!)

### ❌ Local (.env di komputer Anda)
```env
APP_NAME="Tinder App API"
APP_ENV=local                          ← GANTI INI
APP_KEY=base64:xxx...
APP_DEBUG=true                         ← GANTI INI (PENTING!)
APP_URL=http://localhost:8000          ← GANTI INI

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tinder_app                 ← GANTI INI
DB_USERNAME=root                       ← GANTI INI
DB_PASSWORD=                           ← GANTI INI

MAIL_MAILER=smtp
MAIL_HOST=mailhog                      ← GANTI INI
MAIL_PORT=1025                         ← GANTI INI
MAIL_USERNAME=null                     ← GANTI INI
MAIL_PASSWORD=null                     ← GANTI INI
MAIL_ENCRYPTION=null                   ← GANTI INI
MAIL_FROM_ADDRESS="hello@example.com" ← GANTI INI
MAIL_ADMIN_EMAIL=admin@example.com     ← GANTI INI
```

### ✅ Production (.env di hosting)
```env
APP_NAME="Tinder App API"
APP_ENV=production                     ← UBAH ke production
APP_KEY=base64:xxx...                  ← Generate baru!
APP_DEBUG=false                        ← WAJIB false!
APP_URL=https://yourdomain.com         ← Domain Anda

DB_CONNECTION=mysql
DB_HOST=localhost                      ← Biasanya tetap localhost
DB_PORT=3306
DB_DATABASE=cpaneluser_tinderapp       ← Nama DB di hosting
DB_USERNAME=cpaneluser_dbuser          ← Username DB hosting
DB_PASSWORD=strong_password_here       ← Password DB hosting

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com          ← SMTP hosting Anda
MAIL_PORT=587                          ← Port 587 atau 465
MAIL_USERNAME=noreply@yourdomain.com   ← Email hosting Anda
MAIL_PASSWORD=email_password_here      ← Password email
MAIL_ENCRYPTION=tls                    ← tls atau ssl
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_ADMIN_EMAIL=admin@yourdomain.com  ← Email admin asli
```

---

## 🔑 Yang WAJIB Diganti

### 1. APP_ENV
```env
# Local
APP_ENV=local

# Production (WAJIB!)
APP_ENV=production
```

### 2. APP_DEBUG
```env
# Local
APP_DEBUG=true

# Production (WAJIB FALSE!)
APP_DEBUG=false
```
⚠️ **SANGAT PENTING!** Jika true, user bisa lihat error details & struktur database!

### 3. APP_KEY
```bash
# Generate key baru di hosting
php artisan key:generate
```
⚠️ **Jangan pakai APP_KEY yang sama dengan local!**

### 4. APP_URL
```env
# Local
APP_URL=http://localhost:8000

# Production
APP_URL=https://yourdomain.com
```

### 5. Database Credentials
```env
# Local (XAMPP)
DB_HOST=127.0.0.1
DB_DATABASE=tinder_app
DB_USERNAME=root
DB_PASSWORD=

# Production (Hosting)
DB_HOST=localhost                    # Kadang: mysql.yourdomain.com
DB_DATABASE=cpaneluser_tinderapp     # Format: username_dbname
DB_USERNAME=cpaneluser_dbuser        # Format: username_dbuser
DB_PASSWORD=strong_password          # Password yang Anda buat
```

### 6. Email Configuration
```env
# Local (Testing - MailHog/Log)
MAIL_MAILER=log
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null

# Production (Real Email)
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com        # Dari hosting Anda
MAIL_PORT=587                        # 587 (TLS) atau 465 (SSL)
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls                  # tls atau ssl
MAIL_ADMIN_EMAIL=admin@yourdomain.com
```

---

## 📂 File & Folder yang Perlu Disesuaikan

### 1. Composer Dependencies
```bash
# Di local (untuk development)
composer install

# Di hosting (untuk production)
composer install --optimize-autoloader --no-dev
```
Flag `--no-dev` = tidak install package development (testing, debugging)

### 2. Storage Permissions
```bash
# Di hosting, set permission:
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 3. Public Folder Location
```
# Local
http://localhost:8000/api/v1/people
                      ↑ Laravel serve handle ini

# Production (Option 1: Document Root)
https://yourdomain.com/api/v1/people
      ↑ Point domain ke folder: /laravel-app/public

# Production (Option 2: Symlink)
public_html → symlink → laravel-app/public
```

---

## 🔐 Security Settings (WAJIB!)

### File .env Production
```env
# WAJIB di production
APP_ENV=production
APP_DEBUG=false           # ← JANGAN LUPA!
APP_KEY=xxx               # ← Generate baru
```

### File .htaccess (sudah ada, tapi pastikan ada)
```apache
# Di laravel-app/public/.htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On
    # ... (sudah ada di Laravel)
</IfModule>

# Protect .env file
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

---

## 🗄️ Database

### 1. Export dari Local
```bash
# Export database
mysqldump -u root -p tinder_app > tinder_app.sql
```

### 2. Import ke Hosting
```bash
# Via SSH
mysql -u cpaneluser_dbuser -p cpaneluser_tinderapp < tinder_app.sql

# Atau via phpMyAdmin:
# Login → Select Database → Import → Choose file → Go
```

### 3. Atau Run Migration Fresh
```bash
# Di hosting (jika belum ada data penting)
php artisan migrate --force
php artisan db:seed --force
```

---

## ⚙️ Cache & Optimization

### Local (Development)
```bash
# Biasanya tidak perlu cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Production (Hosting)
```bash
# Cache semua untuk performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan l5-swagger:generate
```

---

## 📧 Email Configuration Detail

### Cara Dapat SMTP Credentials

#### Option 1: Email Hosting (cPanel)
1. cPanel → Email Accounts
2. Buat email: `noreply@yourdomain.com`
3. Get SMTP settings:
   - Host: `mail.yourdomain.com` atau `smtp.yourdomain.com`
   - Port: 587 (TLS) atau 465 (SSL)
   - Username: `noreply@yourdomain.com`
   - Password: password yang Anda buat

#### Option 2: Gmail (Free)
1. Buat App Password di Google Account
2. Settings:
   ```env
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your-email@gmail.com
   MAIL_PASSWORD=your-app-password
   MAIL_ENCRYPTION=tls
   ```

#### Option 3: Mailtrap (Development Only)
```env
# Hanya untuk testing, email tidak benar-benar terkirim
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

#### Option 4: Log Driver (Testing)
```env
# Email masuk ke storage/logs/laravel.log
MAIL_MAILER=log
```

---

## 🕐 Cronjob Configuration

### Local (Testing Manual)
```bash
# Run manual
php artisan people:check-popular
```

### Production (Auto via Cron)

#### Shared Hosting (cPanel)
1. cPanel → Cron Jobs
2. Add:
```bash
* * * * * cd /home/cpaneluser/laravel-app && php artisan schedule:run >> /dev/null 2>&1
```

#### VPS (Linux)
```bash
crontab -e

# Add:
* * * * * cd /var/www/laravel-app && php artisan schedule:run >> /dev/null 2>&1
```

⚠️ **PENTING:** Path `php` harus benar!
```bash
# Check path PHP
which php

# Gunakan full path jika perlu
* * * * * /usr/bin/php /home/user/laravel-app/artisan schedule:run >> /dev/null 2>&1
```

---

## 🌐 URL & Domain Configuration

### Local URLs
```
Base: http://localhost:8000
API: http://localhost:8000/api/v1
Swagger: http://localhost:8000/api/documentation
```

### Production URLs
```
Base: https://yourdomain.com
API: https://yourdomain.com/api/v1
Swagger: https://yourdomain.com/api/documentation
```

### Update di Code (Jika Ada Hardcoded URLs)
```php
// JANGAN hardcode URLs!
// ❌ BAD:
$url = 'http://localhost:8000/api/v1/people';

// ✅ GOOD:
$url = config('app.url') . '/api/v1/people';
// atau
$url = route('people.index');
```

---

## 📋 Quick Checklist

Copy checklist ini saat deploy:

### Sebelum Upload
- [ ] Test semua fitur di local
- [ ] Export database (jika ada data)
- [ ] Update `.env.example` dengan settings production (tanpa password)
- [ ] Commit & push ke Git (optional)

### Setelah Upload ke Hosting
- [ ] Copy `.env.example` → `.env`
- [ ] Update `.env`:
  - [ ] `APP_ENV=production`
  - [ ] `APP_DEBUG=false`
  - [ ] `APP_URL=https://yourdomain.com`
  - [ ] Database credentials
  - [ ] Email SMTP settings
- [ ] Generate APP_KEY: `php artisan key:generate`
- [ ] Install composer: `composer install --no-dev`
- [ ] Set permissions: `chmod -R 775 storage bootstrap/cache`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed data: `php artisan db:seed --force` (optional)
- [ ] Generate Swagger: `php artisan l5-swagger:generate`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Setup cronjob
- [ ] Point domain ke folder public
- [ ] Test API endpoints
- [ ] Test Swagger UI
- [ ] Test cronjob manual
- [ ] Test email sending
- [ ] Setup SSL/HTTPS

---

## ⚠️ Common Mistakes to Avoid

### 1. ❌ Lupa Set APP_DEBUG=false
```env
APP_DEBUG=true  # ← BAHAYA! User bisa lihat error & database info
```

### 2. ❌ Pakai Database Credentials Local
```env
# Di production masih pakai:
DB_USERNAME=root
DB_PASSWORD=
# ← Ini salah! Harus ganti dengan credentials hosting
```

### 3. ❌ Lupa Generate APP_KEY Baru
```bash
# WAJIB generate baru di hosting
php artisan key:generate
```

### 4. ❌ Permission Salah
```bash
# Jika permission salah, akan error 500
# WAJIB set:
chmod -R 775 storage bootstrap/cache
```

### 5. ❌ Cronjob Path Salah
```bash
# ❌ SALAH
* * * * * php artisan schedule:run

# ✅ BENAR (dengan full path)
* * * * * cd /home/user/laravel-app && php artisan schedule:run >> /dev/null 2>&1
```

### 6. ❌ Lupa Point Domain ke Public Folder
```
# Domain harus point ke:
/home/user/laravel-app/public
                      ^^^^^^
# Bukan ke:
/home/user/laravel-app  ← SALAH!
```

---

## 📊 Comparison Table

| Setting | Local (Development) | Production (Hosting) |
|---------|-------------------|---------------------|
| APP_ENV | local | production |
| APP_DEBUG | true | **false** |
| APP_URL | http://localhost:8000 | https://yourdomain.com |
| DB_HOST | 127.0.0.1 | localhost |
| DB_DATABASE | tinder_app | cpaneluser_tinderapp |
| DB_USERNAME | root | cpaneluser_dbuser |
| DB_PASSWORD | (empty) | strong_password |
| MAIL_MAILER | log | smtp |
| MAIL_HOST | mailhog | smtp.yourdomain.com |
| Composer | `composer install` | `composer install --no-dev` |
| Cache | No cache | Cached (config, route, view) |
| Permissions | Auto OK | Manual set (775) |
| Cronjob | Manual run | Auto via cron |
| SSL | No | **Yes (HTTPS)** |

---

## 🎯 Summary: Yang HARUS Diganti

### File .env (9 Settings Penting)
1. ✅ `APP_ENV` → production
2. ✅ `APP_DEBUG` → false (WAJIB!)
3. ✅ `APP_KEY` → Generate baru
4. ✅ `APP_URL` → https://yourdomain.com
5. ✅ `DB_DATABASE` → nama database hosting
6. ✅ `DB_USERNAME` → username database hosting
7. ✅ `DB_PASSWORD` → password database hosting
8. ✅ `MAIL_*` settings → SMTP hosting/Gmail
9. ✅ `MAIL_ADMIN_EMAIL` → email admin yang real

### Commands yang Harus Dijalankan
```bash
# 1. Generate key
php artisan key:generate

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Set permissions
chmod -R 775 storage bootstrap/cache

# 4. Run migrations
php artisan migrate --force

# 5. Seed data (optional)
php artisan db:seed --force

# 6. Generate Swagger
php artisan l5-swagger:generate

# 7. Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Setup Tambahan
- ✅ Point domain ke folder `/public`
- ✅ Setup cronjob untuk scheduler
- ✅ Setup SSL/HTTPS certificate
- ✅ Test semua endpoint

---

## 📞 Troubleshooting Quick Fix

Jika ada masalah setelah deploy:

```bash
# 1. Clear all cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 2. Fix permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 3. Regenerate key
php artisan key:generate

# 4. Check .env
cat .env | grep APP_DEBUG
# Harus: APP_DEBUG=false
```

---

**File ini adalah panduan lengkap apa yang harus diganti saat deploy!**

Simpan dan gunakan sebagai checklist saat memindahkan aplikasi ke hosting! ✅

