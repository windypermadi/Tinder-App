# 🚀 Deploy ke demo.windypermadi.com/Tinder-App

## 📍 Setup Spesifik untuk Anda

**Subdomain:** `demo.windypermadi.com`  
**Folder:** `Tinder-App`  
**URL Akses:** `https://demo.windypermadi.com/Tinder-App`

---

## ⚠️ PENTING: Ada 2 Cara Setup

### Option 1: Subdomain Point ke Public Folder (RECOMMENDED ✅)
```
https://demo.windypermadi.com → /home/user/demo/Tinder-App/public
```
**Keuntungan:**
- ✅ URL bersih: `demo.windypermadi.com/api/v1/people`
- ✅ Lebih aman
- ✅ Standard Laravel deployment

### Option 2: Subfolder di Main Domain
```
https://demo.windypermadi.com/Tinder-App → /home/user/public_html/Tinder-App
```
**Kelemahan:**
- ⚠️ URL jadi panjang: `demo.windypermadi.com/Tinder-App/api/v1/people`
- ⚠️ Perlu konfigurasi tambahan
- ⚠️ Kurang aman

**Saya SANGAT REKOMENDASIKAN Option 1!**

---

## 🎯 CARA 1: Subdomain Point ke Public (RECOMMENDED)

### Step 1: Setup Subdomain di cPanel

1. **Login cPanel** → **Subdomains**

2. **Create Subdomain:**
   - Subdomain: `demo`
   - Domain: `windypermadi.com`
   - Document Root: `/home/username/demo/Tinder-App/public`
   
   ⚠️ **PENTING:** Document root harus point ke folder **public**

3. **Klik "Create"**

### Step 2: Upload Files

```
/home/username/
└── demo/
    └── Tinder-App/              ← Upload semua file Laravel di sini
        ├── app/
        ├── bootstrap/
        ├── config/
        ├── database/
        ├── public/              ← Subdomain point ke sini
        ├── routes/
        ├── storage/
        ├── .env.example
        ├── artisan
        └── composer.json
```

### Step 3: Setup .env

```env
APP_NAME="Tinder App API"
APP_ENV=production
APP_KEY=                                    ← Generate nanti
APP_DEBUG=false                             ← WAJIB false!
APP_URL=https://demo.windypermadi.com       ← URL subdomain Anda

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_tinderapp              ← Nama database di hosting
DB_USERNAME=username_dbuser                 ← Username database
DB_PASSWORD=your_database_password          ← Password database

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=mail.windypermadi.com             ← SMTP dari hosting
MAIL_PORT=587
MAIL_USERNAME=noreply@windypermadi.com      ← Email Anda
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@windypermadi.com
MAIL_FROM_NAME="${APP_NAME}"
MAIL_ADMIN_EMAIL=admin@windypermadi.com     ← Email admin
```

### Step 4: Setup Laravel

```bash
# SSH ke hosting
ssh username@windypermadi.com

# Masuk ke folder project
cd ~/demo/Tinder-App

# Copy .env
cp .env.example .env
nano .env  # Edit sesuai di atas

# Generate APP_KEY
php artisan key:generate

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R username:username storage bootstrap/cache

# Run migrations
php artisan migrate --force

# Seed data (optional)
php artisan db:seed --force

# Generate Swagger
php artisan l5-swagger:generate

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Test URLs

```
✅ Homepage:    https://demo.windypermadi.com
✅ API:         https://demo.windypermadi.com/api/v1/people
✅ Swagger:     https://demo.windypermadi.com/api/documentation
```

**Perfect! Tidak ada `/Tinder-App` di URL!** 🎉

---

## 📁 CARA 2: Subfolder di Public HTML (Not Recommended)

Jika Anda tetap ingin pakai subfolder:

### Structure
```
/home/username/
└── public_html/
    └── Tinder-App/
        ├── app/
        ├── public/              ← Isi public folder harus di root
        ├── storage/
        └── ...
```

### .htaccess di public_html/Tinder-App

Buat file `.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /Tinder-App/
    
    # Redirect ke public folder
    RewriteCond %{REQUEST_URI} !^/Tinder-App/public/
    RewriteRule ^(.*)$ /Tinder-App/public/$1 [L]
</IfModule>
```

### .env Configuration
```env
APP_URL=https://demo.windypermadi.com/Tinder-App
```

### URLs akan jadi:
```
❌ Homepage:    https://demo.windypermadi.com/Tinder-App
❌ API:         https://demo.windypermadi.com/Tinder-App/api/v1/people
❌ Swagger:     https://demo.windypermadi.com/Tinder-App/api/documentation
```

**Ini tidak ideal! Lebih baik pakai Cara 1!**

---

## 🗄️ Setup Database

### 1. Create Database di cPanel

1. **cPanel** → **MySQL Databases**

2. **Create New Database:**
   ```
   Database Name: tinderapp
   Final Name: username_tinderapp
   ```

3. **Create MySQL User:**
   ```
   Username: dbuser
   Password: [buat password kuat]
   Final Name: username_dbuser
   ```

4. **Add User to Database:**
   - Select User: `username_dbuser`
   - Select Database: `username_tinderapp`
   - Privileges: **ALL PRIVILEGES** ✅
   - Click **"Make Changes"**

### 2. Import Database (Jika Ada Data dari Local)

**Via phpMyAdmin:**
1. cPanel → phpMyAdmin
2. Select database: `username_tinderapp`
3. Import → Choose file `tinder_app.sql`
4. Click **Go**

**Via SSH:**
```bash
mysql -u username_dbuser -p username_tinderapp < tinder_app.sql
```

**Atau Run Migration Fresh:**
```bash
cd ~/demo/Tinder-App
php artisan migrate --force
php artisan db:seed --force
```

---

## 📧 Setup Email

### Cara Dapat SMTP Settings

1. **cPanel** → **Email Accounts**

2. **Create Email:**
   ```
   Email: noreply@windypermadi.com
   Password: [buat password]
   ```

3. **Get SMTP Settings:**
   - Klik **"Connect Devices"** atau **"Configure Email Client"**
   - Lihat SMTP settings:
     ```
     Incoming Server: mail.windypermadi.com
     SMTP Server: mail.windypermadi.com
     SMTP Port: 587 (TLS) atau 465 (SSL)
     Username: noreply@windypermadi.com
     Password: [password yang dibuat]
     ```

4. **Update .env:**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=mail.windypermadi.com
   MAIL_PORT=587
   MAIL_USERNAME=noreply@windypermadi.com
   MAIL_PASSWORD=your_password
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=noreply@windypermadi.com
   MAIL_ADMIN_EMAIL=admin@windypermadi.com
   ```

### Test Email
```bash
cd ~/demo/Tinder-App

# Update likes count untuk testing
mysql -u username_dbuser -p
USE username_tinderapp;
UPDATE people SET likes_count = 52, email_sent = 0 WHERE id = 1;
EXIT;

# Test command
php artisan people:check-popular

# Output expected:
# Checking for popular people...
# Found 1 popular people.
# Email sent for: John Doe (52 likes)
# Done checking popular people.
```

---

## ⏰ Setup Cronjob

### 1. Open Cron Jobs

**cPanel** → **Cron Jobs**

### 2. Add Cron Job

**Common Settings:** Every Minute `* * * * *`

**Command:**
```bash
* * * * * cd /home/username/demo/Tinder-App && php artisan schedule:run >> /dev/null 2>&1
```

⚠️ **PENTING:** Ganti `username` dengan username cPanel Anda!

**Cara cek username:**
```bash
# Via SSH
pwd
# Output: /home/[username]/...
```

### 3. Alternative: Direct Command (Setiap Jam)
```bash
0 * * * * cd /home/username/demo/Tinder-App && php artisan people:check-popular >> /dev/null 2>&1
```

### 4. Verify Cronjob
```bash
# Test manual dulu
cd ~/demo/Tinder-App
php artisan schedule:run -v

# Lihat output
php artisan people:check-popular
```

---

## 🔒 Setup SSL/HTTPS (PENTING!)

### Option 1: Let's Encrypt (Free) via cPanel

1. **cPanel** → **SSL/TLS Status**

2. **Find domain:** `demo.windypermadi.com`

3. **Click** "Run AutoSSL"

4. **Wait** hingga status: ✅ Certificate installed

### Option 2: Manual SSL Installation

1. **cPanel** → **SSL/TLS**
2. **Manage SSL Sites**
3. Select domain: `demo.windypermadi.com`
4. Upload Certificate atau gunakan AutoSSL

### Force HTTPS di .htaccess

Edit `/home/username/demo/Tinder-App/public/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST%}%{REQUEST_URI} [L,R=301]

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

---

## ✅ Testing Checklist

### 1. Test Homepage
```bash
curl https://demo.windypermadi.com
```
Expected: HTML response (mungkin blank, itu OK untuk API-only app)

### 2. Test API Endpoint
```bash
curl https://demo.windypermadi.com/api/v1/people
```
Expected: JSON dengan list people

### 3. Test Swagger Documentation
```
https://demo.windypermadi.com/api/documentation
```
Expected: Swagger UI dengan semua endpoint

### 4. Test Database Connection
```bash
ssh username@windypermadi.com
cd ~/demo/Tinder-App
php artisan tinker

# Di tinker:
DB::connection()->getPdo();
# Expected: PDO object

\App\Models\Person::count();
# Expected: number (e.g., 15)

exit
```

### 5. Test Cronjob
```bash
cd ~/demo/Tinder-App
php artisan people:check-popular
```

### 6. Test Email
Update database dan run cronjob (lihat section Setup Email di atas)

### 7. Check Logs
```bash
tail -f ~/demo/Tinder-App/storage/logs/laravel.log
```

---

## 🎯 URL Testing Complete List

Setelah deploy, test semua URL ini:

```bash
# 1. Base URL
curl https://demo.windypermadi.com

# 2. API - Get all people
curl https://demo.windypermadi.com/api/v1/people

# 3. API - Get single person
curl https://demo.windypermadi.com/api/v1/people/1

# 4. API - Get recommended
curl https://demo.windypermadi.com/api/v1/people/1/recommended

# 5. API - Like person (POST)
curl -X POST https://demo.windypermadi.com/api/v1/interactions/like \
  -H "Content-Type: application/json" \
  -d '{"from_person_id": 1, "to_person_id": 2}'

# 6. API - Dislike person (POST)
curl -X POST https://demo.windypermadi.com/api/v1/interactions/dislike \
  -H "Content-Type: application/json" \
  -d '{"from_person_id": 1, "to_person_id": 3}'

# 7. API - Get liked by
curl https://demo.windypermadi.com/api/v1/people/2/liked-by

# 8. API - Get disliked by
curl https://demo.windypermadi.com/api/v1/people/2/disliked-by

# 9. Swagger UI
https://demo.windypermadi.com/api/documentation

# 10. Swagger JSON
curl https://demo.windypermadi.com/docs/api-docs.json
```

---

## 🐛 Troubleshooting

### Problem 1: 404 Not Found

**Penyebab:** Document root tidak benar

**Solusi:**
```bash
# Check document root di cPanel → Subdomains
# Harus: /home/username/demo/Tinder-App/public
#                                        ^^^^^^ PENTING!
```

### Problem 2: 500 Internal Server Error

**Solusi:**
```bash
cd ~/demo/Tinder-App

# Set permissions
chmod -R 775 storage bootstrap/cache

# Generate key jika belum
php artisan key:generate

# Clear cache
php artisan config:clear

# Check .env
cat .env | grep APP_DEBUG
# Harus: APP_DEBUG=false
```

### Problem 3: Database Connection Error

**Solusi:**
```bash
# Check credentials di .env
cat .env | grep DB_

# Test connection
php artisan tinker
DB::connection()->getPdo();

# Jika error, verify di cPanel → MySQL Databases
# - Database exists
# - User exists
# - User added to database with ALL PRIVILEGES
```

### Problem 4: Swagger Not Loading

**Solusi:**
```bash
cd ~/demo/Tinder-App

# Regenerate Swagger docs
php artisan l5-swagger:generate

# Check permission
chmod -R 775 storage/api-docs

# Clear cache
php artisan config:clear
```

### Problem 5: Email Not Sending

**Test SMTP:**
```bash
telnet mail.windypermadi.com 587
# Harus connect

# Atau gunakan log driver untuk testing
nano .env
# Change: MAIL_MAILER=log
php artisan config:clear
```

### Problem 6: Cronjob Not Working

**Check path:**
```bash
# Via SSH
which php
# Output: /usr/bin/php atau /usr/local/bin/php

# Update cron command dengan full path
/usr/bin/php /home/username/demo/Tinder-App/artisan schedule:run
```

---

## 📋 Final Checklist

Sebelum declare "DONE":

**Files & Configuration:**
- [ ] Semua file uploaded ke `/home/username/demo/Tinder-App/`
- [ ] `.env` configured dengan credentials production
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false` ← PENTING!
- [ ] `APP_KEY` generated
- [ ] `APP_URL=https://demo.windypermadi.com`

**Database:**
- [ ] Database created di cPanel
- [ ] User created dan added to database
- [ ] Migrations run: `php artisan migrate --force`
- [ ] Seeders run (optional): `php artisan db:seed --force`

**Laravel Setup:**
- [ ] `composer install --no-dev` done
- [ ] Permissions set: `chmod -R 775 storage bootstrap/cache`
- [ ] Swagger generated: `php artisan l5-swagger:generate`
- [ ] Config cached: `php artisan config:cache`
- [ ] Routes cached: `php artisan route:cache`

**Subdomain:**
- [ ] Subdomain `demo` created di cPanel
- [ ] Document root point ke: `/home/username/demo/Tinder-App/public`
- [ ] SSL certificate installed (AutoSSL)

**Email:**
- [ ] Email account created: `noreply@windypermadi.com`
- [ ] SMTP settings configured di `.env`
- [ ] Test email: `php artisan people:check-popular`

**Cronjob:**
- [ ] Cron job added di cPanel
- [ ] Command: `* * * * * cd /home/username/demo/Tinder-App && php artisan schedule:run`
- [ ] Test manual: `php artisan schedule:run -v`

**Testing:**
- [ ] Homepage accessible: `https://demo.windypermadi.com`
- [ ] API works: `https://demo.windypermadi.com/api/v1/people`
- [ ] Swagger UI loads: `https://demo.windypermadi.com/api/documentation`
- [ ] POST endpoints work (like/dislike)
- [ ] Cronjob executes successfully
- [ ] Email sends successfully
- [ ] No errors in logs

---

## 🎉 Expected Final URLs

Jika semua berhasil, aplikasi Anda akan accessible di:

```
🌐 Base URL:
   https://demo.windypermadi.com

📚 API Documentation (Swagger):
   https://demo.windypermadi.com/api/documentation

🔗 API Endpoints:
   https://demo.windypermadi.com/api/v1/people
   https://demo.windypermadi.com/api/v1/people/1
   https://demo.windypermadi.com/api/v1/people/1/recommended
   https://demo.windypermadi.com/api/v1/people/1/liked-by
   https://demo.windypermadi.com/api/v1/people/1/disliked-by
   https://demo.windypermadi.com/api/v1/people/1/disliked
   https://demo.windypermadi.com/api/v1/interactions/like
   https://demo.windypermadi.com/api/v1/interactions/dislike

📄 Swagger JSON:
   https://demo.windypermadi.com/docs/api-docs.json
```

---

## 📞 Quick Commands Reference

```bash
# SSH Login
ssh username@windypermadi.com

# Navigate to project
cd ~/demo/Tinder-App

# Clear all cache
php artisan config:clear && php artisan route:clear && php artisan view:clear && php artisan cache:clear

# Regenerate cache
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Regenerate Swagger
php artisan l5-swagger:generate

# Test cronjob
php artisan people:check-popular

# View logs (real-time)
tail -f storage/logs/laravel.log

# Check database
php artisan tinker
>>> DB::connection()->getPdo();
>>> \App\Models\Person::count();
>>> exit

# Fix permissions
chmod -R 775 storage bootstrap/cache

# Generate new APP_KEY
php artisan key:generate
```

---

**Good luck with your deployment! 🚀**

Jika ada error atau masalah, tanyakan saja! Saya siap bantu troubleshoot! 😊

