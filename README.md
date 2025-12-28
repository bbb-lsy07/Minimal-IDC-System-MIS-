# Minimal IDC System (MIS)

A lightweight IDC management system MVP written in plain PHP.

## Quick Start (Auto Install)

For Ubuntu 20.04/22.04 LTS, run this one-click install script:

```bash
wget https://raw.githubusercontent.com/bbb-lsy07/Minimal-IDC-System-MIS-/main/install.sh && sudo bash install.sh
```

The script will:
- Install Nginx, PHP 8.x, MariaDB
- Setup the database and default admin user (`admin@example.com` / `admin123`)
- Configure cron jobs for billing
- Secure the installation

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

## Database Configuration

**Important:** The default configuration uses `root` with no password, which often fails in modern MySQL/MariaDB installations.

**Solutions:**

1. **Recommended:** Use the web installer at `web_install.php` to set up your database properly
2. Create a `config.local.php` file with your database credentials:

```php
<?php
return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=mis;charset=utf8mb4',
        'user' => 'your_db_user',  // e.g., 'mis_setup' if using install.sh
        'pass' => 'your_db_password',  // e.g., 'MisTemp123!' if using install.sh
    ],
    'app_key' => 'your-secret-key-here',
];
```

3. Use environment variables:

```bash
export MIS_DB_USER='your_db_user'
export MIS_DB_PASS='your_db_password'
export MIS_DB_DSN='mysql:host=127.0.0.1;dbname=mis;charset=utf8mb4'
export MIS_APP_KEY='your-secret-key-here'
```

## Troubleshooting

**Error: "Access denied for user 'root'@'localhost'"**

This occurs when MySQL/MariaDB doesn't allow root access without authentication. Solutions:

1. Use the web installer (`web_install.php`) - it creates proper database credentials
2. Create a `config.local.php` file with valid database credentials
3. If you used `install.sh`, use user `mis_setup` with password `MisTemp123!`

## Entry points

- User: `index.php`
- Admin: `admin.php`
- Monitoring push endpoint: `api/push_monitor.php`
- Cron jobs (CLI): `php cron.php`
