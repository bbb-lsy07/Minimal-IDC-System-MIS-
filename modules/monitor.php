<?php

declare(strict_types=1);

function monitor_get_or_create_token(int $serviceId): string
{
    $row = db_fetch_one('SELECT token, status FROM monitor_tokens WHERE service_id = :sid LIMIT 1', ['sid' => $serviceId]);
    if ($row && ($row['status'] ?? '') === 'active') {
        return (string)$row['token'];
    }

    $token = bin2hex(random_bytes(32));

    db_exec(
        "INSERT INTO monitor_tokens (service_id, token, status) VALUES (:sid, :token, 'active')\n         ON DUPLICATE KEY UPDATE token = VALUES(token), status = 'active'",
        ['sid' => $serviceId, 'token' => $token]
    );

    return $token;
}

function monitor_validate_token(string $token): ?int
{
    $row = db_fetch_one(
        "SELECT service_id FROM monitor_tokens WHERE token = :token AND status = 'active' LIMIT 1",
        ['token' => $token]
    );

    if (!$row) {
        return null;
    }

    return (int)$row['service_id'];
}

function monitor_touch_token(string $token): void
{
    db_exec('UPDATE monitor_tokens SET last_seen_at = NOW() WHERE token = :token', ['token' => $token]);
}

function monitor_insert_log(int $serviceId, array $data): void
{
    db_exec(
        'INSERT INTO monitor_logs (service_id, cpu, mem, disk, net_up, net_down, load1)\n         VALUES (:sid, :cpu, :mem, :disk, :net_up, :net_down, :load1)',
        [
            'sid' => $serviceId,
            'cpu' => (float)($data['cpu'] ?? 0),
            'mem' => (float)($data['mem'] ?? 0),
            'disk' => (float)($data['disk'] ?? 0),
            'net_up' => (int)($data['net_up'] ?? 0),
            'net_down' => (int)($data['net_down'] ?? 0),
            'load1' => isset($data['load1']) ? (float)$data['load1'] : null,
        ]
    );
}

function monitor_list_logs(int $serviceId, int $limit = 288): array
{
    return db_fetch_all(
        'SELECT * FROM monitor_logs WHERE service_id = :sid ORDER BY created_at DESC LIMIT ' . (int)$limit,
        ['sid' => $serviceId]
    );
}
