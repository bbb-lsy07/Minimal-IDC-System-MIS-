<?php

declare(strict_types=1);

define('MIS_ROOT', dirname(__DIR__));

// Critical extension checks - fail early with clear messages
if (!extension_loaded('curl')) {
    die('Critical Error: PHP CURL extension is required.');
}
if (!extension_loaded('pdo_mysql')) {
    die('Critical Error: PHP PDO_MYSQL extension is required.');
}

$GLOBALS['MIS_CONFIG'] = require MIS_ROOT . '/config.php';

date_default_timezone_set((string)($GLOBALS['MIS_CONFIG']['timezone'] ?? 'UTC'));

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once MIS_ROOT . '/includes/app.php';
require_once MIS_ROOT . '/includes/security.php';
require_once MIS_ROOT . '/includes/db.php';
require_once MIS_ROOT . '/includes/auth.php';
require_once MIS_ROOT . '/includes/http.php';
require_once MIS_ROOT . '/includes/validators.php';
require_once MIS_ROOT . '/includes/view.php';

require_once MIS_ROOT . '/modules/users.php';
require_once MIS_ROOT . '/modules/products.php';
require_once MIS_ROOT . '/modules/orders.php';
require_once MIS_ROOT . '/modules/services.php';
require_once MIS_ROOT . '/modules/billing.php';
require_once MIS_ROOT . '/modules/remote_ctl.php';
require_once MIS_ROOT . '/modules/monitor.php';
