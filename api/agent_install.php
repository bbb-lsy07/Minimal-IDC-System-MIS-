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

SERVER_URL="__SERVER__"
TOKEN="__TOKEN__"

if ! command -v curl >/dev/null 2>&1; then
  echo "curl not found" >&2
  exit 1
fi

cat > /usr/local/bin/mis_monitor.sh <<'EOF'
#!/usr/bin/env bash
set -euo pipefail

SERVER_URL="__SERVER__"
TOKEN="__TOKEN__"

cpu="$(LC_ALL=C top -bn1 | awk -F',' 'BEGIN{u=0} /Cpu\(s\)/{for(i=1;i<=NF;i++){if($i~/(^| )id/){gsub(/[^0-9.]/,"",$i); u=100-$i; break}}} END{printf "%.2f",u}')"
mem="$(free | awk '/Mem:/{printf "%.2f", ($3/$2*100)}')"
disk="$(df -P / | awk 'NR==2{gsub(/%/,"",$5); printf "%.2f", $5}')"
load1="$(awk '{print $1}' /proc/loadavg)"

curl -fsS -X POST \
  -d "token=${TOKEN}" \
  -d "cpu=${cpu}" \
  -d "mem=${mem}" \
  -d "disk=${disk}" \
  -d "load1=${load1}" \
  "${SERVER_URL}/api/push_monitor.php" >/dev/null
EOF

chmod +x /usr/local/bin/mis_monitor.sh

( crontab -l 2>/dev/null | grep -v "mis_monitor.sh"; echo "*/2 * * * * /usr/local/bin/mis_monitor.sh >/dev/null 2>&1" ) | crontab -

echo "installed: /usr/local/bin/mis_monitor.sh (every 2 minutes)"
BASH;

$script = str_replace(['__SERVER__', '__TOKEN__'], [$server, $token], $script);
echo $script;
