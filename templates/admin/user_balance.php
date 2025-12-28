<div class="card">
  <h2>Adjust Balance</h2>
  <div class="small">User: <?= e($targetUser['email']) ?></div>
  <div class="small">Current balance: <?= e($targetUser['balance']) ?></div>

  <?php if (!empty($errors)): ?>
    <div class="flash error">
      <?php foreach ($errors as $err): ?>
        <div><?= e($err) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="row">
      <div>
        <div class="small">Delta (e.g. 100 or -10)</div>
        <input class="input" name="delta" required>
      </div>
      <div>
        <div class="small">Description</div>
        <input class="input" name="desc" value="Admin adjustment">
      </div>
    </div>

    <div style="margin-top:12px;">
      <button class="btn" type="submit">Apply</button>
      <a class="btn secondary" href="<?= e(url_with_action('admin.php', 'users')) ?>">Back</a>
    </div>
  </form>
</div>
