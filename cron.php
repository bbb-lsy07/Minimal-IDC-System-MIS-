<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "CLI only\n";
    exit;
}

require_once __DIR__ . '/includes/bootstrap.php';

$task = (string)($argv[1] ?? 'all');

try {
    switch ($task) {
        case 'expire':
            $n = billing_expire_services();
            echo "Expired services suspended: {$n}\n";
            break;
        case 'metered':
            $n = billing_charge_metered_services();
            echo "Metered services charged: {$n}\n";
            break;
        case 'all':
        default:
            $n1 = billing_expire_services();
            $n2 = billing_charge_metered_services();
            echo "Expired services suspended: {$n1}\n";
            echo "Metered services charged: {$n2}\n";
            break;
    }
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
}
