<?php
/** @var string $__template */
/** @var array $__vars */

$user = auth_user();
extract($__vars, EXTR_SKIP);
?><!doctype html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MIS 管理后台</title>
  <!-- 基础样式 (必须添加这一行) -->
  <link href="<?= e(asset_url('css/style.css')) ?>" rel="stylesheet">
  <!-- HKMC 风格 CSS -->
  <link href="<?= e(asset_url('css/hkmc-style.css')) ?>" rel="stylesheet">
  <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <style>
    body { 
      background-color: #f5f5f5; 
      font-family: "PingFang SC", "Microsoft YaHei", sans-serif; 
    }
    a { text-decoration: none; }
    
    .dashboard-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 15px;
        min-height: 60vh;
    }
    .mis-card {
        background: #fff;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }
    .mis-card h2 {
        font-size: 24px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        color: #333;
    }
    .nav-item { list-style: none; }
    .header-actions .btn { padding: 8px 20px; color: #fff; }
    .navbar { padding: 0; }
    .navbar-nav .nav-link { padding: 15px 20px; }
  </style>
</head>
<body>

  <!-- Header Section -->
  <header>
    <div class="container header-container">
      <div class="logo" style="font-weight:bold; font-size:24px; color:#3cb2b8;">
        <i class="fas fa-cog me-2"></i>MIS 管理后台
      </div>
      <div class="header-actions">
        <?php if ($user): ?>
            <span class="me-3 text-dark">管理员: <?= e($user['email']) ?></span>
            <a href="<?= e(url_with_action('admin.php', 'logout')) ?>" class="btn btn-primary" style="background-color:#f55053;">退出</a>
        <?php else: ?>
            <a href="<?= e(url_with_action('admin.php', 'login')) ?>" class="btn btn-primary">登录</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Navigation -->
    <div class="nav-container">
      <div class="container">
        <nav class="d-flex gap-3">
          <a class="nav-item <?= $__template == 'admin/dashboard.php' ? 'active' : '' ?>" href="<?= e(url_with_action('admin.php', 'dashboard')) ?>">
            <i class="fas fa-tachometer-alt"></i> 管理概览
          </a>
          <a class="nav-item <?= str_contains($__template, 'product') ? 'active' : '' ?>" href="<?= e(url_with_action('admin.php', 'products')) ?>">
            <i class="fas fa-box"></i> 产品管理
          </a>
          <a class="nav-item <?= str_contains($__template, 'service') ? 'active' : '' ?>" href="<?= e(url_with_action('admin.php', 'services')) ?>">
            <i class="fas fa-server"></i> 服务管理
          </a>
          <a class="nav-item <?= $__template == 'admin/orders_list.php' ? 'active' : '' ?>" href="<?= e(url_with_action('admin.php', 'orders')) ?>">
            <i class="fas fa-file-invoice"></i> 订单管理
          </a>
          <a class="nav-item <?= str_contains($__template, 'user') ? 'active' : '' ?>" href="<?= e(url_with_action('admin.php', 'users')) ?>">
            <i class="fas fa-users"></i> 用户管理
          </a>
        </nav>
      </div>
    </div>
  </header>

  <!-- Flash Messages -->
  <div class="container mt-3">
    <?php if (!empty($flash)): ?>
      <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show shadow-sm">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
  </div>

  <!-- Main Content Area -->
  <div class="dashboard-container">
      <?php require template_path($__template); ?>
  </div>

  <!-- Footer -->
  <div class="footer">
    <div class="copyright">
        MIS Cloud 管理后台 Copyright © <?= date('Y') ?> All Rights Reserved.
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
