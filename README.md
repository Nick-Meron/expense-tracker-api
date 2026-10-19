# Expense Tracker API

REST API for a personal expense tracker mobile app. Built with Laravel 12, MySQL and Laravel Sanctum token authentication.

Mobile app repository: [expense-tracker-app](https://github.com/Nick-Meron/expense-tracker-app)

## Features

- Register, log in and log out with token authentication (Laravel Sanctum)
- Create, edit and delete income and expense categories
- Create, list, edit and delete transactions, with an optional month filter
- Monthly summary: total income, total expenses, balance and totals per category
- Every query is scoped to the logged-in user, so users can only see their own data
- Input validation on all create and update endpoints

## Tech stack

- PHP 8.2
- Laravel 12
- MySQL
- Laravel Sanctum (token authentication)

## API endpoints

All endpoints are under `/api`. Endpoints marked "Token" need the header `Authorization: Bearer <token>`. Send `Accept: application/json` with every request.

| Method | Endpoint | Token | Description |
|---|---|---|---|
| POST | `/register` | No | Create an account and get a token |
| POST | `/login` | No | Log in and get a token |
| POST | `/logout` | Yes | Delete the current token |
| GET | `/user` | Yes | Get the logged-in user |
| GET | `/categories` | Yes | List your categories |
| POST | `/categories` | Yes | Create a category (`name`, `type`: income or expense) |
| PUT | `/categories/{id}` | Yes | Update a category |
| DELETE | `/categories/{id}` | Yes | Delete a category |
| GET | `/transactions` | Yes | List your transactions (optional `?month=YYYY-MM`) |
| POST | `/transactions` | Yes | Create a transaction (`category_id`, `amount`, `note`, `date`) |
| PUT | `/transactions/{id}` | Yes | Update a transaction |
| DELETE | `/transactions/{id}` | Yes | Delete a transaction |
| GET | `/summary` | Yes | Monthly totals (optional `?month=YYYY-MM`) |

## Database

Three main tables:

- `users`: id, name, email, password
- `categories`: id, user_id, name, type (income or expense)
- `transactions`: id, user_id, category_id, amount, note, date

Deleting a user or a category also deletes the related rows (cascade).

## Getting started

Requirements: PHP 8.2 or higher, Composer and MySQL.

1. Clone the repository and open the folder:

```
   git clone https://github.com/Nick-Meron/expense-tracker-api.git
   cd expense-tracker-api
```

2. Install the dependencies:

```
   composer install
```

3. Create your environment file:

```
   copy .env.example .env
```

   On macOS or Linux use `cp .env.example .env`.

4. Create an empty MySQL database called `expense_tracker`, then open `.env` and set the database section:

```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=expense_tracker
   DB_USERNAME=root
   DB_PASSWORD=
```

5. Generate the app key and create the tables:

```
   php artisan key:generate
   php artisan migrate
```

6. Start the server:

```
   php artisan serve --host=0.0.0.0 --port=8000
```

The API is now available at `http://127.0.0.1:8000/api`.

## Example request

Register a user (PowerShell):

```
Invoke-RestMethod -Method Post -Uri http://127.0.0.1:8000/api/register -ContentType "application/json" -Headers @{Accept="application/json"} -Body '{"name":"Test User","email":"test@example.com","password":"secret123"}'
```

The response contains the user and a `token`. Use that token in the `Authorization: Bearer <token>` header for the other endpoints.

## Notes

- The `.env` file is not committed. It holds local settings and must stay private.
- Built as a portfolio project to practise REST API design, authentication and relational database modelling.