<?php
/** @var string $__template */
/** @var array $__vars */

$user = auth_user();
extract($__vars, EXTR_SKIP);
?><!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MIS 管理后台</title>
  <!-- 引入 Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- 引入 FontAwesome 图标 (可选) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
      body { background-color: #f8f9fa; font-family: "PingFang SC", "Microsoft YaHei", sans-serif; }
      .card { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); border: none; margin-bottom: 1.5rem; }
      .navbar-brand { font-weight: bold; }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
      <a class="navbar-brand" href="<?= e(url_with_action('admin.php', 'dashboard')) ?>">
        <i class="fas fa-server me-2"></i>MIS 管理后台
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <?php if ($user): ?>
            <li class="nav-item"><a class="nav-link" href="<?= e(url_with_action('admin.php', 'dashboard')) ?>">概览</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= e(url_with_action('admin.php', 'users')) ?>">用户管理</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= e(url_with_action('admin.php', 'services')) ?>">服务管理</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= e(url_with_action('admin.php', 'orders')) ?>">订单管理</a></li>
          <?php endif; ?>
        </ul>
        <ul class="navbar-nav ms-auto">
          <?php if ($user): ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <?= e($user['email']) ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= e(url_with_action('admin.php', 'logout')) ?>">退出登录</a></li>
                </ul>
            </li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?= e(url_with_action('admin.php', 'login')) ?>">登录</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <?php if (!empty($flash)): ?>
      <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php require template_path($__template); ?>
  </div>

  <footer class="text-center text-muted py-4 mt-5 border-top">
      <small>&copy; <?= date('Y') ?> Minimal IDC System. All rights reserved.</small>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>