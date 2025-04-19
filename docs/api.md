# API Documentation

This document provides detailed information about the Task Management System API endpoints, request/response formats, and error handling.

**Navigation:**
- [Back to Main README](../README.md)
- [Installation & Setup Guide](installation.md)
- [Architecture Documentation](architecture.md)
- [Postman Collection Guide](postman.md)

## Base URL

All API URLs referenced in this documentation have the following base:

```
https://yourdomain.com/api
```

For local development, use:

```
http://localhost:8000/api
```

## Authentication

All API requests (except Register and Login) require authentication using Laravel Sanctum tokens.

To authenticate, include the token in the Authorization header:

```
Authorization: Bearer {your_token}
```

## Authentication Endpoints

### Register a new user

```
POST /register
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

**Response (201 Created):**
```json
{
    "message": "User registered successfully",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz123456789"
}
```

### Login

```
POST /login
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password"
}
```

**Response (200 OK):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com"
    },
    "token": "1|abcdefghijklmnopqrstuvwxyz123456789"
}
```

### Logout

```
POST /logout
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

**Response (200 OK):**
```json
{
    "message": "Logged out successfully"
}
```

## Task Endpoints

### Create a new task

```
POST /tasks
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

**Request Body:**
```json
{
    
    // Required fields
    "title": "Complete project documentation",
    "description": "Create detailed documentation for the API project",
    "due_date": "2025-04-30T23:59:59",
    
    // Optional fields
    "status": "pending",       // Defaults to "pending" if not provided
    "assigned_to": 2           // Task will be unassigned if not provided

}
```

**Response (201 Created):**
```json
{
     "message": "Task created successfully",
    "task": {
        "id": 1,
        "title": "Complete project documentation",
        "description": "Create detailed documentation for the API project",
        "status": "pending",
        "due_date": "2025-04-30T23:59:59+00:00",
        "completed_at": null,
        "assigned_to": null,
        "assigned_user": null,
        "created_by": 1,
        "created_user": {
            "id": 1,
            "name": "john",
            "email": "john@example.com"
        },
        "created_at": "2025-04-19T06:07:53+00:00",
        "updated_at": "2025-04-19T06:07:53+00:00",
        "_links": {
            "self": {
                "href": "http://task-management-system.test/api/tasks/2"
            },
            "assign": {
                "href": "http://task-management-system.test/api/tasks/2/assign"
            }
        }
    }
}
```

### List all tasks with filters

```
GET /tasks?status=pending&assigned_to=1&search=documentation&sort_by=due_date&sort_direction=asc&per_page=10
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

**Query Parameters:**
- `status`: Filter by status (pending, completed, expired)
- `assigned_to`: Filter by assigned user ID
- `created_by` : Filter by created user ID
- `due_date` : Filter by due date
- `search`: Search in title and description
- `sort_by`: Field to sort by (id, title, status, due_date, created_at, updated_at)
- `sort_direction`: Sort direction (asc, desc)
- `per_page`: Number of items per page

**Response (200 OK):**
```json
{
    "data": [
        {
            "id": 1,
            "title": "Complete project documentation",
            "description": "Create detailed documentation for the API project",
            "status": "pending",
            "due_date": "2025-04-30T23:59:59+00:00",
            "completed_at": null,
            "assigned_to": 2,
            "assigned_user": {
                "id": 2,
                "name": "ram",
                "email": "ram@example.com"
            },
            "created_by": 1,
            "created_user": {
                "id": 1,
                "name": "john",
                "email": "john@example.com"
            },
            "created_at": "2025-04-19T06:07:28+00:00",
            "updated_at": "2025-04-19T06:32:56+00:00",
            "_links": {
                "self": {
                    "href": "http://task-management-system.test/api/tasks/1"
                },
                "assign": {
                    "href": "http://task-management-system.test/api/tasks/1/assign"
                }
            }
        }
    ],
    "links": {
        "first": "http://task-management-system.test/api/tasks?page=1",
        "last": "http://task-management-system.test/api/tasks?page=2",
        "prev": null,
        "next": "http://task-management-system.test/api/tasks?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 2,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "http://task-management-system.test/api/tasks?page=1",
                "label": "1",
                "active": true
            },
            {
                "url": "http://task-management-system.test/api/tasks?page=2",
                "label": "2",
                "active": false
            },
            {
                "url": "http://task-management-system.test/api/tasks?page=2",
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "http://task-management-system.test/api/tasks",
        "per_page": 1,
        "to": 1,
        "total": 2
    }
}
```

### Get a specific task

```
GET /tasks/{id}
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

**Response (200 OK):**
```json
{
    "task": {
        "id": 1,
        "title": "Complete project documentation",
        "description": "Create detailed documentation for the API project",
        "status": "pending",
        "due_date": "2025-04-30T23:59:59+00:00",
        "completed_at": null,
        "assigned_to": 2,
        "assigned_user": {
            "id": 2,
            "name": "ram",
            "email": "ram@example.com"
        },
        "created_by": 1,
        "created_user": {
            "id": 1,
            "name": "john",
            "email": "john@example.com"
        },
        "created_at": "2025-04-19T06:07:28+00:00",
        "updated_at": "2025-04-19T06:32:56+00:00",
        "_links": {
            "self": {
                "href": "http://task-management-system.test/api/tasks/1"
            },
            "assign": {
                "href": "http://task-management-system.test/api/tasks/1/assign"
            }
        }
    }
}
```

### Assign a task to a user

```
PUT /tasks/{id}/assign
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

**Response (200 OK):**
```json
{
     "message": "Task assigned successfully",
    "task": {
        "id": 1,
        "title": "Complete project documentation",
        "description": "Create detailed documentation for the API project",
        "status": "pending",
        "due_date": "2025-04-30T23:59:59+00:00",
        "completed_at": null,
        "assigned_to": 2,
        "assigned_user": {
            "id": 2,
            "name": "ram",
            "email": "ram@example.com"
        },
        "created_by": 1,
        "created_user": {
            "id": 1,
            "name": "john",
            "email": "john@example.com"
        },
        "created_at": "2025-04-19T06:07:28+00:00",
        "updated_at": "2025-04-19T06:32:56+00:00",
        "_links": {
            "self": {
                "href": "http://task-management-system.test/api/tasks/1"
            },
            "assign": {
                "href": "http://task-management-system.test/api/tasks/1/assign"
            }
        }
    }
}
```

### Mark a task as completed

```
PUT /tasks/{id}/complete
```

**Headers:**
```
Authorization: Bearer 1|abcdefghijklmnopqrstuvwxyz123456789
```

**Response (200 OK):**
```json
{
     "message": "Task completed successfully",
    "task": {
        "id": 1,
        "title": "Complete project documentation",
        "description": "Create detailed documentation for the API project",
        "status": "completed",
        "due_date": "2025-04-30T23:59:59+00:00",
        "completed_at": "2025-05-01T23:59:59+00:00",
        "assigned_to": null,
        "assigned_user": null,
        "created_by": 1,
        "created_user": {
            "id": 1,
            "name": "john",
            "email": "john@example.com"
        },
        "created_at": "2025-04-19T07:12:05+00:00",
        "updated_at": "2025-04-19T07:12:10+00:00",
        "_links": {
            "self": {
                "href": "http://task-management-system.test/api/tasks/3"
            },
            "assign": {
                "href": "http://task-management-system.test/api/tasks/3/assign"
            }
        }
    }
}
```

## Error Handling

The API uses standard HTTP status codes to indicate the success or failure of requests. Here are common error responses:

### Authentication Errors

**401 Unauthorized**
```json
{
    "message": "Unauthenticated."
}
```

**403 Forbidden**
```json
{
    "message": "You do not have permission to perform this action."
}
```

### Validation Errors (422 Unprocessable Entity)

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "title": [
            "The title field is required."
        ],
        "due_date": [
            "The due date must be a future date."
        ]
    }
}
```

### Resource Not Found (404 Not Found)

```json
{
    "message": "Resource not found."
}
```

### Server Error (500 Internal Server Error)

```json
{
    "message": "An unexpected error occurred. Please try again later."
}
```
