<?php

declare(strict_types=1);

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || strlen($_SESSION['csrf_token']) < 16) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_verify(?string $token): bool
{
    $expected = $_SESSION['csrf_token'] ?? '';
    if (!is_string($expected) || $expected === '' || !is_string($token) || $token === '') {
        return false;
    }

    return hash_equals($expected, $token);
}

function password_hash_mis(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function password_verify_mis(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

function encrypt_secret(string $plaintext): string
{
    $keyMaterial = (string)mis_config('app_key', 'change-me');
    $key = hash('sha256', $keyMaterial, true);

    $iv = random_bytes(12);
    $tag = '';
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($ciphertext === false) {
        throw new RuntimeException('Encryption failed.');
    }

    return base64_encode($iv . $tag . $ciphertext);
}

function decrypt_secret(string $encoded): string
{
    $raw = base64_decode($encoded, true);
    if ($raw === false || strlen($raw) < 12 + 16 + 1) {
        throw new RuntimeException('Invalid ciphertext.');
    }

    $keyMaterial = (string)mis_config('app_key', 'change-me');
    $key = hash('sha256', $keyMaterial, true);

    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $ciphertext = substr($raw, 28);

    $plaintext = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($plaintext === false) {
        throw new RuntimeException('Decryption failed.');
    }

    return $plaintext;
}
