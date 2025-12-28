<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

$token = (string)($_POST['token'] ?? $_GET['token'] ?? '');
if ($token === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'token required']);
    exit;
}

$serviceId = monitor_validate_token($token);
if (!$serviceId) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'invalid token']);
    exit;
}

$data = [
    'cpu' => (float)($_POST['cpu'] ?? 0),
    'mem' => (float)($_POST['mem'] ?? 0),
    'disk' => (float)($_POST['disk'] ?? 0),
    'net_up' => (int)($_POST['net_up'] ?? 0),
    'net_down' => (int)($_POST['net_down'] ?? 0),
    'load1' => isset($_POST['load1']) ? (float)$_POST['load1'] : null,
];

monitor_insert_log($serviceId, $data);
monitor_touch_token($token);

echo json_encode(['ok' => true]);
