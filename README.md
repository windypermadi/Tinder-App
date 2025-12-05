<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400">
  </a>
</p>

<p align="center">
  <strong>Backend (PHP Laravel) - Tinder App API</strong>
</p>

<p align="center">
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-8.x-blue.svg" alt="PHP"></a>
  <a href="https://laravel.com/"><img src="https://img.shields.io/badge/Laravel-8-red.svg" alt="Laravel"></a>
  <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL-Relational-green.svg" alt="MySQL"></a>
  <a href="https://swagger.io/"><img src="https://img.shields.io/badge/Swagger-API-yellow.svg" alt="Swagger"></a>
</p>

---

## Description

This repository contains the **backend API** for a Tinder-like application, developed as a **technical assignment** using **PHP Laravel 8**.  
The backend handles user management, people recommendations, like/dislike actions, and notifications via cronjob.

---

## People Data

The data model for people includes the following fields:

- **name** – Person's name  
- **age** – Person's age  
- **pictures** – Person's pictures (can be multiple)  
- **location** – Person's location  

---

## Required Features

1. List of recommended people (with pagination)  
2. Like a person  
3. Dislike a person  
4. Liked people list (API only)  
5. Cronjob: if a person receives more than 50 likes, an email is sent to the admin (any email can be used)  

---

## Infrastructure Requirements

1. Must use **PHP Laravel 8**  
2. Create **RDB schema** (database tables and relationships)  
3. Create **Swagger documentation** and deploy it to be testable  

---

## Technologies Used

- **PHP 8.x**  
- **Laravel 8**  
- **MySQL / MariaDB** (Relational Database)  
- **Swagger** (API Documentation)  

---

## 📦 Installation & Setup

### 1. Install Dependencies
```bash
composer install
```

### 2. Environment Configuration
Create `.env` file and configure database:
```bash
cp .env.example .env
```

Update database configuration:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tinder_app
DB_USERNAME=root
DB_PASSWORD=your_password

MAIL_ADMIN_EMAIL=admin@example.com
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Create Database
Create MySQL database named `tinder_app`:
```sql
CREATE DATABASE tinder_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Seed Sample Data (Optional)
```bash
php artisan db:seed
```

This will create 15 sample people for testing.

### 7. Generate Swagger Documentation
```bash
php artisan l5-swagger:generate
```

### 8. Start Development Server
```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

---

## 📚 Documentation

### Swagger API Documentation (Interactive)
Access the interactive API documentation at:
```
http://localhost:8000/api/documentation
```

You can test all API endpoints directly from the browser!

### Additional Guides
- **[SETUP_GUIDE.md](SETUP_GUIDE.md)** - Complete setup guide (Bahasa Indonesia)
- **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - Detailed API documentation (English)
- **[SWAGGER_GUIDE.md](SWAGGER_GUIDE.md)** - Swagger usage guide (Bahasa Indonesia)

---

## 🗄️ Database Schema

### Tables

#### `people` Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| name | VARCHAR | Person's name |
| age | INTEGER | Person's age |
| pictures | JSON | Array of picture URLs |
| location | VARCHAR | Person's location |
| likes_count | INTEGER | Total likes received |
| email_sent | BOOLEAN | Email notification status |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |
| deleted_at | TIMESTAMP | Soft delete timestamp |

#### `interactions` Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| from_person_id | BIGINT | Person who initiated |
| to_person_id | BIGINT | Person who received |
| type | ENUM | 'like' or 'dislike' |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

**Constraints:**
- UNIQUE(`from_person_id`, `to_person_id`) - One interaction per person pair
- Foreign keys with cascade delete

---

## 🚀 API Endpoints

Base URL: `http://localhost:8000/api/v1`

### People Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/people` | Get all people (with pagination) |
| GET | `/people/{id}` | Get single person details |
| GET | `/people/{personId}/recommended` | Get recommended people |
| GET | `/people/{personId}/liked-by` | Get people who liked this person |
| GET | `/people/{personId}/disliked-by` | Get people who disliked this person |
| GET | `/people/{personId}/disliked` | Get people disliked by this person |

### Interactions Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/interactions/like` | Like a person |
| POST | `/interactions/dislike` | Dislike a person |

---

## 🧪 Testing Examples

### Using cURL

```bash
# Get all people
curl http://localhost:8000/api/v1/people

# Get recommended people for person ID 1
curl http://localhost:8000/api/v1/people/1/recommended

# Like a person
curl -X POST http://localhost:8000/api/v1/interactions/like \
  -H "Content-Type: application/json" \
  -d '{"from_person_id": 1, "to_person_id": 2}'

# Dislike a person
curl -X POST http://localhost:8000/api/v1/interactions/dislike \
  -H "Content-Type: application/json" \
  -d '{"from_person_id": 1, "to_person_id": 3}'

# Get people who liked person ID 2
curl http://localhost:8000/api/v1/people/2/liked-by

# Get people who disliked person ID 2
curl http://localhost:8000/api/v1/people/2/disliked-by

# Get people disliked by person ID 1
curl http://localhost:8000/api/v1/people/1/disliked
```

### Using Swagger UI

1. Open `http://localhost:8000/api/documentation`
2. Select an endpoint
3. Click "Try it out"
4. Fill in parameters
5. Click "Execute"
6. View the response

---

## ⏰ Cronjob Feature

### Automatic Email Notification

The application includes a cronjob that checks for popular people (more than 50 likes) and sends email notifications to the admin.

**Command:**
```bash
php artisan people:check-popular
```

**Schedule:** Runs every hour (configured in `app/Console/Kernel.php`)

**To run the scheduler:**
```bash
php artisan schedule:work
```

**What it does:**
1. Finds people with `likes_count > 50`
2. Sends email to admin (configured in `MAIL_ADMIN_EMAIL`)
3. Marks as `email_sent = true` to avoid duplicates
4. Logs the activity

---

## 📂 Project Structure

```
Tinder-App/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── CheckPopularPeople.php      # Cronjob command
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Controller.php               # Base controller with Swagger info
│   │       └── PersonController.php         # Main API controller
│   └── Models/
│       ├── Person.php                       # Person model
│       └── Interaction.php                  # Interaction model
├── database/
│   ├── migrations/
│   │   ├── 2025_12_05_000001_create_people_table.php
│   │   └── 2025_12_05_000002_create_interactions_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── PeopleSeeder.php                 # Sample data seeder
├── routes/
│   └── api.php                              # API routes
├── config/
│   ├── l5-swagger.php                       # Swagger configuration
│   └── mail.php                             # Mail configuration
├── storage/
│   └── api-docs/
│       └── api-docs.json                    # Generated Swagger JSON
├── API_DOCUMENTATION.md                     # Detailed API docs
├── SETUP_GUIDE.md                           # Setup guide (ID)
├── SWAGGER_GUIDE.md                         # Swagger guide (ID)
└── README.md                                # This file
```

---

## 🔧 Configuration

### Mail Configuration

Update `.env` for email functionality:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tinderapp.com
MAIL_ADMIN_EMAIL=admin@example.com
```

### Swagger Configuration

Swagger is accessible at `/api/documentation` by default.  
To change this, edit `config/l5-swagger.php`:

```php
'routes' => [
    'api' => 'api/documentation',  // Change this URL
],
```

---

## 🛠️ Artisan Commands

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Seed database
php artisan db:seed

# Generate Swagger documentation
php artisan l5-swagger:generate

# Check popular people (cronjob)
php artisan people:check-popular

# Run scheduler
php artisan schedule:work

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 📦 Installed Packages

- **Laravel Framework 8.x** - Core framework
- **Laravel Sanctum** - API authentication (ready to use)
- **L5 Swagger (darkaonline/l5-swagger)** - API documentation
- **Guzzle HTTP** - HTTP client
- **Laravel CORS** - CORS handling

---

## ✨ Features Implemented

✅ **RESTful API** with proper HTTP methods  
✅ **Pagination** on list endpoints  
✅ **Database Transactions** for data integrity  
✅ **Validation** on all inputs  
✅ **Error Handling** with consistent response format  
✅ **Soft Deletes** on people table  
✅ **Interactive Swagger Documentation**  
✅ **Cronjob** for popular people notification  
✅ **Email Notifications** to admin  
✅ **Seeder** for sample data  
✅ **Relationships** between models  
✅ **Query Optimization** with proper indexes  

---

## 🚧 Future Improvements

- Add authentication (Laravel Sanctum/Passport)
- Add rate limiting
- Add more filters (age range, location-based)
- Add matching logic (mutual likes)
- Add real-time notifications (WebSockets)
- Add profile picture upload
- Add user profiles and bio
- Add blocking functionality
- Add reporting system
- Add analytics dashboard

---

## 📝 Notes

- All API responses are in JSON format
- Timestamps are in UTC and ISO 8601 format
- Pagination uses Laravel's default pagination
- The `pictures` field stores an array of URLs in JSON
- One person can only interact once with another person
- Changing from like to dislike (or vice versa) updates the existing interaction
- `likes_count` is automatically maintained
- Admin email notifications are sent only once per person

---

## 🐛 Troubleshooting

### Error: SQLSTATE[HY000] [1045] Access denied
- Check database credentials in `.env`
- Ensure MySQL service is running

### Error: Base table or view not found
- Run `php artisan migrate`

### Swagger UI not showing
- Run `php artisan l5-swagger:generate`
- Clear cache: `php artisan cache:clear`

### API returning 404
- Check if server is running: `php artisan serve`
- Verify route prefix in `routes/api.php`

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Author

Created as a technical assignment demonstrating Laravel backend API development with Swagger documentation.

---

## 🎯 Assignment Checklist

- [x] PHP Laravel 8 backend
- [x] RDB schema (MySQL)
- [x] People data (name, age, pictures, location)
- [x] List of recommended people (with pagination)
- [x] Like person functionality
- [x] Dislike person functionality
- [x] Liked people list API
- [x] Cronjob for 50+ likes email notification
- [x] Swagger documentation
- [x] Testable API endpoints

---

<p align="center">
  Made with ❤️ using Laravel
</p>
