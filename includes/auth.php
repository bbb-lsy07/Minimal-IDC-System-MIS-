<?php

declare(strict_types=1);

function auth_user(): ?array
{
    $id = $_SESSION['user_id'] ?? null;
    if (!is_int($id) && !ctype_digit((string)$id)) {
        return null;
    }

    $user = db_fetch_one('SELECT * FROM users WHERE id = :id LIMIT 1', ['id' => (int)$id]);
    if (!$user || ($user['status'] ?? 'active') !== 'active') {
        return null;
    }

    return $user;
}

function auth_login(int $userId): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
}

function auth_logout(): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function require_login(): array
{
    $user = auth_user();
    if (!$user) {
        flash_set('error', 'Please login first.');
        redirect(url_with_action('index.php', 'login'));
    }

    return $user;
}

function require_admin(): array
{
    $user = require_login();
    if ((int)($user['is_admin'] ?? 0) !== 1) {
        flash_set('error', 'Admin access required.');
        redirect(url_with_action('index.php', 'dashboard'));
    }

    return $user;
}
