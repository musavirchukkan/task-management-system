# Postman Collection Guide

This guide explains how to use the Postman collection for the Task Management System API.

**Navigation:**
- [Back to Main README](../README.md)
- [Installation & Setup Guide](installation.md)
- [API Documentation](api.md)
- [Architecture Documentation](architecture.md)

## Collection Contents

The Task Management System Postman collection includes the following folders:

- **Authentication**
  - Register
  - Login
  - Logout

- **Tasks**
  - Create Task
  - List Tasks (with filters)
  - Get Task Details
  - Assign Task
  - Complete Task

## Using the Collection

1. Download and install [Postman](https://www.postman.com/downloads/)

2. Import the collection:
   - Click "Import" in the top left corner
   - Navigate to `docs/postman/task-management-system.postman_collection.json`
   - Click "Import"

3. Import the environment:
   - Click "Import" in the top left corner
   - Navigate to `docs/postman/task-management-system ( Development ).postman_environment.json`
   - Click "Import"

4. Select the environment:
   - Click the environment dropdown in the top right corner
   - Select "task-management-system ( Development )"

5. Set your base URL (if different):
   - Click the eye icon next to the environment dropdown
   - Edit the "base_url" variable if needed (default: http://localhost:8000/api)
   - Close the environment variables window

6. Use the collection:
   - Start with the "Register" or "Login" requests in the Authentication folder
   - Login will automatically store your authentication token in the environment
   - Use other endpoints as needed

## Environment Variables

The collection uses the following environment variables:

| Variable | Description |
|----------|-------------|
| base_url | Base URL for all API endpoints (e.g., http://localhost:8000/api) |
| token    | Authentication token (automatically set after login) |

## Automated Authentication

The collection is configured to automatically handle authentication:

1. The Login request includes a script that extracts the token from the response and stores it
2. All other requests use the stored token in their Authorization header
3. No need to manually copy and paste tokens between requests

## Troubleshooting

- **Authentication errors**: Make sure you've successfully logged in first
- **404 Not Found**: Check that your base_url is set correctly
- **Connection errors**: Ensure the API server is running
- **Validation errors**: Check the request body against the API documentation
