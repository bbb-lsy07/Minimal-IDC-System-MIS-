<div class="card">
  <h2>Services</h2>

  <?php if (empty($services)): ?>
    <div class="small">No services.</div>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Product</th>
          <th>Status</th>
          <th>IP</th>
          <th>Expire</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $s): ?>
          <tr>
            <td><?= e($s['id']) ?></td>
            <td><?= e($s['user_email']) ?></td>
            <td><?= e($s['product_name']) ?></td>
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
            <td><?= e($s['ip'] ?? '-') ?></td>
            <td class="small"><?= e($s['expire_at'] ?? '-') ?></td>
            <td>
              <a class="btn" href="<?= e(url_with_action('admin.php', 'service_deliver', ['id' => $s['id']])) ?>">Deliver</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
