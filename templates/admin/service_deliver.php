<div class="card">
  <h2>Deliver Service #<?= e($service['id']) ?></h2>
  <div class="small">User: <?= e($service['user_email']) ?></div>
  <div class="small">Product: <?= e($service['product_name']) ?></div>

  <?php if (!empty($errors)): ?>
    <div class="flash error">
      <?php foreach ($errors as $err): ?>
        <div><?= e($err) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= e($service['id']) ?>">

    <div class="row">
      <div>
        <div class="small">IP</div>
        <input class="input" name="ip" value="<?= e($service['ip'] ?? '') ?>" required>
      </div>
      <div>
        <div class="small">Port</div>
        <input class="input" name="port" value="<?= e($service['port'] ?? 22) ?>" required>
      </div>
    </div>

    <div class="row" style="margin-top:10px;">
      <div>
        <div class="small">Username</div>
        <input class="input" name="username" value="<?= e($service['username'] ?? 'root') ?>" required>
      </div>
      <div>
        <div class="small">Password (will be encrypted)</div>
        <input class="input" name="password" type="password" value="">
        <div class="small">Leave empty to keep unchanged.</div>
      </div>
    </div>

    <div style="margin-top:12px;">
      <button class="btn" type="submit">Activate / Update</button>
      <a class="btn secondary" href="<?= e(url_with_action('admin.php', 'services')) ?>">Back</a>
    </div>
  </form>
</div>

<div class="card">
  <h3>Monitoring</h3>
  <div class="small">Push token:</div>
  <pre><?= e($token ?? '') ?></pre>

  <div class="small">Manual install (run on target server as root):</div>
  <pre>curl -fsSL "<?= e(base_url()) ?>/api/agent_install.php?token=<?= e($token ?? '') ?>" | bash</pre>

  <form method="post" action="<?= e(url_with_action('admin.php', 'service_install_monitor')) ?>" style="margin-top:12px;">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= e($service['id']) ?>">
    <button class="btn secondary" type="submit">Install Monitor via SSH</button>
  </form>

  <div class="small" style="margin-top:8px;">Requires ext-ssh2 and valid SSH credentials saved for the service.</div>
</div>
