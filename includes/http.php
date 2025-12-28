<?php

declare(strict_types=1);

function http_request(string $method, string $url, array $options = []): array
{
    $ch = curl_init();
    if ($ch === false) {
        throw new RuntimeException('Unable to init curl');
    }

    $headers = $options['headers'] ?? [];
    $timeout = (int)($options['timeout'] ?? 15);
    $body = $options['body'] ?? null;

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    }

    $respBody = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    return [
        'status' => $code,
        'body' => $respBody === false ? '' : $respBody,
        'error' => $err,
    ];
}
