# ⚡ Quick Start Guide - Tinder App Backend

## 🚀 Setup dalam 5 Menit

### 1️⃣ Database Setup
```sql
CREATE DATABASE tinder_app;
```

### 2️⃣ Environment Configuration
Create `.env` file:
```env
DB_DATABASE=tinder_app
DB_USERNAME=root
DB_PASSWORD=your_password
MAIL_ADMIN_EMAIL=admin@example.com
```

### 3️⃣ Install & Run
```bash
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan l5-swagger:generate
php artisan serve
```

### 4️⃣ Access
- **API Base:** http://localhost:8000/api/v1
- **Swagger UI:** http://localhost:8000/api/documentation

---

## 📋 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/people` | List all people |
| GET | `/api/v1/people/{id}` | Get person detail |
| GET | `/api/v1/people/{personId}/recommended` | Get recommendations |
| GET | `/api/v1/people/{personId}/liked-by` | Get liked by list |
| GET | `/api/v1/people/{personId}/disliked-by` | Get disliked by list |
| GET | `/api/v1/people/{personId}/disliked` | Get disliked list |
| POST | `/api/v1/interactions/like` | Like someone |
| POST | `/api/v1/interactions/dislike` | Dislike someone |

---

## 🧪 Test via Swagger

1. Open: http://localhost:8000/api/documentation
2. Click any endpoint
3. Click "Try it out"
4. Fill parameters
5. Click "Execute"
6. See results!

---

## 🔧 Useful Commands

```bash
# Migration
php artisan migrate
php artisan migrate:fresh --seed

# Swagger
php artisan l5-swagger:generate

# Cronjob (check popular people)
php artisan people:check-popular

# Start server
php artisan serve

# Clear cache
php artisan cache:clear
```

---

## 📚 Full Documentation

- **README.md** - Complete overview
- **SETUP_GUIDE.md** - Detailed setup (Bahasa Indonesia)
- **API_DOCUMENTATION.md** - API details (English)
- **SWAGGER_GUIDE.md** - Swagger guide (Bahasa Indonesia)
- **INSTALLATION_COMPLETE.md** - Full report

---

## ✅ Features

✅ 8 API Endpoints  
✅ Swagger Documentation  
✅ Database Migrations  
✅ Sample Data Seeder  
✅ Cronjob Email Notification  
✅ Pagination  
✅ Input Validation  
✅ Error Handling  

---

## 🎯 Assignment Checklist

- [x] Laravel 8 Backend
- [x] MySQL Database Schema
- [x] People Data (name, age, pictures, location)
- [x] Recommended people list (with pagination)
- [x] Like/Dislike functionality
- [x] Liked people list API
- [x] Cronjob for 50+ likes notification
- [x] Swagger Documentation (deployed & testable)

---

**Status:** ✅ PRODUCTION READY

Need help? Check the full documentation files! 🚀

