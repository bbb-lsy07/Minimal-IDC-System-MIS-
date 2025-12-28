<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/includes/bootstrap.php';

$action = (string)($_GET['action'] ?? 'home');

switch ($action) {
    case 'login':
        user_controller_login('admin.php', 'dashboard', true);
        break;
    case 'logout':
        auth_logout();
        flash_set('success', 'Logged out.');
        redirect(url_with_action('admin.php', 'login'));
        break;
    case 'dashboard':
        require_admin();
        render('admin/dashboard.php', [], 'admin/layout.php');
        break;

    case 'products':
        admin_products_controller_list();
        break;
    case 'product_edit':
        admin_products_controller_edit();
        break;

    case 'orders':
        admin_orders_controller_list();
        break;

    case 'services':
        admin_services_controller_list();
        break;
    case 'service_deliver':
        admin_services_controller_deliver();
        break;

    case 'users':
        admin_users_controller_list();
        break;
    case 'user_balance':
        admin_users_controller_adjust_balance();
        break;

    case 'home':
    default:
        if (auth_user()) {
            redirect(url_with_action('admin.php', 'dashboard'));
        }
        redirect(url_with_action('admin.php', 'login'));
}
