<div class="card">
  <h2>Orders</h2>
  <?php if (empty($orders)): ?>
    <div class="small">No orders yet.</div>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Product</th>
          <th>Amount</th>
          <th>Cycle</th>
          <th>Status</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><?= e($o['id']) ?></td>
            <td><?= e($o['product_name']) ?></td>
            <td><?= e($o['amount']) ?></td>
            <td><?= e($o['billing_cycle']) ?></td>
            <td><?= e($o['status']) ?></td>
            <td class="small"><?= e($o['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
