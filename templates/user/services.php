<div class="card">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">我的服务</h5>
  </div>
  <div class="card-body p-0">
    <?php if (empty($services)): ?>
      <div class="p-4 text-center text-muted">暂无已开通的服务</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>产品名称</th>
              <th>状态</th>
              <th>IP地址</th>
              <th>到期时间</th>
              <th class="text-end">操作</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($services as $s): ?>
              <tr>
                <td>#<?= e($s['id']) ?></td>
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
                <td><code><?= e($s['ip'] ?? '等待分配') ?></code></td>
                <td class="small text-muted"><?= e($s['expire_at'] ?? '永久') ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-primary" href="<?= e(url_with_action('index.php', 'service', ['id' => $s['id']])) ?>">管理</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>