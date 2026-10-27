# Wevent

Wevent is an event management platform built with Laravel. It helps organizers
create and manage events, while giving attendees tools to discover events,
request or confirm participation, communicate, and receive updates.

## Features

- User registration, authentication, profiles, and role-based access
- Public and private event management
- Invitations, join requests, capacity, and participation management
- Event comments, replies, polls, and voting
- Real-time notifications through Pusher
- Full-text event search with category and tag filtering
- Reporting and moderation tools
- Event photo and profile image uploads

## Tech stack

- PHP 8.3+
- Laravel 12
- PostgreSQL 16
- Vite and JavaScript
- Docker and Docker Compose

## Local setup

### Requirements

- PHP 8.3 or later with the PostgreSQL extension
- Composer 2.2 or later
- Node.js and npm
- Docker Desktop (optional, for the PostgreSQL and pgAdmin services)

### Installation

1. Install the PHP and JavaScript dependencies:

    ```bash
    composer install
    npm install
    ```

2. Create a local environment file:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    On Windows PowerShell, use `Copy-Item .env.example .env`.

3. Start PostgreSQL and pgAdmin, if using Docker:

    ```bash
    docker compose up -d
    ```

4. Create the database schema and seed data using the SQL script in
   `database/event-management.sql`, or configure the database connection and
   run:

    ```bash
    php artisan db:seed
    ```

5. Start the development servers:

    ```bash
    npm run dev
    php artisan serve
    ```

The application is then available at `http://localhost:8000`.

## Testing

Run the automated test suite with:

```bash
php artisan test
```

## Production configuration

Never commit a real `.env` file or credentials. Configure database, mail, and
third-party service values through environment variables in the deployment
environment (for example with Docker's `--env-file` option). The included
Docker Compose configuration is intended for local development.

## Credits

Wevent is an original collaborative project. The source code and media credits
are retained in the repository.
