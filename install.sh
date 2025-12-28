#!/bin/bash

# ==========================================
# Minimal IDC System (MIS) 自动部署脚本
# 适用系统: Ubuntu 20.04 / 22.04 LTS
# ==========================================

# 设置颜色
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# 配置参数 (你可以修改这里，或者让脚本随机生成)
DB_NAME="mis"
DB_USER="mis_user"
DB_PASS=$(openssl rand -base64 12)
APP_KEY=$(openssl rand -hex 16)
INSTALL_DIR="/var/www/mis"
GITHUB_REPO="https://github.com/bbb-lsy07/Minimal-IDC-System-MIS-.git"

# 默认管理员账号
ADMIN_EMAIL="admin@example.com"
ADMIN_PASS="admin123"

# 检查是否为 Root 用户
if [ "$EUID" -ne 0 ]; then 
  echo -e "${RED}请使用 sudo 或 root 权限运行此脚本${NC}"
  exit 1
fi

echo -e "${GREEN}>>> 1. 更新系统并安装依赖...${NC}"
apt-get update -y
apt-get install -y git curl unzip nginx mariadb-server
apt-get install -y php-fpm php-mysql php-curl php-json php-mbstring php-xml php-ssh2

# 这里的 PHP 版本可能会根据系统不同而不同，获取实际安装的版本号 (例如 8.1)
PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
echo -e "${GREEN}>>> 检测到 PHP 版本: $PHP_VER${NC}"

echo -e "${GREEN}>>> 2. 配置数据库 (MariaDB)...${NC}"
systemctl start mariadb
systemctl enable mariadb

# 创建数据库和用户
mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo -e "${GREEN}>>> 3. 下载项目代码...${NC}"
# 如果目录存在，先备份
if [ -d "$INSTALL_DIR" ]; then
    echo -e "${YELLOW}目录 $INSTALL_DIR 已存在，正在备份...${NC}"
    mv $INSTALL_DIR "${INSTALL_DIR}_backup_$(date +%s)"
fi

git clone $GITHUB_REPO $INSTALL_DIR

echo -e "${GREEN}>>> 4. 导入数据库结构 & 创建管理员...${NC}"
# 导入 Schema
mysql $DB_NAME < $INSTALL_DIR/sql/schema.sql

# 生成管理员密码 Hash (使用 PHP 生成以确保兼容)
ADMIN_HASH=$(php -r "echo password_hash('${ADMIN_PASS}', PASSWORD_DEFAULT);")

# 插入管理员账号 (直接修改 SQL 避免手动操作)
mysql $DB_NAME -e "INSERT INTO users (email, password_hash, balance, status, is_admin) VALUES ('${ADMIN_EMAIL}', '${ADMIN_HASH}', 9999, 'active', 1);"

echo -e "${GREEN}>>> 5. 生成配置文件 config.local.php...${NC}"
# 获取本机 IP 或域名，这里默认用 IP，也可以手动改成域名
SERVER_IP=$(curl -s ifconfig.me)
BASE_URL="http://${SERVER_IP}"

cat > $INSTALL_DIR/config.local.php <<EOF
<?php
return [
    'db' => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=${DB_NAME};charset=utf8mb4',
        'user' => '${DB_USER}',
        'pass' => '${DB_PASS}',
    ],
    'app_key' => '${APP_KEY}',
    'base_url' => '${BASE_URL}',
];
EOF

echo -e "${GREEN}>>> 6. 配置 Nginx...${NC}"
cat > /etc/nginx/sites-available/mis <<EOF
server {
    listen 80;
    server_name _; # 默认匹配所有域名，生产环境建议修改为具体域名
    root $INSTALL_DIR;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php${PHP_VER}-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
    
    # 禁止访问 git 目录和敏感文件
    location ~ /\.git {
        deny all;
    }
    location ~ ^/(sql|includes|templates|config\.local\.php) {
        deny all;
        return 404;
    }
}
EOF

# 启用站点并移除默认站点
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/mis /etc/nginx/sites-enabled/
nginx -t && systemctl restart nginx

echo -e "${GREEN}>>> 7. 设置权限与定时任务...${NC}"
chown -R www-data:www-data $INSTALL_DIR
chmod -R 755 $INSTALL_DIR

# 添加 Cron 任务 (如果不存在则添加)
CRON_JOB="* * * * * cd $INSTALL_DIR && /usr/bin/php cron.php all >> /dev/null 2>&1"
(crontab -l 2>/dev/null | grep -F "$INSTALL_DIR" ) || (crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

echo -e "${GREEN}==============================================${NC}"
echo -e "${GREEN}   安装完成! Minimal IDC System 已部署   ${NC}"
echo -e "${GREEN}==============================================${NC}"
echo -e "访问地址: ${BASE_URL}"
echo -e "管理员账号: ${ADMIN_EMAIL}"
echo -e "管理员密码: ${ADMIN_PASS}"
echo -e "数据库密码: ${DB_PASS}"
echo -e "配置文件: $INSTALL_DIR/config.local.php"
echo -e "=============================================="
