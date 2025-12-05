# 📧 Penjelasan Command Cronjob

## Command 1: `php artisan people:check-popular`

### 🎯 Fungsi Utama
Command ini secara manual menjalankan proses pengecekan orang-orang populer (yang mendapat lebih dari 50 likes) dan mengirim email notifikasi ke admin.

---

## 🔄 Alur Eksekusi `php artisan people:check-popular`

### Step 1: Menampilkan Pesan Awal
```
Checking for popular people...
```

### Step 2: Query Database
Command mencari orang dengan kriteria:
```php
Person::where('likes_count', '>', 50)
    ->where('email_sent', false)
    ->get();
```

**Kriteria:**
- ✅ `likes_count > 50` - Orang yang punya lebih dari 50 likes
- ✅ `email_sent = false` - Email belum pernah dikirim

### Step 3a: Jika TIDAK Ada Orang Populer
```
No new popular people found.
```
Command selesai, tidak ada yang dilakukan.

### Step 3b: Jika ADA Orang Populer
```
Found 2 popular people.
```

Untuk setiap orang populer, lakukan:

#### A. Kirim Email ke Admin
**Email dikirim ke:** `MAIL_ADMIN_EMAIL` dari config (file `.env`)

**Subject:**
```
Popular Person Alert: [Nama Orang]
```

**Isi Email (HTML):**
```html
<h2>Popular Person Alert</h2>
<p>The following person has received more than 50 likes:</p>

<ul>
    <li><strong>Name:</strong> John Doe</li>
    <li><strong>Age:</strong> 25</li>
    <li><strong>Location:</strong> Jakarta</li>
    <li><strong>Total Likes:</strong> 52</li>
</ul>

<p>This notification was sent at: 2025-12-05 14:30:00</p>
```

#### B. Update Database
```php
$person->update(['email_sent' => true]);
```
Menandai bahwa email sudah dikirim untuk mencegah duplikasi.

#### C. Tampilkan di Console
```
Email sent for: John Doe (52 likes)
```

#### D. Log ke File
Log disimpan di `storage/logs/laravel.log`:
```
[2025-12-05 14:30:00] local.INFO: Popular person notification sent
{
    "person_id": 1,
    "person_name": "John Doe",
    "likes_count": 52
}
```

#### E. Jika Error
Jika pengiriman email gagal:
```
Failed to send email for: John Doe
Error: Connection refused
```

Log error:
```
[2025-12-05 14:30:00] local.ERROR: Failed to send popular person notification
{
    "person_id": 1,
    "person_name": "John Doe",
    "error": "Connection refused"
}
```

### Step 4: Pesan Akhir
```
Done checking popular people.
```

---

## 📊 Contoh Output Lengkap

### Skenario 1: Ada 2 Orang Populer
```bash
$ php artisan people:check-popular

Checking for popular people...
Found 2 popular people.
Email sent for: John Doe (52 likes)
Email sent for: Jane Smith (68 likes)
Done checking popular people.
```

### Skenario 2: Tidak Ada Orang Populer
```bash
$ php artisan people:check-popular

Checking for popular people...
No new popular people found.
```

### Skenario 3: Ada Error Saat Kirim Email
```bash
$ php artisan people:check-popular

Checking for popular people...
Found 1 popular people.
Failed to send email for: John Doe
Error: Connection timeout
Done checking popular people.
```

---

## Command 2: `php artisan schedule:work`

### 🎯 Fungsi Utama
Command ini menjalankan **Laravel Scheduler** yang akan mengeksekusi scheduled tasks secara otomatis berdasarkan jadwal yang telah ditentukan.

---

## 🔄 Alur Eksekusi `php artisan schedule:work`

### Step 1: Mulai Scheduler
Command ini akan terus berjalan (long-running process) dan tidak pernah berhenti kecuali:
- Anda stop manual (Ctrl+C)
- Server restart
- Ada error fatal

### Step 2: Cek Schedule Setiap Menit
Scheduler akan:
1. ✅ Bangun setiap menit
2. ✅ Cek jadwal yang terdaftar di `app/Console/Kernel.php`
3. ✅ Jalankan command yang waktunya tepat

### Step 3: Eksekusi Scheduled Tasks

Di project ini, ada 1 scheduled task:
```php
$schedule->command('people:check-popular')->hourly();
```

**Artinya:**
- Command `people:check-popular` akan dijalankan otomatis **setiap jam** (jam 00:00, 01:00, 02:00, dst)

### Step 4: Output Console

Ketika Anda jalankan `php artisan schedule:work`, Anda akan melihat output seperti ini:

```bash
$ php artisan schedule:work

2025-12-05 14:00:00 Running scheduled command: php artisan people:check-popular
2025-12-05 14:00:05 Finished scheduled command: php artisan people:check-popular
2025-12-05 14:01:00 No scheduled commands are ready to run.
2025-12-05 14:02:00 No scheduled commands are ready to run.
...
2025-12-05 15:00:00 Running scheduled command: php artisan people:check-popular
2025-12-05 15:00:03 Finished scheduled command: php artisan people:check-popular
...
```

---

## 📅 Schedule Timeline

```
00:00 → people:check-popular ✅ (jalankan)
00:01 → (tidak ada yang dijadwalkan)
00:02 → (tidak ada yang dijadwalkan)
...
00:59 → (tidak ada yang dijadwalkan)
01:00 → people:check-popular ✅ (jalankan)
01:01 → (tidak ada yang dijadwalkan)
...
02:00 → people:check-popular ✅ (jalankan)
...
```

---

## 🆚 Perbedaan Kedua Command

| Aspek | `php artisan people:check-popular` | `php artisan schedule:work` |
|-------|-----------------------------------|----------------------------|
| **Execution** | Manual, sekali jalan | Otomatis, terus berjalan |
| **Kapan** | Kapanpun Anda jalankan | Setiap jam (sesuai schedule) |
| **Duration** | Selesai dalam beberapa detik | Berjalan terus sampai di-stop |
| **Use Case** | Testing, troubleshooting | Production, automation |
| **Output** | Detail lengkap proses | Log execution schedule |

---

## 🧪 Testing Cronjob

### Test Manual (Recommended untuk Development)
```bash
# Jalankan sekali untuk testing
php artisan people:check-popular
```

**Kapan menggunakan:**
- ✅ Testing apakah command berjalan dengan benar
- ✅ Debugging issues
- ✅ Melihat output detail
- ✅ Development environment

### Test Automatic (Untuk Production)
```bash
# Jalankan scheduler (biarkan berjalan terus)
php artisan schedule:work
```

**Kapan menggunakan:**
- ✅ Production environment
- ✅ Automation penuh
- ✅ Tidak perlu manual intervention
- ✅ Background process

---

## 📝 Simulasi Complete Flow

### 1. Setup Data untuk Testing

Jalankan di database atau API:
```bash
# Via API: Like person ID 1 sebanyak 51 kali
# (simulasi dengan curl loop atau Postman)

for i in {2..52}; do
  curl -X POST http://localhost:8000/api/v1/interactions/like \
    -H "Content-Type: application/json" \
    -d "{\"from_person_id\": $i, \"to_person_id\": 1}"
done
```

Atau via SQL:
```sql
UPDATE people SET likes_count = 51 WHERE id = 1;
```

### 2. Check Database
```sql
SELECT id, name, likes_count, email_sent FROM people WHERE likes_count > 50;
```

Output:
```
+----+----------+-------------+------------+
| id | name     | likes_count | email_sent |
+----+----------+-------------+------------+
|  1 | John Doe |          51 |          0 |
+----+----------+-------------+------------+
```

### 3. Run Command Manual
```bash
php artisan people:check-popular
```

Output:
```
Checking for popular people...
Found 1 popular people.
Email sent for: John Doe (51 likes)
Done checking popular people.
```

### 4. Check Database Lagi
```sql
SELECT id, name, likes_count, email_sent FROM people WHERE likes_count > 50;
```

Output:
```
+----+----------+-------------+------------+
| id | name     | likes_count | email_sent |
+----+----------+-------------+------------+
|  1 | John Doe |          51 |          1 |
+----+----------+-------------+------------+
```

**email_sent** berubah dari `0` menjadi `1` ✅

### 5. Check Log File
```bash
tail -n 20 storage/logs/laravel.log
```

Output:
```
[2025-12-05 14:30:00] local.INFO: Popular person notification sent
{"person_id":1,"person_name":"John Doe","likes_count":51}
```

### 6. Check Email
Cek inbox admin email (sesuai `MAIL_ADMIN_EMAIL` di `.env`):

**Subject:** Popular Person Alert: John Doe

**Body:**
> **Popular Person Alert**
> 
> The following person has received more than 50 likes:
> - **Name:** John Doe
> - **Age:** 25
> - **Location:** Jakarta
> - **Total Likes:** 51
> 
> This notification was sent at: 2025-12-05 14:30:00

---

## ⚙️ Konfigurasi Email

### Option 1: Mailtrap (Development - Recommended)

Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tinderapp.com
MAIL_ADMIN_EMAIL=admin@example.com
```

### Option 2: Log Driver (Testing Tanpa Email Real)

Update `.env`:
```env
MAIL_MAILER=log
MAIL_ADMIN_EMAIL=admin@example.com
```

Email akan ditulis ke `storage/logs/laravel.log` (tidak benar-benar dikirim).

### Option 3: Gmail (Production)

Update `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_ADMIN_EMAIL=admin@yourdomain.com
```

**Note:** Untuk Gmail, gunakan **App Password**, bukan password biasa.

---

## 🚨 Troubleshooting

### Problem 1: Command Tidak Ditemukan
```
Command "people:check-popular" is not defined.
```

**Solusi:**
```bash
composer dump-autoload
php artisan cache:clear
```

### Problem 2: Email Tidak Terkirim
```
Failed to send email for: John Doe
Error: Connection refused
```

**Solusi:**
1. Cek konfigurasi email di `.env`
2. Test koneksi SMTP
3. Gunakan `MAIL_MAILER=log` untuk testing

### Problem 3: Tidak Ada Orang Populer
```
No new popular people found.
```

**Penyebab:**
- Tidak ada orang dengan `likes_count > 50`, ATAU
- Semua orang populer sudah punya `email_sent = true`

**Solusi:**
```sql
-- Reset flag email_sent untuk testing
UPDATE people SET email_sent = false WHERE likes_count > 50;

-- Atau tambah likes secara manual
UPDATE people SET likes_count = 52 WHERE id = 1;
```

### Problem 4: Schedule Tidak Jalan
```
# schedule:work berjalan tapi command tidak execute
```

**Solusi:**
1. Pastikan waktu server benar
2. Check timezone di `config/app.php`
3. Lihat log di `storage/logs/laravel.log`

---

## 🎯 Best Practices

### Development Environment
```bash
# Testing manual
php artisan people:check-popular

# Gunakan log mail driver
MAIL_MAILER=log
```

### Production Environment
```bash
# Setup sebagai background service (Linux)
# Buat file: /etc/systemd/system/laravel-scheduler.service

[Unit]
Description=Laravel Scheduler
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/your/project
ExecStart=/usr/bin/php artisan schedule:work
Restart=always

[Install]
WantedBy=multi-user.target
```

Atau gunakan **traditional cron**:
```bash
# Edit crontab
crontab -e

# Tambahkan:
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📊 Monitoring

### Check Logs
```bash
# Real-time monitoring
tail -f storage/logs/laravel.log | grep "Popular person"

# Last 50 lines
tail -n 50 storage/logs/laravel.log
```

### Check Database
```sql
-- Lihat semua orang populer
SELECT id, name, likes_count, email_sent, updated_at 
FROM people 
WHERE likes_count > 50 
ORDER BY likes_count DESC;

-- Count berapa yang sudah dikirim email
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN email_sent = 1 THEN 1 ELSE 0 END) as email_sent,
    SUM(CASE WHEN email_sent = 0 THEN 1 ELSE 0 END) as pending
FROM people 
WHERE likes_count > 50;
```

---

## 📧 Email Preview

Ketika command berhasil, admin akan menerima email seperti ini:

```
From: noreply@tinderapp.com
To: admin@example.com
Subject: Popular Person Alert: John Doe

╔══════════════════════════════════════╗
║     Popular Person Alert             ║
╚══════════════════════════════════════╝

The following person has received more than 50 likes:

• Name: John Doe
• Age: 25
• Location: Jakarta  
• Total Likes: 52

This notification was sent at: 2025-12-05 14:30:00
```

---

## 🎉 Summary

### `php artisan people:check-popular`
1. ✅ Cari orang dengan likes > 50 dan email_sent = false
2. ✅ Kirim email ke admin untuk setiap orang
3. ✅ Update email_sent = true
4. ✅ Log ke file
5. ✅ Tampilkan output di console

### `php artisan schedule:work`
1. ✅ Berjalan terus sebagai background process
2. ✅ Cek schedule setiap menit
3. ✅ Jalankan `people:check-popular` setiap jam
4. ✅ Log semua execution
5. ✅ Repeat sampai di-stop

---

**File:** `app/Console/Commands/CheckPopularPeople.php`  
**Schedule:** `app/Console/Kernel.php` (hourly)  
**Config:** `config/mail.php` + `.env`  
**Logs:** `storage/logs/laravel.log`

