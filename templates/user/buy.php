<div class="card">
  <h2>Buy: <?= e($product['name']) ?></h2>

  <?php if (!empty($errors)): ?>
    <div class="flash error">
      <?php foreach ($errors as $err): ?>
        <div><?= e($err) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="small">Billing mode: <b><?= e($product['billing_mode']) ?></b></div>

  <form method="post" action="<?= e(url_with_action('index.php', 'buy')) ?>" style="margin-top:12px;">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="product_id" value="<?= e($product['id']) ?>">

    <?php if ($product['billing_mode'] === 'periodic'): ?>
      <div class="small">Choose cycle</div>
      <select class="input" name="cycle" required>
        <?php foreach ($priceJson as $k => $v): ?>
          <option value="<?= e($k) ?>"><?= e($k) ?> - <?= e($v) ?></option>
        <?php endforeach; ?>
      </select>
    <?php else: ?>
      <div class="small">This product is metered. Price config:</div>
      <pre class="small"><?= e(json_encode($priceJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?></pre>
      <input type="hidden" name="cycle" value="metered">
    <?php endif; ?>

    <div style="margin-top:12px;">
      <button class="btn" type="submit">Confirm purchase</button>
      <a class="btn secondary" href="<?= e(url_with_action('index.php', 'products')) ?>">Back</a>
    </div>
  </form>
</div>
