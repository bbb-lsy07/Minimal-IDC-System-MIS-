<div class="mis-card">
  <h2><i class="fas fa-shopping-cart text-success me-2"></i>购买产品: <?= e($product['name']) ?></h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $err): ?>
        <div><?= e($err) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="mb-3">
    <strong>计费模式:</strong>
    <?php if ($product['billing_mode'] === 'periodic'): ?>
      <span class="badge bg-primary">周期计费</span>
    <?php elseif ($product['billing_mode'] === 'metered'): ?>
      <span class="badge bg-warning">按量计费</span>
    <?php else: ?>
      <?= e($product['billing_mode']) ?>
    <?php endif; ?>
  </div>

  <form method="post" action="<?= e(url_with_action('index.php', 'buy')) ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">

    <?php if ($product['billing_mode'] === 'periodic'): ?>
      <div class="mb-3">
        <label class="form-label">选择周期</label>
        <select class="form-select" name="cycle" required>
          <?php foreach ($priceJson as $k => $v): ?>
            <option value="<?= e($k) ?>"><?= e($k) ?> - ￥<?= e($v) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php else: ?>
      <div class="mb-3">
        <label class="form-label">按量计费配置</label>
        <pre class="small bg-light p-2 rounded"><?= e(json_encode($priceJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
      </div>
      <input type="hidden" name="cycle" value="metered">
    <?php endif; ?>

    <div class="d-flex gap-2">
      <button class="btn btn-success" type="submit">
        <i class="fas fa-check-circle me-1"></i>确认购买
      </button>
      <a class="btn btn-secondary" href="<?= e(url_with_action('index.php', 'products')) ?>">
        <i class="fas fa-arrow-left me-1"></i>返回
      </a>
    </div>
  </form>
</div>
