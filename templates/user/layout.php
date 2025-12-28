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
  <title>MIS 云服务控制台</title>
  
  <!-- 引入 Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- HKMC 风格 CSS -->
  <link href="<?= e(asset_url('css/hkmc-style.css')) ?>" rel="stylesheet">
  <!-- Figma 风格的核心 CSS -->
  <link href="<?= e(asset_url('css/figma_custom.css')) ?>" rel="stylesheet">

  <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
  
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
    
    .nav-container .nav-item {
        list-style: none;
        padding: 8px 16px;
        margin: 0;
        cursor: pointer;
        border-radius: 8px;
        transition: background-color 0.2s ease, color 0.2s ease;
        color: #333;
        font-weight: 500;
        display: inline-block;
    }
    .nav-container .nav-item.active {
        background-color: #e0f7fa;
        color: #3cb2b8;
    }
    .nav-container .nav-item:hover {
        background-color: #f0f0f0;
    }
    .nav-container .nav-item i {
        margin-right: 8px;
        color: #3cb2b8;
    }
  </style>
</head>
<body>

  <!-- Header Section -->
  <header>
    <div class="container header-container">
      <div class="logo" style="font-weight:bold; font-size:24px; color:#3cb2b8;">
        <i class="fas fa-cloud me-2"></i>MIS Cloud
      </div>
      <div class="header-actions">
        <?php if ($user): ?>
            <span class="me-3 text-dark d-none d-md-inline">欢迎, <?= e($user['email']) ?></span>
            <span class="me-3 text-dark fw-bold">余额: ￥<?= e($user['balance']) ?></span>
            <a href="<?= e(url_with_action('index.php', 'logout')) ?>" class="btn btn-sm btn-danger">退出</a>
        <?php else: ?>
            <a href="<?= e(url_with_action('index.php', 'login')) ?>" class="btn btn-sm btn-primary">登录</a>
            <a href="<?= e(url_with_action('index.php', 'register')) ?>" class="btn btn-sm btn-outline-primary ms-2">注册</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Navigation -->
    <div class="nav-container border-bottom bg-white shadow-sm">
      <div class="container py-2">
        <nav class="d-flex gap-2">
          <a class="nav-item <?= $__template == 'user/dashboard.php' ? 'active' : '' ?>" href="<?= e(url_with_action('index.php', 'dashboard')) ?>">
            <i class="fas fa-home"></i> 概览
          </a>
          <a class="nav-item <?= $__template == 'user/products.php' ? 'active' : '' ?>" href="<?= e(url_with_action('index.php', 'products')) ?>">
            <i class="fas fa-shopping-cart"></i> 购买产品
          </a>
          <a class="nav-item <?= str_contains($__template, 'service') ? 'active' : '' ?>" href="<?= e(url_with_action('index.php', 'services')) ?>">
            <i class="fas fa-server"></i> 我的服务
          </a>
          <a class="nav-item <?= $__template == 'user/orders.php' ? 'active' : '' ?>" href="<?= e(url_with_action('index.php', 'orders')) ?>">
            <i class="fas fa-file-invoice"></i> 账单记录
          </a>
        </nav>
      </div>
    </div>
  </header>

  <!-- Flash Messages -->
  <div class="container mt-4">
    <?php if (!empty($flash)): ?>
      <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show shadow-sm">
        <?= e($flash['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
  </div>

  <!-- Main Content Area -->
  <main class="dashboard-container">
      <?php require template_path($__template); ?>
  </main>

  <!-- Footer -->
  <footer class="footer mt-5 py-4 border-top bg-white">
    <div class="container text-center text-muted small">
        MIS Cloud 版权所有 Copyright © <?= date('Y') ?> All Rights Reserved.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
