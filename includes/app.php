<?php

declare(strict_types=1);

function mis_config(string $key, mixed $default = null): mixed
{
    $cfg = $GLOBALS['MIS_CONFIG'] ?? [];

    $parts = explode('.', $key);
    $cur = $cfg;
    foreach ($parts as $p) {
        if (!is_array($cur) || !array_key_exists($p, $cur)) {
            return $default;
        }
        $cur = $cur[$p];
    }

    return $cur;
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $msg = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return is_array($msg) ? $msg : null;
}

function url_with_action(string $entry, string $action, array $params = []): string
{
    $params = array_merge(['action' => $action], $params);
    return $entry . '?' . http_build_query($params);
}

function base_url(): string
{
    $configured = (string)mis_config('base_url', '');
    if ($configured !== '') {
        return $configured;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $scheme = $https ? 'https' : 'http';
    $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');

    return $scheme . '://' . $host;
}
