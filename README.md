# Bridge – Suggestions API (Laravel)

A small backend for BR.I.D.G.E to collect suggestions with categories, departments, and file attachments (anonymous-friendly).

## Requirements
- PHP 8.2+
- Composer
- MySQL/MariaDB (or other DB supported by Laravel)

## Setup
```bash
cp .env.example .env
# fill DB_* and APP_URL in .env
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve

