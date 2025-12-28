<div class="card">
  <h2>Users</h2>

  <?php if (empty($users)): ?>
    <div class="small">No users.</div>
  <?php else: ?>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Email</th>
          <th>Balance</th>
          <th>Admin</th>
          <th>Status</th>
          <th>Created</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= e($u['id']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['balance']) ?></td>
            <td><?= (int)$u['is_admin'] === 1 ? 'yes' : 'no' ?></td>
            <td><?= e($u['status']) ?></td>
            <td class="small"><?= e($u['created_at']) ?></td>
            <td>
              <a class="btn" href="<?= e(url_with_action('admin.php', 'user_balance', ['id' => $u['id']])) ?>">Adjust balance</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
