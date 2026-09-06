# TickTrack — Ticketing System API

A support ticketing system built with Laravel 12 as a REST API, designed to be connected to a Vue.js frontend.

## Features

- Full authentication system (Register / Login / Logout / Profile) using Laravel Sanctum
- Complete CRUD for tickets with filtering by search, status, and priority
- Ticket replies system
- Role-based permissions (User / Admin)
- Monthly statistics dashboard

## Tech Stack

- **Backend:** Laravel 12
- **Authentication:** Laravel Sanctum (API Token)
- **Database:** MySQL

## Getting Started

```bash
# Clone the repository
git clone https://github.com/Mohamed-Eltantawy/ticktrack-app.git
cd ticktrack-app

# Install dependencies
composer install

# Set up environment file
cp .env.example .env
php artisan key:generate

# Configure your database credentials in .env, then run migrations
php artisan migrate

# Install Sanctum (if not already installed)
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Start the server
php artisan serve
```

The project will run at: `http://127.0.0.1:8000`

## API Endpoints

### Authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|--------------|----------------|
| POST | `/api/register` | Register a new account | No |
| POST | `/api/login` | Log in | No |
| GET | `/api/me` | Get current user profile | Yes |
| POST | `/api/logout` | Log out | Yes |

### Tickets

| Method | Endpoint | Description | Auth Required |
|--------|----------|--------------|----------------|
| GET | `/api/ticket` | List all tickets (filterable by search, status, priority) | Yes |
| GET | `/api/ticket/{code}` | Show a single ticket in detail | Yes |
| POST | `/api/ticket` | Create a new ticket | Yes |
| POST | `/api/ticket/{code}/reply` | Add a reply to a ticket | Yes |

### Dashboard

| Method | Endpoint | Description | Auth Required |
|--------|----------|--------------|----------------|
| GET | `/api/dashboard/statistics` | Monthly ticket statistics | Yes |

## Example: Login Request

```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"test@example.com","password":"12345678"}'
```

The response includes a `token` which should be used in subsequent requests as:
```
Authorization: Bearer {token}
```

## Database Structure

Core tables: `users`, `tickets`, `ticket_replies`, `personal_access_tokens`.

- `users` has a `hasMany` relationship with `tickets` and `ticket_replies`
- `tickets` has a `hasMany` relationship with `ticket_replies`
