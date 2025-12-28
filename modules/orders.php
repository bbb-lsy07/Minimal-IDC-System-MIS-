<?php

declare(strict_types=1);

function orders_controller_list_user(): void
{
    $user = require_login();
    $orders = db_fetch_all(
        'SELECT o.*, p.name AS product_name\n         FROM orders o\n         JOIN products p ON p.id = o.product_id\n         WHERE o.user_id = :uid\n         ORDER BY o.id DESC LIMIT 200',
        ['uid' => (int)$user['id']]
    );

    render('user/orders.php', ['orders' => $orders], 'user/layout.php');
}

function orders_controller_buy(): void
{
    $user = require_login();

    $productId = (int)($_GET['product_id'] ?? ($_POST['product_id'] ?? 0));
    $product = db_fetch_one("SELECT * FROM products WHERE id = :id AND status = 'active'", ['id' => $productId]);
    if (!$product) {
        flash_set('error', 'Product not found.');
        redirect(url_with_action('index.php', 'products'));
    }

    $priceJson = json_decode((string)$product['price_json'], true);
    if (!is_array($priceJson)) {
        $priceJson = [];
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            $errors[] = 'Invalid CSRF token.';
        } else {
            $billingMode = (string)$product['billing_mode'];
            $cycle = trim((string)($_POST['cycle'] ?? ''));

            $amount = 0.0;
            if ($billingMode === 'periodic') {
                if (!isset($priceJson[$cycle])) {
                    $errors[] = 'Invalid cycle.';
                } else {
                    $amount = (float)$priceJson[$cycle];
                }
            } elseif ($billingMode === 'metered') {
                $cycle = 'metered';
                $amount = 0.0;
            } else {
                $errors[] = 'Invalid billing mode.';
            }

            if (!$errors) {
                db()->beginTransaction();
                try {
                    $u = db_fetch_one('SELECT balance FROM users WHERE id = :id FOR UPDATE', ['id' => (int)$user['id']]);
                    $balance = $u ? (float)$u['balance'] : 0.0;

                    if ($amount > 0 && $balance < $amount) {
                        db()->rollBack();
                        $errors[] = 'Insufficient balance.';
                    } else {
                        if ($amount > 0) {
                            db_exec('UPDATE users SET balance = balance - :amt WHERE id = :id', ['amt' => $amount, 'id' => (int)$user['id']]);
                            db_exec(
                                "INSERT INTO transactions (user_id, change_amount, type, ref_type, ref_id, `desc`)\n                                 VALUES (:uid, :amt, 'consume', 'order', NULL, :d)",
                                ['uid' => (int)$user['id'], 'amt' => -$amount, 'd' => 'Purchase: ' . (string)$product['name']]
                            );
                        }

                        db_exec(
                            "INSERT INTO orders (user_id, product_id, amount, status, billing_cycle, paid_at)\n                             VALUES (:uid, :pid, :amt, 'paid', :cycle, NOW())",
                            [
                                'uid' => (int)$user['id'],
                                'pid' => (int)$product['id'],
                                'amt' => $amount,
                                'cycle' => $cycle,
                            ]
                        );
                        $orderId = (int)db_last_insert_id();

                        $expireAt = null;
                        $unitPrice = null;
                        $meterStartedAt = null;
                        $lastBilledAt = null;

                        if ($product['billing_mode'] === 'periodic') {
                            $now = new DateTimeImmutable('now');
                            $expireAt = billing_add_interval($now, $cycle)->format('Y-m-d H:i:s');
                        } else {
                            $meterStartedAt = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
                            $lastBilledAt = $meterStartedAt;
                            $unitPrice = isset($priceJson['per_second']) ? (float)$priceJson['per_second'] : 0.0;
                        }

                        db_exec(
                            "INSERT INTO services\n                             (user_id, product_id, order_id, status, delivery_mode, expire_at, meter_started_at, last_billed_at, unit_price_per_second)\n                             VALUES\n                             (:uid, :pid, :oid, 'pending', :dm, :expire_at, :ms, :lb, :ups)",
                            [
                                'uid' => (int)$user['id'],
                                'pid' => (int)$product['id'],
                                'oid' => $orderId,
                                'dm' => (string)$product['delivery_mode'],
                                'expire_at' => $expireAt,
                                'ms' => $meterStartedAt,
                                'lb' => $lastBilledAt,
                                'ups' => $unitPrice,
                            ]
                        );
                        $serviceId = (int)db_last_insert_id();

                        monitor_get_or_create_token($serviceId);

                        db_exec("UPDATE orders SET status = 'provisioning' WHERE id = :id", ['id' => $orderId]);

                        db()->commit();
                        flash_set('success', 'Order created. Waiting for delivery/activation.');
                        redirect(url_with_action('index.php', 'services'));
                    }
                } catch (Throwable $e) {
                    db()->rollBack();
                    throw $e;
                }
            }
        }
    }

    render('user/buy.php', [
        'product' => $product,
        'priceJson' => $priceJson,
        'errors' => $errors,
    ], 'user/layout.php');
}

function admin_orders_controller_list(): void
{
    require_admin();
    $orders = db_fetch_all(
        'SELECT o.*, u.email AS user_email, p.name AS product_name\n         FROM orders o\n         JOIN users u ON u.id = o.user_id\n         JOIN products p ON p.id = o.product_id\n         ORDER BY o.id DESC LIMIT 300'
    );
    render('admin/orders_list.php', ['orders' => $orders], 'admin/layout.php');
}
