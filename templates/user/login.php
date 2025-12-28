<div class="card">
  <h2>Login</h2>

  <?php if (!empty($errors)): ?>
    <div class="flash error">
      <?php foreach ($errors as $err): ?>
        <div><?= e($err) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= e(url_with_action('index.php', 'login')) ?>">
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
      <a class="btn secondary" href="<?= e(url_with_action('index.php', 'register')) ?>">Register</a>
    </div>
  </form>
</div>
