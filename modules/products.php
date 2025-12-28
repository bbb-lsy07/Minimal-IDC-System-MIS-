<?php

declare(strict_types=1);

function products_controller_list_user(): void
{
    $products = db_fetch_all("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC");
    render('user/products.php', ['products' => $products], 'user/layout.php');
}

function admin_products_controller_list(): void
{
    require_admin();
    $products = db_fetch_all('SELECT * FROM products ORDER BY id DESC');
    render('admin/products_list.php', ['products' => $products], 'admin/layout.php');
}

function admin_products_controller_edit(): void
{
    require_admin();

    $id = (int)($_GET['id'] ?? 0);
    $product = null;
    if ($id > 0) {
        $product = db_fetch_one('SELECT * FROM products WHERE id = :id', ['id' => $id]);
        if (!$product) {
            flash_set('error', 'Product not found.');
            redirect(url_with_action('admin.php', 'products'));
        }
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            $errors[] = 'Invalid CSRF token.';
        } else {
            $name = trim((string)($_POST['name'] ?? ''));
            $deliveryMode = (string)($_POST['delivery_mode'] ?? 'manual');
            $billingMode = (string)($_POST['billing_mode'] ?? 'periodic');
            $status = (string)($_POST['status'] ?? 'active');
            $priceJsonRaw = trim((string)($_POST['price_json'] ?? ''));

            if ($name === '') {
                $errors[] = 'Name is required.';
            }

            $priceJson = json_decode($priceJsonRaw, true);
            if (!is_array($priceJson)) {
                $errors[] = 'price_json must be valid JSON.';
            }

            $allowedDelivery = ['manual', 'provider_api'];
            $allowedBilling = ['periodic', 'metered'];
            $allowedStatus = ['active', 'hidden', 'disabled'];

            if (!in_array($deliveryMode, $allowedDelivery, true)) {
                $errors[] = 'Invalid delivery_mode.';
            }
            if (!in_array($billingMode, $allowedBilling, true)) {
                $errors[] = 'Invalid billing_mode.';
            }
            if (!in_array($status, $allowedStatus, true)) {
                $errors[] = 'Invalid status.';
            }

            if (!$errors) {
                if ($id > 0) {
                    db_exec(
                        'UPDATE products SET name=:name, delivery_mode=:dm, billing_mode=:bm, price_json=:pj, status=:st WHERE id=:id',
                        [
                            'name' => $name,
                            'dm' => $deliveryMode,
                            'bm' => $billingMode,
                            'pj' => json_encode($priceJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                            'st' => $status,
                            'id' => $id,
                        ]
                    );
                    flash_set('success', 'Product updated.');
                } else {
                    db_exec(
                        'INSERT INTO products (name, delivery_mode, billing_mode, price_json, status) VALUES (:name, :dm, :bm, :pj, :st)',
                        [
                            'name' => $name,
                            'dm' => $deliveryMode,
                            'bm' => $billingMode,
                            'pj' => json_encode($priceJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                            'st' => $status,
                        ]
                    );
                    flash_set('success', 'Product created.');
                }

                redirect(url_with_action('admin.php', 'products'));
            }
        }
    }

    render('admin/product_edit.php', [
        'product' => $product,
        'errors' => $errors,
    ], 'admin/layout.php');
}
