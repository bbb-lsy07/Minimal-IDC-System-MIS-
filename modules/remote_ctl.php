<?php

declare(strict_types=1);

function remote_can_ssh2(): bool
{
    return function_exists('ssh2_connect') && function_exists('ssh2_auth_password') && function_exists('ssh2_exec');
}

function remote_exec_password(string $host, int $port, string $username, string $password, string $command): array
{
    if (!remote_can_ssh2()) {
        return ['ok' => false, 'output' => '', 'error' => 'ext-ssh2 not installed'];
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

    stream_set_blocking($stream, true);
    $output = stream_get_contents($stream);
    fclose($stream);

    return ['ok' => true, 'output' => (string)$output, 'error' => ''];
}

function remote_reboot(string $host, int $port, string $username, string $password): array
{
    return remote_exec_password($host, $port, $username, $password, 'reboot');
}
