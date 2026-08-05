# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project state

This is a fresh Laravel 12 application (PHP ^8.2), currently at the default skeleton stage — no custom routes, models, controllers, or migrations have been added beyond what `laravel new` generates. Treat `app/Models/User.php`, `routes/web.php`, and the existing migrations as the framework baseline, not as established patterns to preserve; they will be replaced as real features are built.

Stack:
- Backend: Laravel 12, SQLite (`database/database.sqlite`)
- Frontend build: Vite + Tailwind CSS 4 (via `@tailwindcss/vite`), Axios
- Testing: PHPUnit 11

## Commands

Install dependencies:
```bash
composer install
npm install
```

Run the full dev environment (server, queue listener, log tailing, Vite) concurrently:
```bash
composer dev
```

Run pieces individually:
```bash
php artisan serve              # app server
php artisan queue:listen --tries=1 --timeout=0
php artisan pail --timeout=0   # log viewer
npm run dev                    # Vite dev server
```

Build frontend assets for production:
```bash
npm run build
```

Run tests:
```bash
composer test                  # clears config cache, then runs php artisan test
php artisan test                                   # run all tests
php artisan test --filter=TestName                 # run a single test by name
php artisan test tests/Feature/ExampleTest.php      # run a single test file
```

Lint / format PHP (Laravel Pint):
```bash
vendor/bin/pint
```

Database migrations:
```bash
php artisan migrate
php artisan migrate:fresh --seed
```

## Architecture

Standard Laravel 12 structure — no custom architectural layers yet:
- `app/Http/Controllers` — controllers (only the base `Controller` class exists so far)
- `app/Models` — Eloquent models (only `User` exists so far)
- `app/Providers/AppServiceProvider.php` — single service provider; register bindings/bootstrapping here unless a feature warrants a dedicated provider
- `routes/web.php` — web routes (currently just the default `/` welcome route)
- `routes/console.php` — Artisan console routes/commands
- `database/migrations`, `database/factories`, `database/seeders` — schema and test data
- `resources/js/app.js`, `resources/css/app.css` — Vite entry points
- Tests live under `tests/Unit` and `tests/Feature`; both suites run against an in-memory SQLite DB (`phpunit.xml` sets `DB_DATABASE=:memory:`)

As the app grows, prefer Laravel's conventional locations (Form Requests in `app/Http/Requests`, policies in `app/Policies`, jobs in `app/Jobs`, etc.) over introducing custom structure.
