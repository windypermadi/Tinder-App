# 🔧 Troubleshooting 404 Error - demo.windypermadi.com

## 🎯 Kasus Anda
- **Subdomain:** demo.windypermadi.com
- **Folder:** Tinder-App
- **Masalah:** 404 Not Found

---

## 📋 Checklist Troubleshooting (Ikuti Berurutan!)

### ✅ STEP 1: Cek Lokasi Upload File

**Via cPanel File Manager:**

1. Buka **cPanel → File Manager**
2. Navigasi ke folder home Anda

**Check apakah strukturnya seperti ini:**

#### Option A: File di folder `demo/Tinder-App` (BENAR ✅)
```
/home/username/
└── demo/
    └── Tinder-App/
        ├── app/
        ├── bootstrap/
        ├── config/
        ├── database/
        ├── public/           ← Folder ini HARUS ADA!
        │   ├── index.php
        │   └── .htaccess
        ├── routes/
        ├── storage/
        ├── vendor/
        ├── .env
        ├── artisan
        └── composer.json
```

#### Option B: File di `public_html/Tinder-App` (SALAH ❌)
```
/home/username/
└── public_html/
    └── Tinder-App/
        └── ...
```

**SOLUSI jika di public_html:**
```bash
# Via SSH atau Terminal cPanel
cd ~
mkdir -p demo
mv public_html/Tinder-App demo/
```

---

### ✅ STEP 2: Cek Document Root Subdomain

**cPanel → Domains atau Subdomains:**

1. Cari subdomain: **demo.windypermadi.com**
2. Klik **Edit** atau **Manage**
3. Check **Document Root**

#### ❌ SALAH - Jika Document Root:
```
/home/username/demo/Tinder-App
```

#### ✅ BENAR - Harus:
```
/home/username/demo/Tinder-App/public
                                ^^^^^^ HARUS ADA /public
```

**CARA FIX:**

**Via cPanel:**
1. Domains/Subdomains → Find `demo.windypermadi.com`
2. Klik **Edit** atau icon pensil
3. Ubah Document Root ke: `/home/username/demo/Tinder-App/public`
4. **Save Changes**

**Via SSH:**
```bash
# Check current document root
grep -r "demo.windypermadi.com" /etc/apache2/ 2>/dev/null
# atau
grep -r "demo.windypermadi.com" /usr/local/apache/conf/ 2>/dev/null
```

---

### ✅ STEP 3: Cek Apakah File index.php Ada

**Via SSH:**
```bash
cd ~/demo/Tinder-App/public
ls -la

# Harus ada file:
# - index.php
# - .htaccess
```

**Via cPanel File Manager:**
```
Navigasi ke: /home/username/demo/Tinder-App/public/

Harus terlihat:
☑ index.php
☑ .htaccess
☑ favicon.ico
☑ robots.txt
```

**JIKA index.php TIDAK ADA:**
```bash
# Berarti file tidak lengkap, upload ulang!
# Pastikan upload SEMUA file Laravel termasuk folder public
```

---

### ✅ STEP 4: Cek File .htaccess di Public Folder

**Location:** `/home/username/demo/Tinder-App/public/.htaccess`

**Pastikan isinya seperti ini:**
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

**JIKA .htaccess TIDAK ADA atau ISI SALAH:**

**Via SSH:**
```bash
cd ~/demo/Tinder-App/public
nano .htaccess
# Copy-paste isi di atas
# Ctrl+X, Y, Enter untuk save
```

**Via cPanel File Manager:**
1. Navigasi ke `/home/username/demo/Tinder-App/public/`
2. Klik **Settings** (icon gear di pojok kanan atas)
3. ✅ Centang **Show Hidden Files (dotfiles)**
4. Klik **Save**
5. Cari file `.htaccess`
6. Jika tidak ada, klik **+ File** → buat file baru: `.htaccess`
7. Edit dan paste isi di atas

---

### ✅ STEP 5: Cek File .env dan APP_KEY

**Via SSH:**
```bash
cd ~/demo/Tinder-App

# Check apakah .env ada
ls -la .env

# Check APP_KEY
cat .env | grep APP_KEY

# Jika APP_KEY kosong atau tidak ada file .env:
cp .env.example .env
php artisan key:generate
```

**Edit .env:**
```bash
nano .env
```

**Pastikan isinya:**
```env
APP_NAME="Tinder App API"
APP_ENV=production
APP_KEY=base64:xxxxx          ← HARUS ADA VALUE!
APP_DEBUG=false
APP_URL=https://demo.windypermadi.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=username_tinderapp
DB_USERNAME=username_dbuser
DB_PASSWORD=your_password
```

---

### ✅ STEP 6: Cek Permissions

**Via SSH:**
```bash
cd ~/demo/Tinder-App

# Set permissions
chmod -R 755 .
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Check ownership
ls -la

# Jika owner bukan username Anda:
chown -R username:username .
# Ganti 'username' dengan username cPanel Anda
```

---

### ✅ STEP 7: Install Composer Dependencies

**Via SSH:**
```bash
cd ~/demo/Tinder-App

# Check apakah vendor folder ada
ls -la vendor/

# Jika tidak ada atau error:
composer install --optimize-autoloader --no-dev

# Jika composer tidak ditemukan:
php composer.phar install --optimize-autoloader --no-dev

# Jika composer.phar juga tidak ada:
curl -sS https://getcomposer.org/installer | php
php composer.phar install --optimize-autoloader --no-dev
```

---

### ✅ STEP 8: Clear Cache

**Via SSH:**
```bash
cd ~/demo/Tinder-App

# Clear semua cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Generate cache baru
php artisan config:cache
php artisan route:cache
```

---

### ✅ STEP 9: Test Access Langsung ke index.php

**Test di browser:**
```
https://demo.windypermadi.com/index.php
```

**Hasil:**
- **Jika BISA akses** → Problem di .htaccess atau mod_rewrite
- **Jika TIDAK BISA** → Problem di document root atau file location

---

### ✅ STEP 10: Cek DNS dan Propagation

**Check apakah subdomain sudah propagate:**
```bash
# Via command line
nslookup demo.windypermadi.com

# Via online tool
https://www.whatsmydns.net/
# Masukkan: demo.windypermadi.com
```

**Tunggu propagasi:** 5-60 menit (kadang bisa sampai 24 jam)

---

## 🔍 Diagnostic Commands

Jalankan command ini dan kasih tau hasilnya:

```bash
# 1. Check current directory
pwd

# 2. List files in home
ls -la ~

# 3. Check if demo folder exists
ls -la ~/demo/

# 4. Check Tinder-App folder
ls -la ~/demo/Tinder-App/

# 5. Check public folder
ls -la ~/demo/Tinder-App/public/

# 6. Check if index.php exists
cat ~/demo/Tinder-App/public/index.php | head -n 5

# 7. Check .htaccess
cat ~/demo/Tinder-App/public/.htaccess | head -n 10

# 8. Check .env
cat ~/demo/Tinder-App/.env | grep APP_

# 9. Check vendor folder
ls ~/demo/Tinder-App/vendor/ | head

# 10. Check PHP version
php -v
```

---

## 🎯 Common Scenarios & Solutions

### Scenario 1: Document Root Salah

**Symptom:** 404 di semua halaman

**Fix:**
```bash
# cPanel → Subdomains
# Edit demo.windypermadi.com
# Document Root: /home/username/demo/Tinder-App/public
```

### Scenario 2: File Belum Di-upload

**Symptom:** 404 dan log error: "File not found"

**Fix:**
```bash
# Upload ulang semua file Laravel ke:
# /home/username/demo/Tinder-App/
```

### Scenario 3: Vendor Folder Kosong

**Symptom:** 500 Error atau "Class not found"

**Fix:**
```bash
cd ~/demo/Tinder-App
composer install --no-dev
```

### Scenario 4: Permissions Salah

**Symptom:** 403 Forbidden atau 500 Error

**Fix:**
```bash
cd ~/demo/Tinder-App
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

### Scenario 5: .htaccess Tidak Ada

**Symptom:** 404 di route API, tapi homepage OK

**Fix:**
```bash
# Copy .htaccess dari Laravel
cd ~/demo/Tinder-App/public
# Buat .htaccess baru (lihat Step 4 di atas)
```

### Scenario 6: Subdomain Belum Propagate

**Symptom:** 404 atau "Site can't be reached"

**Fix:**
```
Tunggu 15-60 menit
Clear browser cache
Coba browser lain atau incognito mode
```

---

## 🚨 Quick Fix (Try This First!)

Jalankan command ini secara berurutan:

```bash
# 1. SSH ke hosting
ssh username@windypermadi.com

# 2. Masuk ke folder project
cd ~/demo/Tinder-App

# 3. Check struktur folder
ls -la

# 4. Pastikan folder public ada
ls -la public/

# 5. Check index.php ada
cat public/index.php | head -n 5

# 6. Fix permissions
chmod -R 755 .
chmod -R 775 storage bootstrap/cache

# 7. Pastikan .env ada dan benar
cp .env.example .env
nano .env
# Update database & app settings

# 8. Generate APP_KEY
php artisan key:generate

# 9. Install dependencies
composer install --no-dev

# 10. Clear & cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# 11. Test
curl http://localhost/api/v1/people
```

---

## 📞 Kasih Tau Hasilnya

Setelah coba troubleshooting di atas, kasih tau:

1. **Hasil test homepage:**
   ```
   https://demo.windypermadi.com
   ```
   Response: ?

2. **Hasil test index.php langsung:**
   ```
   https://demo.windypermadi.com/index.php
   ```
   Response: ?

3. **Hasil diagnostic commands:**
   ```bash
   ls -la ~/demo/Tinder-App/public/
   ```
   Output: ?

4. **Error message yang muncul:**
   - 404 Not Found?
   - 403 Forbidden?
   - 500 Internal Server Error?
   - "Site can't be reached"?

5. **Sudah cek Document Root di cPanel?**
   - Path saat ini: ?
   - Sudah tambahkan `/public` di akhir?

---

## 🎯 Kemungkinan Besar Masalahnya

Berdasarkan pengalaman, biasanya salah satu dari ini:

1. **90% kasus:** Document Root tidak point ke `/public` folder
2. **5% kasus:** File belum di-upload dengan lengkap
3. **3% kasus:** Subdomain belum propagate (tunggu)
4. **2% kasus:** Permissions atau .htaccess salah

---

## ✅ Checklist Cepat

Centang yang sudah Anda lakukan:

Setup:
- [ ] File sudah upload ke `/home/username/demo/Tinder-App/`
- [ ] Folder `public/` ada dan berisi `index.php`
- [ ] `.htaccess` ada di folder `public/`
- [ ] `.env` file sudah dibuat dan configured
- [ ] `vendor/` folder ada (sudah composer install)

cPanel:
- [ ] Subdomain `demo` sudah dibuat
- [ ] Document Root: `/home/username/demo/Tinder-App/public` ← **HARUS /public!**
- [ ] DNS sudah propagate (cek di whatsmydns.net)

Permissions:
- [ ] `chmod -R 775 storage`
- [ ] `chmod -R 775 bootstrap/cache`
- [ ] Owner folder benar

Laravel:
- [ ] `php artisan key:generate` sudah dijalankan
- [ ] `composer install --no-dev` sudah dijalankan
- [ ] Cache sudah di-clear

---

**Coba langkah-langkah di atas dan kasih tau hasilnya ya!** 

Atau kalau mau lebih cepat, kasih tau hasil dari command ini:

```bash
cd ~/demo/Tinder-App
ls -la public/
cat .env | grep APP_URL
```

Saya siap bantu lebih detail! 🚀😊

