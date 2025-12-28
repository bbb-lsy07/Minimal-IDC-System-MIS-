<div class="mis-card">
  <h2><i class="fas fa-file-invoice text-warning me-2"></i>账单记录</h2>
  
  <?php if (empty($orders)): ?>
    <div class="alert alert-info text-center">
      <i class="fas fa-info-circle me-2"></i>暂无账单记录
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>订单ID</th>
            <th>产品名称</th>
            <th>金额</th>
            <th>计费周期</th>
            <th>状态</th>
            <th>创建时间</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td><strong>#<?= e($o['id']) ?></strong></td>
              <td><?= e($o['product_name']) ?></td>
              <td>
                <?php if ($o['amount'] > 0): ?>
                  <span class="text-success">￥<?= e($o['amount']) ?></span>
                <?php else: ?>
                  <span class="text-muted">免费</span>
                <?php endif; ?>
              </td>
              <td><?= e($o['billing_cycle']) ?></td>
              <td>
                <?php 
                $statusClass = match($o['status']) {
                    'paid' => 'success',
                    'pending' => 'warning',
                    'provisioning' => 'info',
                    'active' => 'success',
                    'failed' => 'danger',
                    default => 'secondary'
                };
                $statusName = match($o['status']) {
                    'paid' => '已支付',
                    'pending' => '待处理',
                    'provisioning' => '开通中',
                    'active' => '已激活',
                    'failed' => '失败',
                    default => $o['status']
                };
                ?>
                <span class="badge bg-<?= $statusClass ?>"><?= $statusName ?></span>
              </td>
              <td class="small text-muted"><?= e($o['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
