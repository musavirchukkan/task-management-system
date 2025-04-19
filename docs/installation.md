# Installation & Setup Guide

This guide provides detailed instructions for setting up the Task Management System API in both development and production environments.

**Navigation:**
- [Back to Main README](../README.md)
- [API Documentation](api.md)
- [Architecture Documentation](architecture.md)
- [Postman Collection Guide](postman.md)


### 1. Clone the repository

```bash
git clone https://github.com/musavirchukkan/task-management-system.git
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


MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
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
## Logging

The Task Management System maintains separate log files for different types of activities:

- **Request Logs**: Execution time and request details are logged to:
  ```
  storage/logs/request/request_time-{date}.log
  ```

- **Task Activity Logs**: Task completions and other task-related events are logged to:
  ```
  storage/logs/task/task-{date}.log
  ```

### Log Configuration

To configure custom log channels, add the following to your `.env` file:

```
LOG_CHANNEL=stack
LOG_REQUEST_CHANNEL=request
LOG_TASK_CHANNEL=task
```

Make sure the corresponding directories exist and are writable:

```bash
mkdir -p storage/logs/request
mkdir -p storage/logs/task
chmod -R 775 storage/logs
```

The system will automatically use these channels for the appropriate log types.

## Troubleshooting

### Common Installation Issues

- **Database connection error**: Check your database credentials in `.env` file
- **Permission denied errors**: Make sure storage and bootstrap/cache directories are writable
- **Queue worker not processing jobs**: Verify that the queue connection is properly configured
- **Missing logs**: Ensure log directories exist and have proper permissions

### Debugging

In development, you can enable more verbose logging by setting:

```
APP_DEBUG=true
LOG_LEVEL=debug
```

For API request debugging, consider using the included Postman collection with the proper environment variables set.
