<?php

declare(strict_types=1);

function remote_can_ssh2(): bool
{
    return extension_loaded('ssh2')
        && function_exists('ssh2_connect')
        && function_exists('ssh2_auth_password')
        && function_exists('ssh2_exec')
        && function_exists('ssh2_fetch_stream');
}

function remote_exec_password(string $host, int $port, string $username, string $password, string $command): array
{
    if (!remote_can_ssh2()) {
        return ['ok' => false, 'output' => '', 'error' => 'Server Configuration Error: php-ssh2 extension not installed. Please install it: apt install php-ssh2'];
    }

    $conn = @ssh2_connect($host, $port);
    if (!$conn) {
        return ['ok' => false, 'output' => '', 'error' => 'SSH connect failed'];
    }

    if (!@ssh2_auth_password($conn, $username, $password)) {
        return ['ok' => false, 'output' => '', 'error' => 'SSH auth failed'];
    }

    $stream = @ssh2_exec($conn, $command);
    if (!$stream) {
        return ['ok' => false, 'output' => '', 'error' => 'SSH exec failed'];
    }

    $errStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);

    stream_set_blocking($stream, true);
    if (is_resource($errStream)) {
        stream_set_blocking($errStream, true);
    }

    $output = stream_get_contents($stream);
    $err = is_resource($errStream) ? stream_get_contents($errStream) : '';

    fclose($stream);
    if (is_resource($errStream)) {
        fclose($errStream);
    }

    $combined = trim((string)$output . "\n" . (string)$err);

    return ['ok' => true, 'output' => $combined, 'error' => ''];
}

function remote_reboot(string $host, int $port, string $username, string $password): array
{
    return remote_exec_password($host, $port, $username, $password, 'reboot');
}
