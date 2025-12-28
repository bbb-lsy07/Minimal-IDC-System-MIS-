<div class="card">
  <h2>Admin Login</h2>

  <?php if (!empty($errors)): ?>
    <div class="flash error">
      <?php foreach ($errors as $err): ?>
        <div><?= e($err) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= e(url_with_action('admin.php', 'login')) ?>">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

    <div class="row">
      <div>
        <div class="small">Email</div>
        <input class="input" name="email" type="email" required>
      </div>
      <div>
        <div class="small">Password</div>
        <input class="input" name="password" type="password" required>
      </div>
    </div>

    <div style="margin-top:12px;">
      <button class="btn" type="submit">Login</button>
      <a class="btn secondary" href="<?= e(url_with_action('index.php', 'login')) ?>">User site</a>
    </div>
  </form>

  <div class="small" style="margin-top:10px;">
    Note: set <code>users.is_admin = 1</code> for your admin account.
  </div>
</div>
