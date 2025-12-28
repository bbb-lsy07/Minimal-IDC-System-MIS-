#!/bin/bash
set -u

# ==========================================
# MIS 环境一键部署脚本 (中文版)
# 支持: Ubuntu 20.04/22.04, Debian 10/11
# ==========================================

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

INSTALL_DIR="/var/www/mis"
GITHUB_REPO="https://github.com/bbb-lsy07/Minimal-IDC-System-MIS-.git"

log_info() { echo -e "${GREEN}[信息] $1${NC}"; }
log_warn() { echo -e "${YELLOW}[警告] $1${NC}"; }
log_error() { echo -e "${RED}[错误] $1${NC}"; }

# 1. 检查 Root 权限
if [ "$EUID" -ne 0 ]; then
    log_error "请使用 root 权限运行此脚本: sudo bash install.sh"
    exit 1
fi

echo -e "${BLUE}=================================================${NC}"
echo -e "${BLUE}       MIS 系统环境部署向导 (中文版)      ${NC}"
echo -e "${BLUE}=================================================${NC}"
echo ""
echo "请输入您的域名 (例如: panel.example.com)"
echo "如果是本地测试或仅IP访问，请直接回车"
read -p "域名: " DOMAIN_INPUT

if [ -z "$DOMAIN_INPUT" ]; then
    SERVER_NAME="_"
    USE_SSL=false
    PUBLIC_IP=$(curl -s4 ifconfig.me || hostname -I | awk '{print $1}')
    BASE_URL="http://${PUBLIC_IP}"
    log_warn "未输入域名，将使用 IP 模式: ${BASE_URL}"
else
    SERVER_NAME="$DOMAIN_INPUT"
    USE_SSL=true
    BASE_URL="https://${SERVER_NAME}"
    log_info "使用域名: ${SERVER_NAME} (稍后将尝试申请 SSL 证书)"
fi

# 2. 系统初始化
log_info "步骤 1: 初始化系统环境..."
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y software-properties-common curl git unzip wget cron ufw lsb-release ca-certificates apt-transport-https

# 3. 安装 Nginx 和 MariaDB
log_info "正在安装 Nginx 和数据库..."
apt-get install -y nginx mariadb-server mariadb-client

# 4. 安装 PHP (PHP 8.2，满足 match 表达式等语法要求)
log_info "步骤 2: 安装 PHP 环境 (PHP 8.2)..."

# 添加 PHP 源
add-apt-repository ppa:ondrej/php -y
apt-get update -y

# 安装 PHP 8.2 及扩展
apt-get install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-curl \
    php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath \
    php8.2-ssh2 libssh2-1-dev

# 如果找不到 php8.2-ssh2，尝试通用包名
if ! dpkg -l | grep -q php8.2-ssh2; then
    apt-get install -y php-ssh2
fi

# 启动 PHP
systemctl start php8.2-fpm
systemctl enable php8.2-fpm

PHP_SOCK="/run/php/php8.2-fpm.sock"

# 5. 代码部署
log_info "步骤 3: 下载代码..."
if [ -d "$INSTALL_DIR" ]; then
    log_warn "目录已存在，正在备份..."
    mv "$INSTALL_DIR" "${INSTALL_DIR}_backup_$(date +%s)"
fi

git clone "$GITHUB_REPO" "$INSTALL_DIR" || { log_error "代码下载失败"; exit 1; }

# 设置权限 (确保 Nginx 可读取静态资源)
chown -R www-data:www-data "$INSTALL_DIR"
find "$INSTALL_DIR" -type d -exec chmod 755 {} \;
find "$INSTALL_DIR" -type f -exec chmod 644 {} \;

# 6. 配置 Nginx
log_info "步骤 4: 配置 Web 服务器..."
rm -f /etc/nginx/sites-enabled/default

cat > /etc/nginx/sites-available/mis <<EOF
server {
    listen 80;
    server_name ${SERVER_NAME};
    root ${INSTALL_DIR};
    index index.php index.html web_install.php;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:${PHP_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # 禁止访问敏感文件
    location ~ ^/(\.git|sql|includes|templates|storage|\.env|config\.local\.php) {
        deny all;
        return 404;
    }
}
EOF

ln -sf /etc/nginx/sites-available/mis /etc/nginx/sites-enabled/

# 7. 配置防火墙
echo "y" | ufw enable 2>/dev/null
ufw allow 80
ufw allow 443
ufw allow 22

# 8. 申请 SSL
if [ "$USE_SSL" = true ]; then
    log_info "步骤 5: 申请 SSL 证书..."
    apt-get install -y certbot python3-certbot-nginx
    certbot --nginx --non-interactive --agree-tos -m "admin@example.com" -d "$SERVER_NAME" || log_warn "SSL 申请失败，请确认域名解析是否正确。后续可手动运行 certbot --nginx 重试。"
fi

# 9. 重启服务
systemctl restart nginx

log_info "步骤 6: 数据库初始化..."
# 确保数据库启动
systemctl start mariadb

# 创建一个空的数据库用户供 Web 安装器使用
# 注意：生产环境建议手动设置更复杂的密码
DB_TEMP_PASS="MisTemp123!"
mysql -e "CREATE DATABASE IF NOT EXISTS mis;"
mysql -e "CREATE USER IF NOT EXISTS 'mis_setup'@'localhost' IDENTIFIED BY '${DB_TEMP_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON *.* TO 'mis_setup'@'localhost' WITH GRANT OPTION;"
mysql -e "FLUSH PRIVILEGES;"

echo -e "${GREEN}==============================================${NC}"
echo -e "${GREEN}   环境部署完成！                     ${NC}"
echo -e "${GREEN}==============================================${NC}"
echo -e "请在浏览器访问以下地址完成最后的安装："
echo -e "安装地址:   ${BLUE}${BASE_URL}/web_install.php${NC}"
echo -e ""
echo -e "数据库主机: 127.0.0.1"
echo -e "数据库用户: mis_setup"
echo -e "数据库密码: ${DB_TEMP_PASS}"
echo -e "数据库名:   mis"
echo -e ""
echo -e "${YELLOW}注意：安装完成后，建议删除数据库用户 mis_setup 并删除 web_install.php 文件${NC}"