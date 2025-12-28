<?php
$isEdit = !empty($product);
$priceJson = $isEdit ? (string)$product['price_json'] : "{\n  \"month\": 10,\n  \"quarter\": 28,\n  \"year\": 100\n}";
?>
<div class="card">
  <h2><?= $isEdit ? 'Edit' : 'New' ?> Product</h2>

  <?php if (!empty($errors)): ?>
    <div class="flash error">
      <?php foreach ($errors as $err): ?>
        <div><?= e($err) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div>
      <div class="small">Name</div>
      <input class="input" name="name" value="<?= e($isEdit ? $product['name'] : '') ?>" required>
    </div>

    <div class="row" style="margin-top:10px;">
      <div>
        <div class="small">Delivery mode</div>
        <select class="input" name="delivery_mode">
          <?php $dm = $isEdit ? $product['delivery_mode'] : 'manual'; ?>
          <option value="manual" <?= $dm==='manual'?'selected':'' ?>>manual</option>
          <option value="provider_api" <?= $dm==='provider_api'?'selected':'' ?>>provider_api</option>
        </select>
      </div>
      <div>
        <div class="small">Billing mode</div>
        <select class="input" name="billing_mode">
          <?php $bm = $isEdit ? $product['billing_mode'] : 'periodic'; ?>
          <option value="periodic" <?= $bm==='periodic'?'selected':'' ?>>periodic</option>
          <option value="metered" <?= $bm==='metered'?'selected':'' ?>>metered</option>
        </select>
      </div>
      <div>
        <div class="small">Status</div>
        <?php $st = $isEdit ? $product['status'] : 'active'; ?>
        <select class="input" name="status">
          <option value="active" <?= $st==='active'?'selected':'' ?>>active</option>
          <option value="hidden" <?= $st==='hidden'?'selected':'' ?>>hidden</option>
          <option value="disabled" <?= $st==='disabled'?'selected':'' ?>>disabled</option>
        </select>
      </div>
    </div>

    <div style="margin-top:10px;">
      <div class="small">price_json (JSON)</div>
      <textarea class="input" name="price_json" rows="8" style="font-family:ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;"><?= e($priceJson) ?></textarea>
      <div class="small">For metered example: {"per_second": 0.00002}</div>
    </div>

    <div style="margin-top:12px;">
      <button class="btn" type="submit">Save</button>
      <a class="btn secondary" href="<?= e(url_with_action('admin.php', 'products')) ?>">Back</a>
    </div>
  </form>
</div>
