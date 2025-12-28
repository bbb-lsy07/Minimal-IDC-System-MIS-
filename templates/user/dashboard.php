<div class="card">
  <h2>Dashboard</h2>
  <div>邮箱: <b><?= e($user['email']) ?></b></div>
  <div>余额: <b><?= e($user['balance']) ?></b></div>
  <div>订单数: <b><?= e($orderCount) ?></b></div>
  <div>Services: <b><?= e($serviceCount) ?></b></div>
  <div class="small" style="margin-top:10px;">
    Tip: Ask admin to adjust your balance from <code>admin.php</code> to test purchasing.
  </div>
</div>
