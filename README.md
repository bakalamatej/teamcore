# Teamcore – Sports Club Management System

Teamcore is a Laravel-based web application for managing sports clubs, events, members, and related data.

---

## 🛠 Requirements

- PHP ≥ 8.1  
- Composer  
- Node.js + npm  
- XAMPP (Apache + MySQL)  
- Git  

---

## 🚀 Installation Guide

### 1. Clone repository

### 2. Install backend dependencies

```bash
composer install
```

### 3. Install frontend dependencies

```bash
npm install
```

### 4. Environment configuration

Copy environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 5. Database configuration

Start **XAMPP** and enable:
- Apache
- MySQL

Create a database in phpMyAdmin:

```sql
CREATE DATABASE <your_database_name>;
```

Update `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<your_database_name>
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Database migration

Run all migrations:

```bash
php artisan migrate
```

Optional reset:

```bash
php artisan migrate:fresh
```

Optional with seeders:

```bash
php artisan migrate:fresh --seed
```

### 7. Build frontend assets

```bash
npm run build
```


### 8. Run application

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

---

## 📂 Database

Database structure is fully managed using Laravel migrations:

```
database/migrations/
```

No SQL import is required. All tables are created automatically.

---

## 👥 Test Accounts

| Role | Email | Password |
|--------|--------------------|----------|
| Admin  | admin@example.com  | password |
| Coach  | coach@example.com  | password |
| Player | player@example.com | password |

---


## ⚙ Technologies Used

- **Laravel** 11 - Backend framework
- **MySQL** - Database
- **Blade** - Template engine
- **Tailwind CSS** - Styling
- **Vite** - Frontend build tool
- **Alpine.js** - Lightweight interactivity
- **XAMPP** - Local development server

---


## 📝 License

This project is created for educational purposes.
