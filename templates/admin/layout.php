<?php
/** @var string $__template */
/** @var array $__vars */

$user = auth_user();
extract($__vars, EXTR_SKIP);
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MIS Admin</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="nav">
    <div class="container">
      <a href="<?= e(url_with_action('admin.php', 'dashboard')) ?>">Admin</a>
      <?php if ($user && (int)($user['is_admin'] ?? 0) === 1): ?>
        <a href="<?= e(url_with_action('admin.php', 'products')) ?>">Products</a>
        <a href="<?= e(url_with_action('admin.php', 'orders')) ?>">Orders</a>
        <a href="<?= e(url_with_action('admin.php', 'services')) ?>">Services</a>
        <a href="<?= e(url_with_action('admin.php', 'users')) ?>">Users</a>
        <a href="<?= e(url_with_action('admin.php', 'logout')) ?>">Logout</a>
        <span class="small">(<?= e($user['email']) ?>)</span>
      <?php else: ?>
        <a href="<?= e(url_with_action('admin.php', 'login')) ?>">Login</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="container">
    <?php if (!empty($flash)): ?>
      <div class="flash <?= e($flash['type']) ?>">
        <?= e($flash['message']) ?>
      </div>
    <?php endif; ?>

    <?php require template_path($__template); ?>
  </div>
</body>
</html>
