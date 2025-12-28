<?php
// 确保 bootstrap.php 已包含
require_once MIS_ROOT . '/includes/bootstrap.php'; 

// 检查 $products 是否为空
if (empty($products)): ?>
  <div class="mis-card">
    <div class="alert alert-info text-center">
      <i class="fas fa-info-circle me-2"></i>暂无产品，请联系管理员添加。
    </div>
  </div>
<?php else: ?>
  <div class="mis-card">
    <h2><i class="fas fa-shopping-cart text-primary me-2"></i>云产品列表</h2>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>产品名称</th>
            <th>交付方式</th>
            <th>计费模式</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><strong>#<?= e($p['id']) ?></strong></td>
              <td><span class="fw-bold"><?= e($p['name']) ?></span></td>
              <td>
                <?php if ($p['delivery_mode'] === 'manual'): ?>
                  <span class="badge bg-info">人工交付</span>
                <?php elseif ($p['delivery_mode'] === 'provider_api'): ?>
                  <span class="badge bg-success">API 自动交付</span>
                <?php else: ?>
                  <?= e($p['delivery_mode']) ?>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($p['billing_mode'] === 'periodic'): ?>
                  <span class="badge bg-primary">周期计费</span>
                <?php elseif ($p['billing_mode'] === 'metered'): ?>
                  <span class="badge bg-warning text-dark">按量计费</span>
                <?php else: ?>
                  <?= e($p['billing_mode']) ?>
                <?php endif; ?>
              </td>
              <td>
                <a class="btn btn-sm btn-primary" href="<?= e(url_with_action('index.php', 'buy', ['product_id' => $p['id']])) ?>">
                  <i class="fas fa-shopping-cart me-1"></i>购买
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>
