<div class="card">
  <h2>Products</h2>
  <div style="margin-bottom:10px;">
    <a class="btn" href="<?= e(url_with_action('admin.php', 'product_edit')) ?>">New product</a>
  </div>

  <?php if (empty($products)): ?>
    <div class="small">No products yet.</div>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Delivery</th>
          <th>Billing</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td><?= e($p['id']) ?></td>
            <td><?= e($p['name']) ?></td>
            <td><?= e($p['delivery_mode']) ?></td>
            <td><?= e($p['billing_mode']) ?></td>
            <td><?= e($p['status']) ?></td>
            <td><a class="btn" href="<?= e(url_with_action('admin.php', 'product_edit', ['id' => $p['id']])) ?>">Edit</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
