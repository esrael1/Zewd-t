# ZEWD Backend API

## Requirements
- PHP 8.2+
- Composer
- SQLite (default)

## Setup
1. Copy `.env.example` to `.env`.
2. Generate app key: `php artisan key:generate`.
3. Run migrations: `php artisan migrate`.
4. Link storage: `php artisan storage:link`.
5. Start server: `php artisan serve`.

## API Routes
All routes are prefixed with `/api`. Authentication is handled via Laravel Sanctum.
- `/register`
- `/login`
- `/admin/*` (Protected by role)
- `/teacher/*` (Protected by role & subscription)
- `/student/*` (Protected by role)
