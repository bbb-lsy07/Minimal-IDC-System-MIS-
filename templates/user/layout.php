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
  <title>MIS</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="nav">
    <div class="container">
      <a href="<?= e(url_with_action('index.php', 'dashboard')) ?>">MIS</a>
      <?php if ($user): ?>
        <a href="<?= e(url_with_action('index.php', 'products')) ?>">Products</a>
        <a href="<?= e(url_with_action('index.php', 'orders')) ?>">Orders</a>
        <a href="<?= e(url_with_action('index.php', 'services')) ?>">Services</a>
        <a href="<?= e(url_with_action('index.php', 'logout')) ?>">Logout</a>
        <span class="small">(<?= e($user['email']) ?>)</span>
      <?php else: ?>
        <a href="<?= e(url_with_action('index.php', 'login')) ?>">Login</a>
        <a href="<?= e(url_with_action('index.php', 'register')) ?>">Register</a>
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
