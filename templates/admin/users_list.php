<div class="mis-card">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0"><i class="fas fa-users text-primary me-2"></i>用户管理</h2>
    <span class="badge bg-light text-dark border">共 <?= count($users) ?> 位用户</span>
  </div>

  <?php if (empty($users)): ?>
    <div class="alert alert-info">暂无用户数据。</div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>电子邮箱</th>
            <th>账户余额</th>
            <th>管理员</th>
            <th>状态</th>
            <th>注册时间</th>
            <th class="text-end">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><strong>#<?= e($u['id']) ?></strong></td>
              <td><?= e($u['email']) ?></td>
              <td class="text-success fw-bold">￥<?= e($u['balance']) ?></td>
              <td>
                <?php if ((int)$u['is_admin'] === 1): ?>
                  <span class="badge bg-primary">是 (Admin)</span>
                <?php else: ?>
                  <span class="badge bg-light text-dark">否</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($u['status'] === 'active'): ?>
                  <span class="badge bg-success">正常</span>
                <?php else: ?>
                  <span class="badge bg-danger"><?= e($u['status']) ?></span>
                <?php endif; ?>
              </td>
              <td class="small text-muted"><?= e($u['created_at']) ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="<?= e(url_with_action('admin.php', 'user_balance', ['id' => $u['id']])) ?>">
                  <i class="fas fa-coins me-1"></i>调整余额
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
