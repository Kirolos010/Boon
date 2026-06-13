# Boon - POS and Inventory Management System

Boon is a Laravel 12 web application for daily business operations including:

- Product and stock management
- Sales (invoices and quick sales)
- Purchases and supplier tracking
- Expense management
- Financial and inventory reports
- PDF and Excel report export
- Scheduled database backups to Google Drive

## Tech Stack

- Backend: Laravel 12, PHP 8.2+
- Database: MySQL or PostgreSQL
- Frontend build: Vite, Tailwind CSS 4
- Report export: maatwebsite/excel, barryvdh/laravel-dompdf
- Google Drive integration: google/apiclient

## Main Modules

- Dashboard and KPIs
- Products with stock adjustment and low-stock checks
- Clients and client invoice history
- Invoices with payment recording
- Quick sales
- Purchases with receive flow
- Expenses and expense categories
- Reports:
	- Sales report
	- Profit report
	- Inventory report
	- Daily closing report
- Settings:
	- Main categories and subcategories
	- Suppliers
	- Users

## Prerequisites

Before setup, make sure the following are installed:

- PHP 8.2+
- Composer 2+
- Node.js 20+ and npm
- MySQL 8+ or PostgreSQL 13+
- Git

For scheduled SQL backups:

- `mysqldump` (for MySQL/MariaDB) or `pg_dump` (for PostgreSQL)
- Google Cloud OAuth credentials

## Installation

1. Clone repository

```bash
git clone <your-repo-url> boon
cd boon
```

2. Install backend and frontend dependencies

```bash
composer install
npm install
```

3. Create environment file

```bash
cp .env.example .env
```

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

4. Generate application key

```bash
php artisan key:generate
```

5. Configure database in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=boon
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations and seeders

```bash
php artisan migrate
php artisan db:seed
```

7. Build frontend assets

```bash
npm run build
```

8. Start development services

Option A (single command, recommended):

```bash
composer run dev
```

Option B (manual):

```bash
php artisan serve
npm run dev
```

## One-command Setup

You can use the built-in setup script:

```bash
composer run setup
```

Then run seeders manually:

```bash
php artisan db:seed
```

## Default Seeded Users

After `php artisan db:seed`, these users are created:

- `admin@boon.local` / `password`
- `sales@boon.local` / `password`
- `accountant@boon.local` / `password`

Important: Change these passwords immediately in non-local environments.

## License Host Protection (Important)

The app includes global machine license middleware. If host name does not match the expected value, requests are blocked with `403 License Error`.

Set these in `.env`:

```env
LICENSE_ALLOWED_HOST=YOUR_MACHINE_HOSTNAME
LICENSE_ALERT_EMAIL=alerts@example.com
```

To get host name:

- Windows: `hostname`
- Linux/macOS: `hostname`

## Google Drive Database Backup Setup

The app supports SQL backup upload to Google Drive via OAuth refresh token.

### 1) Add Google credentials to `.env`

```env
GOOGLE_DRIVE_CLIENT_ID=
GOOGLE_DRIVE_CLIENT_SECRET=
GOOGLE_DRIVE_REFRESH_TOKEN=
GOOGLE_DRIVE_FOLDER_ID=

# Optional explicit dump binary paths:
MYSQLDUMP_PATH=
PG_DUMP_PATH=
```

### 2) Generate refresh token (one-time)

```bash
php artisan backup:google-oauth-setup
```

### 3) Validate configuration

```bash
php artisan backup:diagnose-drive
```

### 4) Run backup manually

```bash
php artisan backup:database-to-google-drive
```

### 5) Scheduled backup

The app schedules backup daily at 02:00 through Laravel scheduler.

Set your server cron to run every minute:

```bash
* * * * * php /path-to-project/artisan schedule:run >> /dev/null 2>&1
```

On Windows Task Scheduler, run `php artisan schedule:run` every minute.

## Useful Commands

```bash
# Run tests
composer run test

# Clear config/cache/routes/views
php artisan optimize:clear

# Rebuild frontend for production
npm run build

# Run queue worker manually (if needed)
php artisan queue:listen --tries=1 --timeout=0
```

## Project Structure (high level)

```text
app/
	Console/Commands/        # Custom artisan commands (backup, diagnostics)
	Http/Controllers/        # Feature controllers
	Http/Middleware/         # Role and machine license middleware
	Models/                  # Eloquent models
	Services/                # Business/domain services
database/
	migrations/              # Database schema changes
	seeders/                 # Initial/sample data
routes/
	web.php                  # Web routes
	console.php              # Artisan commands and scheduler definitions
resources/
	views/                   # Blade templates
	css/, js/                # Frontend assets
```

## Troubleshooting

- `403 License Error`
	- Verify `LICENSE_ALLOWED_HOST` matches current machine hostname exactly.

- Backup fails with dump tool not found
	- Install database client tools or set `MYSQLDUMP_PATH` / `PG_DUMP_PATH` in `.env`.

- Google backup auth errors
	- Re-run `php artisan backup:google-oauth-setup`.
	- Confirm `GOOGLE_DRIVE_FOLDER_ID` is valid and accessible.

- Frontend assets not loading
	- Run `npm install` then `npm run dev` (development) or `npm run build` (production).

## Deployment Notes

- Set `APP_ENV=production` and `APP_DEBUG=false`.
- Use secure database credentials.
- Replace seeded default user passwords.
- Configure queue and scheduler in production process manager.
- Ensure writable permissions for `storage/` and `bootstrap/cache/`.

## License

This project is licensed under the MIT License unless your organization policy states otherwise.
