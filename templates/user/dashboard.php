<div class="card">
  <h2>Dashboard</h2>
  <div>Email: <b><?= e($user['email']) ?></b></div>
  <div>Balance: <b><?= e($user['balance']) ?></b></div>
  <div>Orders: <b><?= e($orderCount) ?></b></div>
  <div>Services: <b><?= e($serviceCount) ?></b></div>
  <div class="small" style="margin-top:10px;">
    Tip: Ask admin to adjust your balance from <code>admin.php</code> to test purchasing.
  </div>
</div>
