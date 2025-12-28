<?php
$ip = $service['ip'] ?? null;
$isNetdataAvailable = false;
if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
    $isNetdataAvailable = monitor_check_netdata_availability($ip, 19999);
}
?>
<div class="mis-card mb-4">
  <h2><i class="fas fa-server text-primary me-2"></i>交付服务 #<?= e($service['id']) ?></h2>
  <div class="text-muted small mb-3">
    用户: <strong><?= e($service['user_email']) ?></strong> | 
    产品: <strong><?= e($service['product_name']) ?></strong>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <?php foreach ($errors as $error): ?>
    <div><i class="fas fa-exclamation-triangle me-1"></i><?= e($error) ?></div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= e($service['id']) ?>">

    <div class="row">
      <div class="col-lg-6 mb-3">
        <label class="form-label fw-bold">IP地址</label>
        <input class="form-control" name="ip" value="<?= e($service['ip'] ?? '') ?>" required>
      </div>
      <div class="col-lg-6 mb-3">
        <label class="form-label fw-bold">端口</label>
        <input class="form-control" name="port" value="<?= e($service['port'] ?? 22) ?>" required>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-6 mb-3">
        <label class="form-label fw-bold">用户名</label>
        <input class="form-control" name="username" value="<?= e($service['username'] ?? 'root') ?>" required>
      </div>
      <div class="col-lg-6 mb-3">
        <label class="form-label fw-bold">密码 (将被加密存储)</label>
        <input class="form-control" name="password" type="password" value="">
        <div class="form-text">留空则保持密码不变</div>
      </div>
    </div>

    <div class="mt-3">
      <button class="btn btn-primary" type="submit">
        <i class="fas fa-save"></i> 激活 / 更新服务
      </button>
      <a class="btn btn-secondary" href="<?= e(url_with_action('admin.php', 'services')) ?>">
        <i class="fas fa-arrow-left"></i> 返回
      </a>
    </div>
  </form>
</div>

<div class="mis-card">
  <h3><i class="fas fa-chart-line text-success me-2"></i>Netdata监控设置</h3>
  
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 推荐使用 <strong>Netdata</strong> 作为监控解决方案，提供完整的服务器性能可视化
  </div>

  <?php if ($isNetdataAvailable): ?>
  <div class="alert alert-success">
    <i class="fas fa-check-circle"></i> Netdata监控已激活！可直接访问 <a href="http://<?= e($ip) ?>:19999" target="_blank" class="alert-link">http://<?= e($ip) ?>:19999</a> 查看监控面板
  </div>
  <?php else: ?>
  <div class="row mb-3">
    <div class="col-md-3">
      <div class="alert alert-secondary">
        <strong>安装Token:</strong>
        <pre class="mb-0 user-select-all small"><?= e($token ?? '') ?></pre>
      </div>
    </div>
    <div class="col-md-9">
      <div class="alert alert-primary">
        <strong>在服务器上安装Netdata:</strong><br>
        <small class="d-block mb-2">在目标服务器上以root用户运行：</small>
        <pre class="mb-0 user-select-all">curl -fsSL "<?= e(base_url()) ?>/api/agent_install.php?token=<?= e($token ?? '') ?>" | bash</pre>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <div class="row align-items-center">
    <div class="col-md-8">
      <ul class="text-muted small mb-0">
        <li>✅ 一键自动安装，支持Debian/Ubuntu/CentOS等主流系统</li>
        <li>✅ 实时监控数千项指标(CPU、内存、磁盘、网络、进程等)</li>
        <li>✅ 提供美观的Web图表界面，可直接嵌入MIS Cloud</li>
        <li>✅ CPU占用率<2%，适合IDC/vps服务器环境</li>
        <li>✅ 自动配置防火墙(UFW/firewalld)，开箱即用</li>
      </ul>
    </div>
    <div class="col-md-4 text-end">
      <?php if (($service['ip'] ?? '') !== '' && ($service['username'] ?? '') !== '' && ($service['password_enc'] ?? '') !== ''): ?>
      <form method="post" action="<?= e(url_with_action('admin.php', 'service_install_monitor')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e($service['id']) ?>">
        <button class="btn btn-success" type="submit">
          <i class="fas fa-rocket"></i> 通过SSH安装Netdata
        </button>
      </form>
      <?php else: ?>
      <div class="text-muted small">
        <i class="fas fa-info-circle"></i> 需要先配置SSH连接信息
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>