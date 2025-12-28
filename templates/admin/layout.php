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
  
  <!-- 引入 Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- 基础样式 -->
  <link href="<?= e(asset_url('css/style.css')) ?>" rel="stylesheet">
  <!-- HKMC 风格 CSS -->
  <link href="<?= e(asset_url('css/hkmc-style.css')) ?>" rel="stylesheet">
  <!-- Figma 风格的核心 CSS -->
  <link href="<?= e(asset_url('css/figma_custom.css')) ?>" rel="stylesheet">

  <!-- Font Awesome CSS -->
  <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

  <style>
    body {
        background-color: #f5f5f5;
        font-family: "PingFang SC", "Microsoft YaHei", sans-serif;
    }
    a { text-decoration: none; }

    /* Admin Dashboard container overrides */
    .admin-dashboard-container {
        max-width: none;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background-color: #f5f5f5;
        display: flex;
    }
    .admin-dashboard-container .row.g-0 {
        width: 100%;
    }
    /* Sidebar styling */
    .admin-sidebar-col {
        flex: 0 0 264px;
        max-width: 264px;
        background-color: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        padding-top: 16px;
        border-right: 1px solid #eee;
        overflow-y: auto;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 100;
    }
    .content-area {
        margin-left: 264px;
        background-color: #f5f5f5;
        flex: 1;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Header styling within content area */
    .admin-header {
        background: #fff;
        padding: 10px 25px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 90;
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

    .base_upgrade_section--content--95XrT {
        background-color: #e0f7fa;
        border-radius: 8px;
        padding: 16px;
        margin: 16px 8px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .base_upgrade_section--textContainer--ObHaO p {
        margin: 0;
        font-size: 0.9em;
        color: #555;
        line-height: 1.4;
    }

    /* Overrides for Bootstrap forms */
    .form-control, .form-select {
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 8px;
    }
    
    .footer {
        margin-top: auto;
        padding: 20px;
        text-align: center;
        color: #888;
        font-size: 14px;
    }
  </style>
</head>
<body>

<div class="admin-dashboard-container">
    <!-- Admin Sidebar -->
    <div class="admin-sidebar-col">
      <?php require template_path('admin/sidebar.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="content-area">
      <header class="admin-header">
        <div class="logo" style="font-weight:bold; font-size:20px; color:#3cb2b8;">
          <i class="fas fa-cog me-2"></i>MIS 管理控制台
        </div>
        <div class="header-actions">
          <?php if ($user): ?>
              <span class="me-3 text-dark small">管理员: <?= e($user['email']) ?></span>
              <a href="<?= e(url_with_action('admin.php', 'logout')) ?>" class="btn btn-sm btn-danger">退出</a>
          <?php else: ?>
              <a href="<?= e(url_with_action('admin.php', 'login')) ?>" class="btn btn-sm btn-primary">登录</a>
          <?php endif; ?>
        </div>
      </header>

      <div class="container-fluid mt-3 px-4">
        <?php if (!empty($flash)): ?>
          <div class="alert alert-<?= $flash['type'] === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show shadow-sm">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <div class="py-3">
           <?php require template_path($__template); ?>
        </div>
      </div>

      <footer class="footer">
        <div class="copyright">
            MIS Cloud 管理后台 Copyright © <?= date('Y') ?> All Rights Reserved.
        </div>
      </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
