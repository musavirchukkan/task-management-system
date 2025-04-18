# Task Management System API

A Laravel 12 API for a Task Management System with authentication, task assignment, and task status management.

## Features

- User authentication with Laravel Sanctum
- Task management (create, assign, complete)
- Queues & Jobs for asynchronous notification handling
- Custom middleware for request execution time logging
- Service container & dependency injection
- Task scheduler for expiring overdue tasks
- Event-listener system for logging task completions
- API resource transformations

## Requirements

- PHP 8.4+
- Laravel 12
- MySQL Database
- Composer

## Installation & Setup

### 1. Clone the repository

```bash
git clone https://github.com/yourusername/task-management-system.git
cd task-management-system
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment

Copy the `.env.example` file to `.env` and update the database settings:

```bash
cp .env.example .env
```

Update the following in your `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

QUEUE_CONNECTION=database
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Run migrations

```bash
php artisan migrate
```

### 6. Create queue tables

```bash
php artisan queue:table
php artisan migrate
```

### 7. Start the development server

```bash
php artisan serve
```

### 8. Start the queue worker (in a separate terminal)

```bash
php artisan queue:work
```

### 9. Run the scheduler (in a separate terminal, for development)

```bash
php artisan schedule:work
```

## API Documentation

### Authentication Endpoints

#### Register a new user

```
POST /api/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Login

```
POST /api/login
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        // "email_verified_at": null,
        // "created_at": "2024-04-17T12:00:00.000000Z",
        // "updated_at": "2024-04-17T12:00:00.000000Z"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz123456789"
}
```

#### Logout

```
POST /api/logout
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

### Task Endpoints

#### Create a new task

```
POST /api/tasks
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

**Request Body:**
```json
{
    "title": "Complete project documentation",
    "description": "Create detailed documentation for the API project",
    "due_date": "2024-04-30T23:59:59"
}
```

#### List all tasks with filters

```
GET /api/tasks?status=pending&assigned_to=1&search=documentation&sort_by=due_date&sort_direction=asc&per_page=10
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

**Query Parameters:**
- `status`: Filter by status (pending, completed, expired)
- `assigned_to`: Filter by assigned user ID
- `search`: Search in title and description
- `sort_by`: Field to sort by (id, title, status, due_date, created_at, updated_at)
- `sort_direction`: Sort direction (asc, desc)
- `per_page`: Number of items per page

#### Get a specific task

```
GET /api/tasks/{id}
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

#### Assign a task to a user

```
PUT /api/tasks/{id}/assign
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

**Request Body:**
```json
{
    "user_id": 2
}
```

#### Mark a task as completed

```
PUT /api/tasks/{id}/complete
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

## Architecture

### Directory Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── AuthController.php
│   │       └── TaskController.php
│   ├── Middleware/
│   │   └── LogExecutionTime.php
│   ├── Requests/
│   │   ├── TaskCreateRequest.php
│   │   ├── TaskAssignRequest.php
│   │   └── TaskCompleteRequest.php
│   └── Resources/
│       └── TaskResource.php
├── Models/
│   ├── User.php
│   └── Task.php
├── Services/
│   ├── Interfaces/
│   │   └── TaskServiceInterface.php
│   └── TaskService.php
├── Jobs/
│   └── SendTaskAssignmentNotification.php
├── Console/
│   └── Commands/
│       └── ExpireOverdueTasks.php
├── Events/
│   └── TaskCompleted.php
└── Listeners/
    └── LogTaskCompletion.php
```

### SOLID Principles

1. **Single Responsibility Principle**: Each class has a single responsibility
2. **Open/Closed Principle**: Classes are open for extension but closed for modification
3. **Liskov Substitution Principle**: TaskService implements TaskServiceInterface
4. **Interface Segregation Principle**: Using specific interfaces
5. **Dependency Inversion Principle**: Dependencies are injected rather than hardcoded

## License

[MIT](https://choosealicense.com/licenses/mit/)
