<div class="card">
  <h2>Products</h2>

  <?php if (empty($products)): ?>
    <div class="small">No products yet.</div>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>产品名称</th>
          <th>Delivery</th>
          <th>计费方式</th>
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
            <td>
              <a class="btn" href="<?= e(url_with_action('index.php', 'buy', ['product_id' => $p['id']])) ?>">Buy</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
