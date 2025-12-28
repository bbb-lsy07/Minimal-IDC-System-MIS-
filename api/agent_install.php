<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: text/plain; charset=utf-8');

$token = (string)($_GET['token'] ?? '');
if ($token === '') {
    http_response_code(400);
    echo "token required\n";
    exit;
}

$serviceId = monitor_validate_token($token);
if (!$serviceId) {
    http_response_code(403);
    echo "invalid token\n";
    exit;
}

$server = base_url();

$script = <<<'BASH'
#!/usr/bin/env bash
set -euo pipefail

# Ensure PATH is available in minimal environments
export PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

echo "Starting Netdata installation for MIS Cloud monitoring..."

# Check if Netdata is already installed
if command -v netdata >/dev/null 2>&1; then
    echo "Netdata is already installed."
    systemctl is-active --quiet netdata && echo "Netdata service is running."
    exit 0
fi

# Check for required commands
for cmd in curl bash; do
    if ! command -v "$cmd" >/dev/null 2>&1; then
        echo "ERROR: Required command '$cmd' not found. Please install it first." >&2
        exit 1
    fi
done

# Determine OS package manager
if [ -f /etc/debian_version ]; then
    PKG_UPDATE="apt-get update -qq"
    PKG_INSTALL="apt-get install -y --no-install-recommends"
elif [ -f /etc/redhat-release ]; then
    PKG_UPDATE="yum makecache"
    PKG_INSTALL="yum install -y"
else
    echo "WARNING: Unknown Linux distribution. Attempting universal install..."
fi

if [ -n "${PKG_UPDATE:-}" ]; then
    echo "Updating package repository..."
    $PKG_UPDATE
fi

# Install Netdata using kickstart (official method)
echo "Downloading and installing Netdata..."
if ! curl -fsSL "https://get.netdata.cloud/kickstart.sh" | bash -s -- --stable-channel --disable-telemetry; then
    echo "ERROR: Failed to install Netdata. Please check network connectivity and permissions." >&2
    exit 1
fi

# Wait for Netdata to start
sleep 3
echo "Netdata installation completed."

# Open firewall port 19999 if firewall is active
if systemctl is-active --quiet firewalld 2>/dev/null; then
    echo "Configuring firewalld for Netdata..."
    firewall-cmd --add-port=19999/tcp --permanent >/dev/null 2>&1 || true
    firewall-cmd --reload >/dev/null 2>&1 || true
elif command -v ufw >/dev/null 2>&1 && ufw status | grep -q "Status: active"; then
    echo "Configuring UFW for Netdata..."
    ufw allow 19999/tcp >/dev/null 2>&1 || true
fi

# Show status
if systemctl is-active --quiet netdata; then
    echo "✅ Netdata is installed and running on http://$(hostname -I | awk '{print $1}'):19999"
    echo "📊 You can now access detailed real-time monitoring charts."
else
    echo "⚠️  Netdata installed but service not running. Check: systemctl status netdata"
fi

exit 0
BASH;

$script = str_replace(['__SERVER__', '__TOKEN__'], [$server, $token], $script);
echo $script;