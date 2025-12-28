<?php

declare(strict_types=1);

$config = [
    'db' => [
        'dsn' => getenv('MIS_DB_DSN') ?: 'mysql:host=127.0.0.1;dbname=mis;charset=utf8mb4',
        'user' => getenv('MIS_DB_USER') ?: 'root',
        'pass' => getenv('MIS_DB_PASS') ?: '',
    ],
    'app_key' => getenv('MIS_APP_KEY') ?: 'change-me',
    'base_url' => rtrim((string)(getenv('MIS_BASE_URL') ?: ''), '/'),
    'timezone' => getenv('MIS_TIMEZONE') ?: 'UTC',
];

$localPath = __DIR__ . '/config.local.php';
if (is_file($localPath)) {
    $local = require $localPath;
    if (is_array($local)) {
        $config = array_replace_recursive($config, $local);
    }
}

return $config;
