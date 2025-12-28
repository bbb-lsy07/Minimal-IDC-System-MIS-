# Minimal IDC System (MIS)

A lightweight IDC management system MVP written in plain PHP.

## Requirements

- PHP 8.1+ (session enabled)
- MySQL 8+ (recommended)
- Optional: `ext-ssh2` for remote control features

## Setup

1. Create a database and import schema:

```bash
mysql -u root -p mis < sql/schema.sql
```

2. Configure environment variables (recommended) or create `config.local.php`.

Environment variables:

- `MIS_DB_DSN` (e.g. `mysql:host=127.0.0.1;dbname=mis;charset=utf8mb4`)
- `MIS_DB_USER`
- `MIS_DB_PASS`
- `MIS_APP_KEY` (32+ chars; used for encryption)

3. Point your web server document root to this project directory (where `index.php` is).

## Entry points

- User: `index.php`
- Admin: `admin.php`
- Monitoring push endpoint: `api/push_monitor.php`
- Cron jobs (CLI): `php cron.php`
