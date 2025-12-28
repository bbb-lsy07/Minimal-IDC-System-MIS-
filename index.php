<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/includes/bootstrap.php';

$action = (string)($_GET['action'] ?? 'home');

switch ($action) {
    case 'login':
        user_controller_login('index.php', 'dashboard', false);
        break;
    case 'register':
        user_controller_register();
        break;
    case 'logout':
        auth_logout();
        flash_set('success', 'Logged out.');
        redirect(url_with_action('index.php', 'login'));
        break;
    case 'dashboard':
        user_controller_dashboard();
        break;
    case 'products':
        products_controller_list_user();
        break;
    case 'buy':
        orders_controller_buy();
        break;
    case 'orders':
        orders_controller_list_user();
        break;
    case 'services':
        services_controller_list_user();
        break;
    case 'service':
        services_controller_detail_user();
        break;
    case 'service_reboot':
        services_controller_reboot_user();
        break;
    case 'home':
    default:
        if (auth_user()) {
            redirect(url_with_action('index.php', 'dashboard'));
        }
        redirect(url_with_action('index.php', 'login'));
}
