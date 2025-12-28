#!/bin/bash
set -u

# ==========================================
# MIS Enhanced Auto-Deployment Script (Production Ready)
# Supported: Ubuntu 20.04/22.04/24.04, Debian 10/11/12
# ==========================================

# Color definitions
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Default configuration
INSTALL_DIR="/var/www/mis"
GITHUB_REPO="https://github.com/bbb-lsy07/Minimal-IDC-System-MIS-.git"
DB_NAME="mis"
DB_USER="mis_user"
DB_PASS=$(openssl rand -base64 16 | tr -dc 'a-zA-Z0-9')
APP_KEY=$(openssl rand -hex 32)
ADMIN_EMAIL="admin@example.com"
ADMIN_PASS="admin123"

# Logging functions
log_info() { echo -e "${GREEN}[INFO] $1${NC}"; }
log_warn() { echo -e "${YELLOW}[WARN] $1${NC}"; }
log_error() { echo -e "${RED}[ERROR] $1${NC}"; }

# 1. Check root privileges
if [ "$EUID" -ne 0 ]; then
    log_error "Please run with root privileges: sudo bash install.sh"
    exit 1
fi

# 2. Get domain name (for Nginx and SSL)
echo -e "${BLUE}=================================================${NC}"
echo -e "${BLUE}       Minimal IDC System (MIS) Installation      ${NC}"
echo -e "${BLUE}=================================================${NC}"
echo ""
echo "Enter your domain name (e.g., panel.example.com)"
echo "For local testing or IP-only access, press Enter directly"
read -p "Domain: " DOMAIN_INPUT

if [ -z "$DOMAIN_INPUT" ]; then
    SERVER_NAME="_"
    USE_SSL=false
    PUBLIC_IP=$(curl -s4 ifconfig.me || hostname -I | awk '{print $1}')
    BASE_URL="http://${PUBLIC_IP}"
    log_warn "No domain entered, using IP mode: ${BASE_URL}"
else
    SERVER_NAME="$DOMAIN_INPUT"
    USE_SSL=true
    BASE_URL="https://${SERVER_NAME}"
    log_info "Using domain: ${SERVER_NAME} (will attempt SSL certificate)"
fi

# 3. System initialization and dependency installation
log_info "Step 1: Initializing system environment..."

# Prevent apt interactive prompts from hanging
export DEBIAN_FRONTEND=noninteractive
# Fix possible apt locks
rm -f /var/lib/apt/lists/lock /var/cache/apt/archives/lock /var/lib/dpkg/lock* 2>/dev/null || true

apt-get update -y
apt-get install -y software-properties-common curl git unzip wget cron ufw lsb-release ca-certificates apt-transport-https

# Enable universe repository (for Ubuntu, ensures php-ssh2 availability)
if grep -q "Ubuntu" /etc/issue 2>/dev/null || grep -q "Ubuntu" /etc/os-release 2>/dev/null; then
    add-apt-repository universe -y
fi

# Install Nginx and MariaDB
log_info "Installing Nginx & MariaDB..."
apt-get install -y nginx mariadb-server mariadb-client

# 4. Install PHP and extensions
log_info "Step 2: Installing PHP environment..."

# Attempt to install PHP 8.1, 8.2 or 8.3
PHP_VER=""
for v in "8.2" "8.1" "8.3"; do
    if apt-cache show php$v-fpm >/dev/null 2>&1; then
        PHP_VER="$v"
        break
    fi
done

if [ -z "$PHP_VER" ]; then
    log_warn "No suitable PHP version found, adding ppa:ondrej/php..."
    add-apt-repository ppa:ondrej/php -y
    apt-get update -y
    PHP_VER="8.2"
fi

log_info "Detected PHP Version: $PHP_VER"

# Install PHP and core extensions (key: ssh2, curl, mysql)
apt-get install -y php${PHP_VER}-fpm php${PHP_VER}-cli php${PHP_VER}-mysql php${PHP_VER}-curl \
    php${PHP_VER}-mbstring php${PHP_VER}-xml php${PHP_VER}-zip php${PHP_VER}-bcmath \
    php${PHP_VER}-ssh2 libssh2-1-dev

# Verify ssh2 extension loaded (common issue in fresh environments)
if ! php -m | grep -q 'ssh2'; then
    log_warn "PHP-SSH2 extension not detected, attempting manual installation..."
    apt-get install -y php-ssh2 || log_error "Failed to install php-ssh2. Remote management features may be limited."
fi

# 5. Configure database
log_info "Step 3: Initializing database..."
systemctl start mariadb 2>/dev/null || service mariadb start
systemctl enable mariadb 2>/dev/null || true

# Idempotent operations - safe to run multiple times
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# 6. Deploy code
log_info "Step 4: Deploying code to $INSTALL_DIR ..."

if [ -d "$INSTALL_DIR" ]; then
    log_warn "Directory exists, backing up..."
    mv "$INSTALL_DIR" "${INSTALL_DIR}_backup_$(date +%s)"
fi

git clone "$GITHUB_REPO" "$INSTALL_DIR" || { log_error "Git clone failed"; exit 1; }

# Import SQL schema
mysql "${DB_NAME}" < "$INSTALL_DIR/sql/schema.sql"

# Create initial administrator
ADMIN_HASH=$(php -r "echo password_hash('${ADMIN_PASS}', PASSWORD_DEFAULT);")
mysql "${DB_NAME}" -e "INSERT IGNORE INTO users (id, email, password_hash, balance, status, is_admin) VALUES (1, '${ADMIN_EMAIL}', '${ADMIN_HASH}', 9999, 'active', 1);"

# Generate configuration file
cat > "$INSTALL_DIR/config.local.php" <<EOF
<?php
return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=${DB_NAME};charset=utf8mb4',
        'user' => '${DB_USER}',
        'pass' => '${DB_PASS}',
    ],
    'app_key' => '${APP_KEY}',
    'base_url' => '${BASE_URL}',
    'timezone' => 'Asia/Shanghai',
];
EOF

log_info "Configuration file generated: $INSTALL_DIR/config.local.php"

# 7. Configure Nginx
log_info "Step 5: Configuring web server..."

# Find PHP socket
PHP_SOCK=$(find /run/php -name "php*-fpm.sock" 2>/dev/null | head -n 1)
[ -z "$PHP_SOCK" ] && PHP_SOCK="/run/php/php${PHP_VER}-fpm.sock"

# Remove default config
rm -f /etc/nginx/sites-enabled/default

# Write Nginx config
cat > /etc/nginx/sites-available/mis <<EOF
server {
    listen 80;
    server_name ${SERVER_NAME};
    root ${INSTALL_DIR};
    index index.php index.html;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    access_log /var/log/nginx/mis_access.log;
    error_log /var/log/nginx/mis_error.log;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:${PHP_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive directories
    location ~ ^/(\.git|sql|includes|templates|storage|\.env|config\.local\.php) {
        deny all;
        return 404;
    }

    location ~ \.(sql|log|txt)$ {
        deny all;
        return 404;
    }
}
EOF

ln -sf /etc/nginx/sites-available/mis /etc/nginx/sites-enabled/

# Firewall configuration
log_info "Configuring firewall (UFW)..."
ufw allow OpenSSH 2>/dev/null || true
ufw allow 'Nginx Full' 2>/dev/null || true
# Enable UFW if not running (non-interactive)
echo "y" | ufw enable 2>/dev/null || true

# 8. SSL certificate issuance (Certbot)
if [ "$USE_SSL" = true ]; then
    log_info "Step 6: Applying for SSL certificate..."
    apt-get install -y certbot python3-certbot-nginx
    certbot --nginx --non-interactive --agree-tos -m "$ADMIN_EMAIL" -d "$SERVER_NAME" || log_warn "SSL certificate application failed. Ensure domain $SERVER_NAME resolves to this server IP. You can run 'certbot --nginx' manually later."
fi

# 9. Permissions and Cron
log_info "Step 7: Final configuration..."
chown -R www-data:www-data "$INSTALL_DIR" 2>/dev/null || chown -R nginx:nginx "$INSTALL_DIR" 2>/dev/null || true
chmod -R 755 "$INSTALL_DIR"
chmod 640 "$INSTALL_DIR/config.local.php" 2>/dev/null || true

# Add cron task (remove old, add new)
CRON_CMD="cd $INSTALL_DIR && /usr/bin/php cron.php all >> /var/log/mis_cron.log 2>&1"
(crontab -l 2>/dev/null | grep -v "mis_cron.log"; echo "$CRON_CMD") | crontab -

# Restart services
systemctl restart nginx 2>/dev/null || service nginx restart 2>/dev/null
systemctl restart php${PHP_VER}-fpm 2>/dev/null || true

echo -e "${GREEN}==============================================${NC}"
echo -e "${GREEN}   Installation Complete!                     ${NC}"
echo -e "${GREEN}==============================================${NC}"
echo -e "Access URL:    ${BLUE}${BASE_URL}${NC}"
echo -e "Admin Panel:   ${BLUE}${BASE_URL}/admin.php${NC}"
echo -e ""
echo -e "Admin Email:   ${YELLOW}${ADMIN_EMAIL}${NC}"
echo -e "Admin Password:${YELLOW}${ADMIN_PASS}${NC}"
echo -e ""
echo -e "Database Name: ${YELLOW}${DB_NAME}${NC}"
echo -e "DB User:       ${YELLOW}${DB_USER}${NC}"
echo -e "DB Password:   ${YELLOW}${DB_PASS}${NC}"
echo -e ""
echo -e "${YELLOW}Please login and change the default password!${NC}"
echo -e "${GREEN}==============================================${NC}"
