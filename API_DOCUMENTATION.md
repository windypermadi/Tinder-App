# Tinder App - Backend API Documentation

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Environment Configuration
Copy the example environment file:
```bash
copy .env.example .env
```

Update the `.env` file with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tinder_app
DB_USERNAME=root
DB_PASSWORD=your_password
```

Configure mail settings (for admin notifications):
```
MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_email@example.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_ADMIN_EMAIL=admin@example.com
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Create Database
Create a MySQL database named `tinder_app`:
```sql
CREATE DATABASE tinder_app;
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Seed Sample Data
```bash
php artisan db:seed
```

Or seed only people data:
```bash
php artisan db:seed --class=PeopleSeeder
```

### 7. Start Development Server
```bash
php artisan serve
```

The API will be available at: `http://localhost:8000/api/v1`

### 8. Setup Cronjob (Optional)
To enable the automatic email notification for popular people, add this to your crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or manually run the command:
```bash
php artisan people:check-popular
```

---

## Database Schema

### People Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| name | VARCHAR | Person's name |
| age | INTEGER | Person's age |
| pictures | JSON | Array of picture URLs |
| location | VARCHAR | Person's location |
| likes_count | INTEGER | Total likes received (default: 0) |
| email_sent | BOOLEAN | Whether admin email was sent (default: false) |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |
| deleted_at | TIMESTAMP | Soft delete timestamp |

### Interactions Table
| Column | Type | Description |
|--------|------|-------------|
| id | BIGINT | Primary key |
| from_person_id | BIGINT | Person who initiated the interaction |
| to_person_id | BIGINT | Person who received the interaction |
| type | ENUM | Type of interaction: 'like' or 'dislike' |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Last update timestamp |

**Unique Constraint:** `(from_person_id, to_person_id)` - One person can only interact once with another person

---

## API Endpoints

### Base URL
```
http://localhost:8000/api/v1
```

---

### 1. Get All People (Testing)
Get a paginated list of all people.

**Endpoint:** `GET /people`

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 15)
- `page` (optional): Page number (default: 1)

**Example Request:**
```bash
curl http://localhost:8000/api/v1/people?per_page=10&page=1
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "age": 25,
                "pictures": [
                    "https://randomuser.me/api/portraits/men/1.jpg",
                    "https://randomuser.me/api/portraits/men/2.jpg"
                ],
                "location": "Jakarta",
                "likes_count": 0,
                "email_sent": false,
                "created_at": "2025-12-05T10:00:00.000000Z",
                "updated_at": "2025-12-05T10:00:00.000000Z"
            }
        ],
        "first_page_url": "http://localhost:8000/api/v1/people?page=1",
        "from": 1,
        "last_page": 2,
        "last_page_url": "http://localhost:8000/api/v1/people?page=2",
        "links": [...],
        "next_page_url": "http://localhost:8000/api/v1/people?page=2",
        "path": "http://localhost:8000/api/v1/people",
        "per_page": 10,
        "prev_page_url": null,
        "to": 10,
        "total": 15
    }
}
```

---

### 2. Get Single Person
Get details of a specific person.

**Endpoint:** `GET /people/{id}`

**Example Request:**
```bash
curl http://localhost:8000/api/v1/people/1
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "age": 25,
        "pictures": [
            "https://randomuser.me/api/portraits/men/1.jpg",
            "https://randomuser.me/api/portraits/men/2.jpg"
        ],
        "location": "Jakarta",
        "likes_count": 0,
        "email_sent": false,
        "created_at": "2025-12-05T10:00:00.000000Z",
        "updated_at": "2025-12-05T10:00:00.000000Z"
    }
}
```

---

### 3. Get Recommended People
Get a list of recommended people for a specific person (excludes already interacted people).

**Endpoint:** `GET /people/{personId}/recommended`

**Path Parameters:**
- `personId`: ID of the current person viewing recommendations

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 10)
- `page` (optional): Page number (default: 1)

**Example Request:**
```bash
curl http://localhost:8000/api/v1/people/1/recommended?per_page=5
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 2,
                "name": "Jane Smith",
                "age": 23,
                "pictures": [
                    "https://randomuser.me/api/portraits/women/1.jpg"
                ],
                "location": "Bandung",
                "likes_count": 0,
                "email_sent": false,
                "created_at": "2025-12-05T10:00:00.000000Z",
                "updated_at": "2025-12-05T10:00:00.000000Z"
            }
        ],
        "per_page": 5,
        "total": 14
    }
}
```

---

### 4. Like a Person
Record that one person likes another person.

**Endpoint:** `POST /interactions/like`

**Request Body:**
```json
{
    "from_person_id": 1,
    "to_person_id": 2
}
```

**Validation Rules:**
- `from_person_id`: Required, must exist in people table
- `to_person_id`: Required, must exist in people table, must be different from from_person_id

**Example Request:**
```bash
curl -X POST http://localhost:8000/api/v1/interactions/like \
  -H "Content-Type: application/json" \
  -d '{"from_person_id": 1, "to_person_id": 2}'
```

**Example Success Response:**
```json
{
    "success": true,
    "message": "Person liked successfully"
}
```

**Example Error Response (Validation):**
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "to_person_id": [
            "The to person id field must be different from from person id."
        ]
    }
}
```

---

### 5. Dislike a Person
Record that one person dislikes another person.

**Endpoint:** `POST /interactions/dislike`

**Request Body:**
```json
{
    "from_person_id": 1,
    "to_person_id": 3
}
```

**Validation Rules:**
- `from_person_id`: Required, must exist in people table
- `to_person_id`: Required, must exist in people table, must be different from from_person_id

**Example Request:**
```bash
curl -X POST http://localhost:8000/api/v1/interactions/dislike \
  -H "Content-Type: application/json" \
  -d '{"from_person_id": 1, "to_person_id": 3}'
```

**Example Success Response:**
```json
{
    "success": true,
    "message": "Person disliked successfully"
}
```

---

### 6. Get Liked By List
Get a list of people who liked a specific person.

**Endpoint:** `GET /people/{personId}/liked-by`

**Path Parameters:**
- `personId`: ID of the person to check who liked them

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 10)
- `page` (optional): Page number (default: 1)

**Example Request:**
```bash
curl http://localhost:8000/api/v1/people/2/liked-by
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "John Doe",
                "age": 25,
                "pictures": [
                    "https://randomuser.me/api/portraits/men/1.jpg"
                ],
                "location": "Jakarta",
                "likes_count": 5,
                "email_sent": false,
                "created_at": "2025-12-05T10:00:00.000000Z",
                "updated_at": "2025-12-05T10:00:00.000000Z",
                "interactions_given": [
                    {
                        "id": 1,
                        "from_person_id": 1,
                        "to_person_id": 2,
                        "type": "like",
                        "created_at": "2025-12-05T10:30:00.000000Z",
                        "updated_at": "2025-12-05T10:30:00.000000Z"
                    }
                ]
            }
        ],
        "per_page": 10,
        "total": 1
    }
}
```

---

### 7. Get Disliked By List
Get a list of people who disliked a specific person.

**Endpoint:** `GET /people/{personId}/disliked-by`

**Path Parameters:**
- `personId`: ID of the person to check who disliked them

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 10)
- `page` (optional): Page number (default: 1)

**Example Request:**
```bash
curl http://localhost:8000/api/v1/people/2/disliked-by
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 3,
                "name": "Michael Johnson",
                "age": 28,
                "pictures": [
                    "https://randomuser.me/api/portraits/men/3.jpg"
                ],
                "location": "Surabaya",
                "likes_count": 2,
                "email_sent": false,
                "created_at": "2025-12-05T10:00:00.000000Z",
                "updated_at": "2025-12-05T10:00:00.000000Z",
                "interactions_given": [
                    {
                        "id": 5,
                        "from_person_id": 3,
                        "to_person_id": 2,
                        "type": "dislike",
                        "created_at": "2025-12-05T10:45:00.000000Z",
                        "updated_at": "2025-12-05T10:45:00.000000Z"
                    }
                ]
            }
        ],
        "per_page": 10,
        "total": 1
    }
}
```

---

### 8. Get Disliked People List
Get a list of people that the current person has disliked.

**Endpoint:** `GET /people/{personId}/disliked`

**Path Parameters:**
- `personId`: ID of the current person

**Query Parameters:**
- `per_page` (optional): Number of items per page (default: 10)
- `page` (optional): Page number (default: 1)

**Example Request:**
```bash
curl http://localhost:8000/api/v1/people/1/disliked
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 4,
                "name": "Emily Davis",
                "age": 26,
                "pictures": [
                    "https://randomuser.me/api/portraits/women/3.jpg"
                ],
                "location": "Bali",
                "likes_count": 8,
                "email_sent": false,
                "created_at": "2025-12-05T10:00:00.000000Z",
                "updated_at": "2025-12-05T10:00:00.000000Z",
                "interactions_received": [
                    {
                        "id": 7,
                        "from_person_id": 1,
                        "to_person_id": 4,
                        "type": "dislike",
                        "created_at": "2025-12-05T11:00:00.000000Z",
                        "updated_at": "2025-12-05T11:00:00.000000Z"
                    }
                ]
            }
        ],
        "per_page": 10,
        "total": 1
    }
}
```

---

## Cronjob Command

### Check Popular People
This command checks for people with more than 50 likes and sends an email notification to the admin.

**Command:**
```bash
php artisan people:check-popular
```

**Schedule:** 
The command is scheduled to run hourly (configured in `app/Console/Kernel.php`)

**What it does:**
1. Finds all people with `likes_count > 50` and `email_sent = false`
2. Sends an email to the admin (configured in `MAIL_ADMIN_EMAIL` env variable)
3. Marks the person's `email_sent` flag as `true` to avoid duplicate emails
4. Logs the activity

**Email Content:**
- Person's name
- Person's age
- Person's location
- Total likes count
- Timestamp

---

## Error Responses

All endpoints follow a consistent error response format:

**404 Not Found:**
```json
{
    "success": false,
    "message": "Person not found"
}
```

**422 Validation Error:**
```json
{
    "success": false,
    "message": "Validation error",
    "errors": {
        "field_name": [
            "Error message here"
        ]
    }
}
```

**500 Server Error:**
```json
{
    "success": false,
    "message": "Error message",
    "error": "Detailed error information"
}
```

---

## Testing the API

### Example Workflow

1. **Get all people to see available IDs:**
```bash
curl http://localhost:8000/api/v1/people
```

2. **Get recommended people for person ID 1:**
```bash
curl http://localhost:8000/api/v1/people/1/recommended
```

3. **Person 1 likes person 2:**
```bash
curl -X POST http://localhost:8000/api/v1/interactions/like \
  -H "Content-Type: application/json" \
  -d '{"from_person_id": 1, "to_person_id": 2}'
```

4. **Person 1 dislikes person 3:**
```bash
curl -X POST http://localhost:8000/api/v1/interactions/dislike \
  -H "Content-Type: application/json" \
  -d '{"from_person_id": 1, "to_person_id": 3}'
```

5. **Check who liked person 2:**
```bash
curl http://localhost:8000/api/v1/people/2/liked-by
```

6. **Get recommended people for person 1 again (should exclude person 2 and 3):**
```bash
curl http://localhost:8000/api/v1/people/1/recommended
```

7. **Manually trigger the popular people check:**
```bash
php artisan people:check-popular
```

---

## Notes

- The API uses JSON for all request and response bodies
- All timestamps are in UTC and ISO 8601 format
- Pagination is available on list endpoints using Laravel's default pagination
- The `pictures` field stores an array of URLs in JSON format
- Soft deletes are enabled on the `people` table
- A person can only interact (like/dislike) with another person once
- Changing from like to dislike (or vice versa) updates the existing interaction
- The `likes_count` field is automatically maintained when interactions are created/updated
- Admin email notifications are sent only once per person (tracked by `email_sent` flag)

---

## Future Improvements

- Add authentication (Laravel Sanctum or Passport)
- Add rate limiting
- Add more filters for recommendations (age range, location, etc.)
- Add matching logic (when both people like each other)
- Add real-time notifications using WebSockets
- Add profile pictures upload functionality
- Add user profiles and bio
- Add blocking functionality
- Add reporting functionality
- Add analytics dashboard

