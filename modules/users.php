<?php

declare(strict_types=1);

function user_controller_register(): void
{
    if (auth_user()) {
        redirect(url_with_action('index.php', 'dashboard'));
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            $errors[] = 'Invalid CSRF token.';
        } else {
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            if (!validate_email($email)) {
                $errors[] = 'Invalid email.';
            }
            if (!validate_password($password)) {
                $errors[] = 'Password must be at least 8 characters.';
            }

            if (!$errors) {
                $exists = db_fetch_one('SELECT id FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
                if ($exists) {
                    $errors[] = 'Email already registered.';
                } else {
                    db_exec(
                        "INSERT INTO users (email, password_hash, balance, status, is_admin) VALUES (:email, :hash, 0, 'active', 0)",
                        ['email' => $email, 'hash' => password_hash_mis($password)]
                    );
                    $uid = (int)db_last_insert_id();
                    auth_login($uid);
                    flash_set('success', 'Welcome!');
                    redirect(url_with_action('index.php', 'dashboard'));
                }
            }
        }
    }

    render('user/register.php', ['errors' => $errors], 'user/layout.php');
}

function user_controller_login(string $entry, string $successAction, bool $adminOnly = false): void
{
    if (auth_user()) {
        redirect(url_with_action($entry, $successAction));
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            $errors[] = 'Invalid CSRF token.';
        } else {
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');

            $user = db_fetch_one('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
            if (!$user || !password_verify_mis($password, (string)$user['password_hash'])) {
                $errors[] = 'Invalid credentials.';
            } elseif (($user['status'] ?? 'active') !== 'active') {
                $errors[] = 'Account disabled.';
            } elseif ($adminOnly && (int)($user['is_admin'] ?? 0) !== 1) {
                $errors[] = 'Admin only.';
            } else {
                auth_login((int)$user['id']);
                flash_set('success', 'Logged in.');
                redirect(url_with_action($entry, $successAction));
            }
        }
    }

    $template = $adminOnly ? 'admin/login.php' : 'user/login.php';
    $layout = $adminOnly ? 'admin/layout.php' : 'user/layout.php';
    render($template, ['errors' => $errors], $layout);
}

function user_controller_dashboard(): void
{
    $user = require_login();

    $serviceCount = db_fetch_one('SELECT COUNT(*) AS c FROM services WHERE user_id = :uid', ['uid' => (int)$user['id']]);
    $orderCount = db_fetch_one('SELECT COUNT(*) AS c FROM orders WHERE user_id = :uid', ['uid' => (int)$user['id']]);

    render('user/dashboard.php', [
        'user' => $user,
        'serviceCount' => (int)($serviceCount['c'] ?? 0),
        'orderCount' => (int)($orderCount['c'] ?? 0),
    ], 'user/layout.php');
}

function admin_users_controller_list(): void
{
    require_admin();
    $users = db_fetch_all('SELECT id, email, balance, status, is_admin, created_at FROM users ORDER BY id DESC LIMIT 200');
    render('admin/users_list.php', ['users' => $users], 'admin/layout.php');
}

function admin_users_controller_adjust_balance(): void
{
    require_admin();

    $id = (int)($_GET['id'] ?? 0);
    $user = db_fetch_one('SELECT id, email, balance FROM users WHERE id = :id', ['id' => $id]);
    if (!$user) {
        flash_set('error', 'User not found.');
        redirect(url_with_action('admin.php', 'users'));
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            $errors[] = 'Invalid CSRF token.';
        } else {
            $delta = (float)($_POST['delta'] ?? 0);
            $desc = trim((string)($_POST['desc'] ?? 'Admin adjustment'));

            if ($delta == 0.0) {
                $errors[] = 'Delta must not be 0.';
            }

            if (!$errors) {
                db()->beginTransaction();
                try {
                    db_exec('UPDATE users SET balance = balance + :d WHERE id = :id', ['d' => $delta, 'id' => $id]);
                    db_exec(
                        "INSERT INTO transactions (user_id, change_amount, type, ref_type, ref_id, `desc`)\n                         VALUES (:uid, :amt, 'adjust', 'admin', NULL, :d)",
                        ['uid' => $id, 'amt' => $delta, 'd' => $desc]
                    );
                    db()->commit();
                    flash_set('success', 'Balance updated.');
                    redirect(url_with_action('admin.php', 'users'));
                } catch (Throwable $e) {
                    db()->rollBack();
                    throw $e;
                }
            }
        }
    }

    render('admin/user_balance.php', ['targetUser' => $user, 'errors' => $errors], 'admin/layout.php');
}
