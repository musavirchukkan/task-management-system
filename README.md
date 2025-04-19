# Task Management System API

A Laravel 12 API for a Task Management System with authentication, task assignment, and task status management.

## Documentation

This project's documentation is split into multiple files for easier navigation:

- [Installation & Setup Guide](docs/installation.md) - Instructions for development and production setup
- [API Documentation](docs/api.md) - Complete API reference with examples and error handling
- [Architecture](docs/architecture.md) - System architecture, flow diagrams, and design principles
- [Postman Collection Guide](docs/postman.md) - How to use the included Postman collection

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

## Quick Start

```bash
# Clone the repository
git clone https://github.com/yourusername/task-management-system.git
cd task-management-system

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Start the development server
php artisan serve
```

For detailed setup instructions, see the [Installation & Setup Guide](docs/installation.md).

## License

[MIT](https://choosealicense.com/licenses/mit/)
