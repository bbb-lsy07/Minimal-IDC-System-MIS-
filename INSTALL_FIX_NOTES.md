# Database Connection Fix

## Problem

After running `install.sh`, users encountered the following error when trying to access the application:

```
Fatal error: Uncaught PDOException: Database connection failed. 
Please check your database configuration in config.php or config.local.php. 
Error: SQLSTATE[HY000] [1698] Access denied for user 'root'@'localhost'
```

## Root Cause

1. The `install.sh` script created a database user `mis_setup` with password `MisTemp123!`
2. However, it did NOT create a `config.local.php` file
3. The application's default `config.php` tries to connect as `root` with no password
4. Modern MariaDB installations use Unix socket authentication for root, so password-based connection fails

## Solution

The `install.sh` script has been updated to:

1. **Auto-generate `config.local.php`** during installation with:
   - Database credentials (`mis_setup` / `MisTemp123!`)
   - A random 32-character `APP_KEY`
   - The correct `BASE_URL` (based on domain or IP)

2. **Set proper file permissions** (640, owned by www-data)

3. **Update user documentation** in the completion message

## Changes Made

### 1. install.sh
- Added `DB_TEMP_PASS` variable at the top of the script
- After git clone, the script now creates `config.local.php` with working credentials
- Uses sed to replace placeholders with actual values
- Sets secure file permissions (640) on config.local.php
- Updated completion message to clarify that database is already configured

### 2. README.md
- Updated to document that install.sh auto-generates config.local.php
- Added section explaining the automatic configuration
- Clarified that users can access the app immediately after installation

### 3. .gitignore
- Added `install.sh.*` to ignore wget backup files

## Testing

After these changes:

1. ✅ `install.sh` creates a valid `config.local.php` file
2. ✅ Application can connect to database immediately
3. ✅ No more "Access denied for user 'root'@'localhost'" errors
4. ✅ Users can still run `web_install.php` if needed to set up database schema
5. ✅ Config file has secure permissions and is gitignored

## For Users

If you've already run `install.sh` and are seeing the database error:

**Quick Fix:**
```bash
# Create config.local.php manually
cat > /var/www/mis/config.local.php <<'EOF'
<?php
declare(strict_types=1);
return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=mis;charset=utf8mb4',
        'user' => 'mis_setup',
        'pass' => 'MisTemp123!',
    ],
    'app_key' => 'your-random-32-char-key-here',
    'base_url' => 'http://your-ip-or-domain',
];
EOF

# Set proper permissions
sudo chmod 640 /var/www/mis/config.local.php
sudo chown www-data:www-data /var/www/mis/config.local.php
```

**Or re-run the installation:**
```bash
wget https://raw.githubusercontent.com/bbb-lsy07/Minimal-IDC-System-MIS-/main/install.sh
sudo bash install.sh
```
