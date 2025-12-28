<?php
// 覆盖原有内容，实现中文和 Bootstrap 样式
require_once MIS_ROOT . '/includes/bootstrap.php'; // 确保 bootstrap.php 已包含

$user = require_login(); // 确保用户已登录

$serviceCountRow = db_fetch_one('SELECT COUNT(*) AS c FROM services WHERE user_id = :uid', ['uid' => (int)$user['id']]);
$orderCountRow = db_fetch_one('SELECT COUNT(*) AS c FROM orders WHERE user_id = :uid', ['uid' => (int)$user['id']]);

$serviceCount = $serviceCountRow ? (int)$serviceCountRow['c'] : 0;
$orderCount = $orderCountRow ? (int)$orderCountRow['c'] : 0;
?>
<div class="mis-card">
  <h2><i class="fas fa-tachometer-alt text-primary me-2"></i>控制台概览</h2>
  
  <div class="row g-4 text-center">
    <!-- 账户余额卡片 -->
    <div class="col-md-3 mb-3">
      <div class="p-3 border rounded bg-light h-100">
        <div class="text-muted mb-2">账户余额</div>
        <h3 class="text-success fw-bold">￥<?= e($user['balance'] ?? '0.0000') ?></h3>
        <span class="btn btn-sm btn-outline-success mt-2">联系管理员充值</span>
      </div>
    </div>

    <!-- 服务卡片 -->
    <div class="col-md-3 mb-3">
      <div class="p-3 border rounded bg-light h-100">
        <div class="text-muted mb-2">有效服务</div>
        <h3 class="text-primary fw-bold"><?= e($serviceCount) ?></h3>
        <a href="<?= e(url_with_action('index.php', 'services')) ?>" class="btn btn-sm btn-outline-primary mt-2">管理服务</a>
      </div>
    </div>

    <!-- 订单卡片 -->
    <div class="col-md-3 mb-3">
      <div class="p-3 border rounded bg-light h-100">
        <div class="text-muted mb-2">订单总数</div>
        <h3 class="text-dark fw-bold"><?= e($orderCount) ?></h3>
        <a href="<?= e(url_with_action('index.php', 'orders')) ?>" class="btn btn-sm btn-outline-secondary mt-2">查看账单</a>
      </div>
    </div>

    <!-- 账号状态卡片 -->
    <div class="col-md-3 mb-3">
      <div class="p-3 border rounded bg-light h-100">
        <div class="text-muted mb-2">账号状态</div>
        <h3 class="text-info fw-bold">正常</h3>
        <span class="badge bg-success mt-2">已激活</span>
      </div>
    </div>
  </div>
</div>

<!-- 快捷入口 -->
<div class="mis-card">
  <h4><i class="fas fa-bolt text-warning me-2"></i>快速开始</h4>
  <div class="row mt-3">
    <div class="col-md-4 mb-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-cloud text-primary"></i> 购买云产品</h5>
          <p class="card-text text-muted small">浏览我们的高性能服务器，即刻开通使用。</p>
          <a href="<?= e(url_with_action('index.php', 'products')) ?>" class="btn btn-primary btn-sm">前往选购</a>
        </div>
      </div>
    </div>
    <!-- Add more quick links if needed -->
  </div>
</div>
