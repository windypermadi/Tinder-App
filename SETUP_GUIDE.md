# Tinder App Backend - Setup Guide

## Langkah-langkah Setup

### 1. Install Dependencies
```bash
composer install
```

### 2. Konfigurasi Environment
Buat file `.env` dengan konfigurasi berikut:

```env
APP_NAME="Tinder App API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tinder_app
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
MAIL_ADMIN_EMAIL=admin@example.com
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Buat Database
Buat database MySQL dengan nama `tinder_app`:

**Menggunakan phpMyAdmin:**
1. Buka http://localhost/phpmyadmin
2. Klik tab "Database"
3. Buat database baru dengan nama `tinder_app`
4. Pilih collation: `utf8mb4_unicode_ci`

**Atau menggunakan Command Line:**
```sql
CREATE DATABASE tinder_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Jalankan Migrasi Database
```bash
php artisan migrate
```

Perintah ini akan membuat tabel:
- `people` - untuk menyimpan data orang
- `interactions` - untuk menyimpan data like/dislike

### 6. Seed Data Sample (Opsional)
```bash
php artisan db:seed
```

Ini akan membuat 15 data orang sample untuk testing.

### 7. Jalankan Development Server
```bash
php artisan serve
```

API akan berjalan di: `http://localhost:8000`

### 8. Generate Swagger Documentation
```bash
php artisan l5-swagger:generate
```

### 9. Setup Cronjob (Opsional)
Untuk mengaktifkan email notifikasi otomatis, jalankan command ini secara manual:
```bash
php artisan people:check-popular
```

Atau setup scheduler Laravel:
```bash
php artisan schedule:work
```

---

## Akses Swagger API Documentation

Setelah server berjalan, buka browser dan akses:

```
http://localhost:8000/api/documentation
```

Anda akan melihat dokumentasi API interaktif yang bisa langsung di-test dari browser!

Lihat panduan lengkap di file: **SWAGGER_GUIDE.md**

---

## Struktur Database

### Tabel `people`
```sql
- id (BIGINT, Primary Key)
- name (VARCHAR)
- age (INTEGER)
- pictures (JSON)
- location (VARCHAR)
- likes_count (INTEGER, default: 0)
- email_sent (BOOLEAN, default: false)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- deleted_at (TIMESTAMP, nullable)
```

### Tabel `interactions`
```sql
- id (BIGINT, Primary Key)
- from_person_id (BIGINT, Foreign Key -> people.id)
- to_person_id (BIGINT, Foreign Key -> people.id)
- type (ENUM: 'like', 'dislike')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
- UNIQUE(from_person_id, to_person_id)
```

---

## API Endpoints

### Base URL
```
http://localhost:8000/api/v1
```

### 1. List semua orang (testing)
```
GET /people
```

### 2. Detail satu orang
```
GET /people/{id}
```

### 3. Rekomendasi orang untuk user tertentu
```
GET /people/{personId}/recommended?per_page=10
```

### 4. Like seseorang
```
POST /interactions/like
Body: {
    "from_person_id": 1,
    "to_person_id": 2
}
```

### 5. Dislike seseorang
```
POST /interactions/dislike
Body: {
    "from_person_id": 1,
    "to_person_id": 3
}
```

### 6. List orang yang like user tertentu
```
GET /people/{personId}/liked-by
```

### 7. List orang yang dislike user tertentu
```
GET /people/{personId}/disliked-by
```

### 8. List orang yang di-dislike oleh user tertentu
```
GET /people/{personId}/disliked
```

---

## Contoh Testing dengan cURL

```bash
# 1. Lihat semua orang
curl http://localhost:8000/api/v1/people

# 2. Lihat rekomendasi untuk person ID 1
curl http://localhost:8000/api/v1/people/1/recommended

# 3. Person 1 like Person 2
curl -X POST http://localhost:8000/api/v1/interactions/like \
  -H "Content-Type: application/json" \
  -d "{\"from_person_id\": 1, \"to_person_id\": 2}"

# 4. Person 1 dislike Person 3
curl -X POST http://localhost:8000/api/v1/interactions/dislike \
  -H "Content-Type: application/json" \
  -d "{\"from_person_id\": 1, \"to_person_id\": 3}"

# 5. Lihat siapa yang like Person 2
curl http://localhost:8000/api/v1/people/2/liked-by

# 6. Lihat siapa yang dislike Person 2
curl http://localhost:8000/api/v1/people/2/disliked-by

# 7. Lihat siapa yang di-dislike oleh Person 1
curl http://localhost:8000/api/v1/people/1/disliked
```

---

## Fitur Cronjob

Command untuk cek orang populer (lebih dari 50 likes):
```bash
php artisan people:check-popular
```

Command ini akan:
1. Mencari orang dengan `likes_count > 50`
2. Kirim email ke admin (jika belum pernah dikirim)
3. Tandai sebagai sudah dikirim email

Schedule otomatis: **Setiap jam** (sudah dikonfigurasi di `app/Console/Kernel.php`)

Untuk menjalankan scheduler:
```bash
php artisan schedule:work
```

---

## File-file yang Dibuat

### Migrations
- `database/migrations/2025_12_05_000001_create_people_table.php`
- `database/migrations/2025_12_05_000002_create_interactions_table.php`

### Models
- `app/Models/Person.php`
- `app/Models/Interaction.php`

### Controllers
- `app/Http/Controllers/PersonController.php`

### Routes
- `routes/api.php` (sudah diupdate)

### Console Commands
- `app/Console/Commands/CheckPopularPeople.php`

### Seeders
- `database/seeders/PeopleSeeder.php`
- `database/seeders/DatabaseSeeder.php` (sudah diupdate)

### Documentation
- `API_DOCUMENTATION.md` (dokumentasi lengkap dalam bahasa Inggris)
- `SETUP_GUIDE.md` (panduan setup dalam bahasa Indonesia)

---

## Troubleshooting

### Error: SQLSTATE[HY000] [1045] Access denied
- Pastikan username dan password database di `.env` benar
- Pastikan MySQL service sudah running

### Error: Base table or view not found
- Jalankan `php artisan migrate`

### Error: Class not found
- Jalankan `composer dump-autoload`

### API tidak bisa diakses
- Pastikan server sudah running: `php artisan serve`
- Cek apakah port 8000 sudah digunakan aplikasi lain

---

## Catatan Penting

- Database menggunakan MySQL dengan nama `tinder_app`
- Semua response API dalam format JSON
- Pagination tersedia di endpoint list
- Field `pictures` menyimpan array URL dalam format JSON
- Satu orang hanya bisa interact (like/dislike) sekali dengan orang lain
- Mengubah dari like ke dislike (atau sebaliknya) akan update interaction yang ada
- `likes_count` otomatis dihitung dari jumlah like yang diterima
- Email admin hanya dikirim sekali per orang (tracked oleh flag `email_sent`)

Selamat mencoba! 🚀

