<div class="card">
  <h2>Services</h2>
  <?php if (empty($services)): ?>
    <div class="small">No services yet.</div>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
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
            <td><?= e($s['product_name']) ?></td>
            <td><?= e($s['status']) ?></td>
            <td><?= e($s['ip'] ?? '-') ?></td>
            <td class="small"><?= e($s['expire_at'] ?? '-') ?></td>
            <td><a class="btn" href="<?= e(url_with_action('index.php', 'service', ['id' => $s['id']])) ?>">Detail</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
