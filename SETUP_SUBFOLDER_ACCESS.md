# 🔧 Setup Laravel di Subfolder - demo.windypermadi.com/Tinder-App

## 🎯 Kasus Anda
- **URL:** https://demo.windypermadi.com/Tinder-App
- **Subdomain:** demo.windypermadi.com (sudah ada)
- **Subfolder:** Tinder-App

---

## 📂 Struktur Folder yang Benar

### Option 1: File di Document Root Subdomain (Recommended)

```
/home/username/
└── demo/                           ← Document root demo.windypermadi.com
    └── Tinder-App/                 ← Folder project Laravel
        ├── app/
        ├── bootstrap/
        ├── config/
        ├── database/
        ├── public/                 ← Isi folder public
        │   ├── index.php
        │   ├── .htaccess
        │   └── ...
        ├── routes/
        ├── storage/
        ├── vendor/
        ├── .env
        ├── .htaccess               ← BUAT FILE INI! (penting)
        ├── artisan
        └── composer.json
```

---

## 🔧 SOLUSI: Setup untuk Subfolder Access

### Step 1: Buat .htaccess di Root Tinder-App

**Location:** `/home/username/demo/Tinder-App/.htaccess`

**Isi file .htaccess:**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /Tinder-App/
    
    # Redirect semua request ke folder public
    RewriteCond %{REQUEST_URI} !^/Tinder-App/public/
    RewriteRule ^(.*)$ /Tinder-App/public/$1 [L,QSA]
</IfModule>
```

**Cara buat via SSH:**
```bash
cd ~/demo/Tinder-App
nano .htaccess
# Paste isi di atas
# Ctrl+X, Y, Enter
```

**Cara buat via cPanel File Manager:**
1. Navigasi ke `/home/username/demo/Tinder-App/`
2. Klik **+ File**
3. Nama file: `.htaccess`
4. Edit file, paste isi di atas
5. Save

---

### Step 2: Update .htaccess di Public Folder

**Location:** `/home/username/demo/Tinder-App/public/.htaccess`

**Isi file .htaccess:**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On
    RewriteBase /Tinder-App/public/

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

**Update via SSH:**
```bash
cd ~/demo/Tinder-App/public
nano .htaccess
# Paste isi di atas (ganti yang lama)
# Ctrl+X, Y, Enter
```

---

### Step 3: Update .env File

**Location:** `/home/username/demo/Tinder-App/.env`

**Update APP_URL:**
```env
APP_NAME="Tinder App API"
APP_ENV=production
APP_KEY=base64:xxxxx
APP_DEBUG=false
APP_URL=https://demo.windypermadi.com/Tinder-App    ← PENTING! Tambahkan /Tinder-App

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=username_tinderapp
DB_USERNAME=username_dbuser
DB_PASSWORD=your_password

# ... rest of config
```

**Via SSH:**
```bash
cd ~/demo/Tinder-App
nano .env
# Update APP_URL sesuai di atas
# Ctrl+X, Y, Enter

# Clear cache setelah update
php artisan config:clear
php artisan config:cache
```

---

### Step 4: Update Swagger Configuration (Opsional)

**Location:** `/home/username/demo/Tinder-App/app/Http/Controllers/Controller.php`

Edit Swagger base URL:
```php
/**
 * @OA\Server(
 *     url="https://demo.windypermadi.com/Tinder-App",
 *     description="Production Server"
 * )
 */
```

**Regenerate Swagger:**
```bash
cd ~/demo/Tinder-App
php artisan l5-swagger:generate
```

---

### Step 5: Clear Cache & Test

```bash
cd ~/demo/Tinder-App

# Clear all cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Regenerate cache
php artisan config:cache
php artisan route:cache

# Fix permissions
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

---

## ✅ Test URLs

Setelah setup, test URL ini:

### 1. Homepage
```
https://demo.windypermadi.com/Tinder-App
```
**Expected:** Blank page atau Laravel default (OK untuk API-only)

### 2. API Endpoint
```
https://demo.windypermadi.com/Tinder-App/api/v1/people
```
**Expected:** JSON response dengan data

### 3. Swagger Documentation
```
https://demo.windypermadi.com/Tinder-App/api/documentation
```
**Expected:** Swagger UI

### 4. Test via cURL
```bash
curl https://demo.windypermadi.com/Tinder-App/api/v1/people
```

---

## 🎯 Struktur URL Lengkap

```
Base:       https://demo.windypermadi.com/Tinder-App
API:        https://demo.windypermadi.com/Tinder-App/api/v1/people
Swagger:    https://demo.windypermadi.com/Tinder-App/api/documentation

Endpoints:
- GET  /Tinder-App/api/v1/people
- GET  /Tinder-App/api/v1/people/{id}
- GET  /Tinder-App/api/v1/people/{id}/recommended
- GET  /Tinder-App/api/v1/people/{id}/liked-by
- GET  /Tinder-App/api/v1/people/{id}/disliked-by
- GET  /Tinder-App/api/v1/people/{id}/disliked
- POST /Tinder-App/api/v1/interactions/like
- POST /Tinder-App/api/v1/interactions/dislike
```

---

## 🐛 Troubleshooting

### Problem 1: Masih 404

**Check .htaccess di root Tinder-App:**
```bash
cd ~/demo/Tinder-App
cat .htaccess

# Harus ada:
# RewriteBase /Tinder-App/
# RewriteRule ^(.*)$ /Tinder-App/public/$1 [L,QSA]
```

**Jika tidak ada, buat:**
```bash
cd ~/demo/Tinder-App
nano .htaccess
# Paste dari Step 1 di atas
```

### Problem 2: API Routes 404

**Update public/.htaccess:**
```bash
cd ~/demo/Tinder-App/public
nano .htaccess
# Pastikan ada: RewriteBase /Tinder-App/public/
```

### Problem 3: CSS/JS Not Loading di Swagger

**Update APP_URL di .env:**
```env
APP_URL=https://demo.windypermadi.com/Tinder-App
```

**Clear cache:**
```bash
php artisan config:clear
php artisan config:cache
```

### Problem 4: 500 Internal Server Error

**Check permissions:**
```bash
cd ~/demo/Tinder-App
chmod -R 755 .
chmod -R 775 storage bootstrap/cache

# Check .env
cat .env | grep APP_KEY
# Harus ada value!

# Generate jika kosong
php artisan key:generate
```

---

## 📋 Complete Setup Checklist

### File Structure
- [ ] Files di: `/home/username/demo/Tinder-App/`
- [ ] Folder `public/` ada dengan `index.php`
- [ ] `.htaccess` di root `Tinder-App/` (redirect ke public)
- [ ] `.htaccess` di `public/` (Laravel routes)

### Configuration
- [ ] `.env` file exists dan configured
- [ ] `APP_URL=https://demo.windypermadi.com/Tinder-App`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` generated
- [ ] Database credentials correct

### Laravel Setup
- [ ] `composer install --no-dev` done
- [ ] `php artisan migrate --force` done
- [ ] `php artisan key:generate` done
- [ ] `php artisan l5-swagger:generate` done
- [ ] Cache cleared and regenerated

### Permissions
- [ ] `chmod -R 755 .`
- [ ] `chmod -R 775 storage bootstrap/cache`

### Testing
- [ ] Homepage works: `https://demo.windypermadi.com/Tinder-App`
- [ ] API works: `https://demo.windypermadi.com/Tinder-App/api/v1/people`
- [ ] Swagger works: `https://demo.windypermadi.com/Tinder-App/api/documentation`

---

## 🎯 Quick Commands Summary

```bash
# Navigate to project
cd ~/demo/Tinder-App

# Create root .htaccess (if not exists)
cat > .htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /Tinder-App/
    RewriteCond %{REQUEST_URI} !^/Tinder-App/public/
    RewriteRule ^(.*)$ /Tinder-App/public/$1 [L,QSA]
</IfModule>
EOF

# Update .env
nano .env
# Set: APP_URL=https://demo.windypermadi.com/Tinder-App

# Clear & cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache

# Regenerate Swagger
php artisan l5-swagger:generate

# Fix permissions
chmod -R 755 .
chmod -R 775 storage bootstrap/cache

# Test
curl https://demo.windypermadi.com/Tinder-App/api/v1/people
```

---

## 💡 Penjelasan Cara Kerja

### Request Flow:

```
User request:
https://demo.windypermadi.com/Tinder-App/api/v1/people
                                         ↓
.htaccess di /Tinder-App/
Redirect ke: /Tinder-App/public/api/v1/people
                                         ↓
.htaccess di /Tinder-App/public/
Route ke: index.php
                                         ↓
Laravel Router (routes/api.php)
Handle: /api/v1/people
                                         ↓
Controller: PersonController@index
                                         ↓
Response: JSON
```

---

## 🆚 Alternative: Symbolic Link (Advanced)

Jika cara di atas tidak work, coba cara ini:

```bash
# Move isi public ke Tinder-App root
cd ~/demo/Tinder-App
cp -r public/* .
cp public/.htaccess .

# Update index.php
nano index.php
# Ubah semua __DIR__.'/../ menjadi __DIR__.'/

# Contoh:
# require __DIR__.'/../vendor/autoload.php';
# jadi:
# require __DIR__.'/vendor/autoload.php';

# TAPI CARA INI LESS SECURE!
# Lebih baik gunakan cara .htaccess redirect di atas
```

---

## 📞 Still Having Issues?

Jika masih 404, kasih tau:

1. **Output command ini:**
```bash
cd ~/demo/Tinder-App
ls -la | head -20
ls -la public/ | head -10
cat .htaccess
cat .env | grep APP_URL
```

2. **Screenshot atau copy error message lengkap**

3. **URL yang Anda akses:**
```
https://demo.windypermadi.com/Tinder-App/
```

4. **Browser console error (F12 → Console tab)**

Saya akan bantu troubleshoot lebih detail! 🚀

