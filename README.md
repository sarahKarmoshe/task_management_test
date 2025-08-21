# Laravel Job Application Task

---

## 🚀 Setup & Installation

### Prerequisites

- PHP 8.2+
- Composer
- MySQL
- Node.js + npm
- Pusher account (optional, for real-time notifications)
- Mail provider 

### 1. Clone Repository

```bash
git clone https://github.com/sarahKarmoshe/task_management_test.git
```

### 2. Install Dependencies

```bash
composer install
npm install && npm run build
```

### 3. Environment Setup

Copy the example env file and configure database + services:

```bash
cp .env.example .env
```

Update `.env` with your settings:

```
APP_NAME="Laravel Job Task"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Sanctum
SESSION_DRIVER=cookie

# Broadcasting (using Pusher)
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=xxx
PUSHER_APP_KEY=xxx
PUSHER_APP_SECRET=xxx
PUSHER_APP_CLUSTER=mt1

MAIL_MAILER=xxx
MAIL_HOST=xxx
MAIL_PORT=2525
MAIL_USERNAME=xxx
MAIL_PASSWORD=xxx
MAIL_FROM_ADDRESS=xxx
MAIL_FROM_NAME="${APP_NAME}"
MAIL_ENCRYPTION=null

```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Run Migrations & Seeders

```bash
php artisan migrate --seed
```

### 6. Run the Application

```bash
php artisan serve
```

To open Admin Dashboard:
visit: `http://127.0.0.1:8000/admin`

### 7. Queue & Scheduler (Jobs + Daily Summary Emails)

Start the queue worker:

```bash
php artisan queue:work
```

Run scheduler (usually via cron):

```bash
php artisan schedule:run
```

---

### Key Features

- **Task Management API** (`/api/tasks`)
- **Admin Dashboard using Filament** (`/admin`)
- **Service Layer Pattern** (`TaskService`) keeps controllers clean
- **Sanctum Authentication**
- **Role-based Authorization** (Admin, User)
- **Multiple Image Uploads** per task
- **Real-Time Notifications** via Pusher + Broadcasting
- **Queued Jobs** (daily task summary email)
- **Feature Tests** with PHPUnit

---

## 🔥 API Endpoints

| Method | Endpoint            | Description                 | Auth Required |
|--------|---------------------|-----------------------------|--------------|
| POST   | /api/auth/register     | user sign up                | No           |
| POST    | /api/auth/login | user login                | No  |
| GET   | /api/auth/logout         | user logout               | Yes          |
| GET    | /api/tasks          | List all tasks (admin only) | Yes          |
| GET    | /api/tasks/{id}     | Get a specific task         | Yes   |
| POST   | /api/tasks          | Create a new task           | Yes          |
| PUT    | /api/tasks/{id}     | Update a task               | Yes (owner)  |
| DELETE | /api/tasks/{id}     | Delete a task               | Yes (owner)  |

---

## ✅ Testing


```bash
php artisan test
```
Create task Api test (feature test)

### Run Feature Tests

```bash
php artisan test --testsuite=Feature
```

---

## 📦 Scalability Considerations

- **Service Layer** for maintainability
- **Queue + Scheduler** for async tasks and daily emails
- **Broadcasting** for real-time task updates
- **Validation & Policies** for secure multi-user handling
- **Efficient schema design** ready for 50,000+ users
- **Tests** to ensure correctness and prevent regressions

