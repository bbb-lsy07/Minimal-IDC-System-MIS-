<?php

declare(strict_types=1);

function services_controller_list_user(): void
{
    $user = require_login();
    $services = db_fetch_all(
        'SELECT s.*, p.name AS product_name
         FROM services s
         JOIN products p ON p.id = s.product_id
         WHERE s.user_id = :uid
         ORDER BY s.id DESC LIMIT 200',
        ['uid' => (int)$user['id']]
    );

    render('user/services.php', ['services' => $services], 'user/layout.php');
}

function services_controller_detail_user(): void
{
    $user = require_login();
    $id = (int)($_GET['id'] ?? 0);

    $service = db_fetch_one(
        'SELECT s.*, p.name AS product_name, p.billing_mode
         FROM services s
         JOIN products p ON p.id = s.product_id
         WHERE s.id = :id AND s.user_id = :uid
         LIMIT 1',
        ['id' => $id, 'uid' => (int)$user['id']]
    );

    if (!$service) {
        flash_set('error', '服务未找到。');
        redirect(url_with_action('index.php', 'services'));
    }

    $token = monitor_get_or_create_token((int)$service['id']);
    $logs = monitor_list_logs((int)$service['id'], 200);

    render('user/service_detail.php', [
        'service' => $service,
        'token' => $token,
        'logs' => $logs,
    ], 'user/layout.php');
}

function services_controller_reboot_user(): void
{
    $user = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(url_with_action('index.php', 'services'));
    }

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        flash_set('error', 'Invalid CSRF token.');
        redirect(url_with_action('index.php', 'services'));
    }

    $id = (int)($_POST['id'] ?? 0);
    $service = db_fetch_one('SELECT * FROM services WHERE id = :id AND user_id = :uid LIMIT 1', ['id' => $id, 'uid' => (int)$user['id']]);
    if (!$service) {
        flash_set('error', '服务未找到。');
        redirect(url_with_action('index.php', 'services'));
    }

    if (($service['ip'] ?? '') === '' || ($service['username'] ?? '') === '' || ($service['password_enc'] ?? '') === '') {
        flash_set('error', '服务连接信息未设置。');
        redirect(url_with_action('index.php', 'service', ['id' => $id]));
    }

    try {
        $pass = decrypt_secret((string)$service['password_enc']);
    } catch (Throwable $e) {
        flash_set('error', '密码解密失败。');
        redirect(url_with_action('index.php', 'service', ['id' => $id]));
    }

    $res = remote_reboot((string)$service['ip'], (int)$service['port'], (string)$service['username'], $pass);
    if ($res['ok']) {
        flash_set('success', '重启命令已发送。');
    } else {
        flash_set('error', '重启失败: ' . (string)$res['error']);
    }

    redirect(url_with_action('index.php', 'service', ['id' => $id]));
}

function services_controller_install_monitor_user(): void
{
    $user = require_login();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(url_with_action('index.php', 'services'));
    }

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        flash_set('error', 'Invalid CSRF token.');
        redirect(url_with_action('index.php', 'services'));
    }

    $id = (int)($_POST['id'] ?? 0);
    $service = db_fetch_one('SELECT * FROM services WHERE id = :id AND user_id = :uid LIMIT 1', ['id' => $id, 'uid' => (int)$user['id']]);
    if (!$service) {
        flash_set('error', 'Service not found.');
        redirect(url_with_action('index.php', 'services'));
    }

    if (($service['ip'] ?? '') === '' || ($service['username'] ?? '') === '' || ($service['password_enc'] ?? '') === '') {
        flash_set('error', 'Service connection info not set.');
        redirect(url_with_action('index.php', 'service', ['id' => $id]));
    }

    $token = monitor_get_or_create_token((int)$service['id']);
    $url = base_url() . '/api/agent_install.php?token=' . rawurlencode($token);

    try {
        $pass = decrypt_secret((string)$service['password_enc']);
    } catch (Throwable $e) {
        flash_set('error', 'Unable to decrypt password.');
        redirect(url_with_action('index.php', 'service', ['id' => $id]));
    }

    $cmd = 'curl -fsSL ' . escapeshellarg($url) . ' | bash 2>&1';
    $res = remote_exec_password((string)$service['ip'], (int)$service['port'], (string)$service['username'], $pass, $cmd);

    if ($res['ok']) {
        $out = trim((string)($res['output'] ?? ''));
        $out = $out === '' ? '' : (' Output: ' . substr($out, 0, 300));
        flash_set('success', 'Monitor agent install executed.' . $out);
    } else {
        flash_set('error', 'Install failed: ' . (string)$res['error']);
    }

    redirect(url_with_action('index.php', 'service', ['id' => $id]));
}

function admin_services_controller_install_monitor(): void
{
    require_admin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(url_with_action('admin.php', 'services'));
    }

    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        flash_set('error', '无效的CSRF令牌。');
        redirect(url_with_action('admin.php', 'services'));
    }

    $id = (int)($_POST['id'] ?? 0);
    $service = db_fetch_one('SELECT * FROM services WHERE id = :id LIMIT 1', ['id' => $id]);
    if (!$service) {
        flash_set('error', 'Service not found.');
        redirect(url_with_action('admin.php', 'services'));
    }

    if (($service['ip'] ?? '') === '' || ($service['username'] ?? '') === '' || ($service['password_enc'] ?? '') === '') {
        flash_set('error', 'Service connection info not set.');
        redirect(url_with_action('admin.php', 'service_deliver', ['id' => $id]));
    }

    $token = monitor_get_or_create_token((int)$service['id']);
    $url = base_url() . '/api/agent_install.php?token=' . rawurlencode($token);

    try {
        $pass = decrypt_secret((string)$service['password_enc']);
    } catch (Throwable $e) {
        flash_set('error', 'Unable to decrypt password.');
        redirect(url_with_action('admin.php', 'service_deliver', ['id' => $id]));
    }

    $cmd = 'curl -fsSL ' . escapeshellarg($url) . ' | bash 2>&1';
    $res = remote_exec_password((string)$service['ip'], (int)$service['port'], (string)$service['username'], $pass, $cmd);

    if ($res['ok']) {
        $out = trim((string)($res['output'] ?? ''));
        $out = $out === '' ? '' : (' Output: ' . substr($out, 0, 300));
        flash_set('success', 'Monitor agent install executed.' . $out);
    } else {
        flash_set('error', 'Install failed: ' . (string)$res['error']);
    }

    redirect(url_with_action('admin.php', 'service_deliver', ['id' => $id]));
}

function admin_services_controller_list(): void
{
    require_admin();
    $services = db_fetch_all(
        'SELECT s.*, u.email AS user_email, p.name AS product_name
         FROM services s
         JOIN users u ON u.id = s.user_id
         JOIN products p ON p.id = s.product_id
         ORDER BY s.id DESC LIMIT 300'
    );
    render('admin/services_list.php', ['services' => $services], 'admin/layout.php');
}

function admin_services_controller_deliver(): void
{
    require_admin();

    $id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
    $service = db_fetch_one(
        'SELECT s.*, u.email AS user_email, p.name AS product_name
         FROM services s
         JOIN users u ON u.id = s.user_id
         JOIN products p ON p.id = s.product_id
         WHERE s.id = :id
         LIMIT 1',
        ['id' => $id]
    );

    if (!$service) {
        flash_set('error', '服务未找到。');
        redirect(url_with_action('admin.php', 'services'));
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            $errors[] = '无效的CSRF令牌。';
        } else {
            $ip = trim((string)($_POST['ip'] ?? ''));
            $port = (int)($_POST['port'] ?? 22);
            $username = trim((string)($_POST['username'] ?? 'root'));
            $password = (string)($_POST['password'] ?? '');

            if ($ip === '') {
                $errors[] = 'IP is required.';
            }
            if ($port <= 0 || $port > 65535) {
                $errors[] = 'Invalid port.';
            }
            if ($username === '') {
                $errors[] = 'Username is required.';
            }

            if (!$errors) {
                $enc = $service['password_enc'];
                if ($password !== '') {
                    $enc = encrypt_secret($password);
                }

                db()->beginTransaction();
                try {
                    db_exec(
                        "UPDATE services\n                         SET ip=:ip, port=:port, username=:u, password_enc=:p, status='active'\n                         WHERE id=:id",
                        ['ip' => $ip, 'port' => $port, 'u' => $username, 'p' => $enc, 'id' => $id]
                    );
                    if (!empty($service['order_id'])) {
                        db_exec("UPDATE orders SET status = 'active' WHERE id = :oid", ['oid' => (int)$service['order_id']]);
                    }
                    db()->commit();
                    flash_set('success', '服务已交付/激活。');
                    redirect(url_with_action('admin.php', 'services'));
                    } catch (Throwable $e) {
                    db()->rollBack();
                    throw $e;
                }
            }
        }
    }

    $token = monitor_get_or_create_token((int)$service['id']);

    render('admin/service_deliver.php', ['service' => $service, 'errors' => $errors, 'token' => $token], 'admin/layout.php');
}
