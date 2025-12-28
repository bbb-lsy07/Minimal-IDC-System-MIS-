<div class="mis-card">
  <h2><i class="fas fa-server text-info me-2"></i>我的服务</h2>
  
  <?php if (empty($services)): ?>
    <div class="alert alert-info text-center">
      <i class="fas fa-info-circle me-2"></i>暂无已开通的服务，<a href="<?= e(url_with_action('index.php', 'products')) ?>">前往选购</a>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>服务ID</th>
            <th>产品名称</th>
            <th>状态</th>
            <th>IP地址</th>
            <th>到期时间</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($services as $s): ?>
            <tr>
              <td><strong>#<?= e($s['id']) ?></strong></td>
              <td><span class="fw-bold"><?= e($s['product_name']) ?></span></td>
              <td>
                  <?php 
                  $badgeClass = match($s['status']) {
                      'active' => 'success',
                      'pending' => 'warning',
                      'suspended' => 'danger',
                      'terminated' => 'secondary',
                      default => 'secondary'
                  };
                  $statusName = match($s['status']) {
                      'active' => '运行中',
                      'pending' => '开通中',
                      'suspended' => '已暂停',
                      'terminated' => '已销毁',
                      default => $s['status']
                  };
                  ?>
                  <span class="badge bg-<?= $badgeClass ?>"><?= $statusName ?></span>
              </td>
              <td><code class="user-select-all"><?= e($s['ip'] ?? '等待分配') ?></code></td>
              <td class="small text-muted">
                <?php if ($s['expire_at']): ?>
                  <?= e($s['expire_at']) ?>
                <?php else: ?>
                  <span class="badge bg-success">永久</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <a class="btn btn-sm btn-primary" href="<?= e(url_with_action('index.php', 'service', ['id' => $s['id']])) ?>">
                  <i class="fas fa-cog"></i> 管理
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
