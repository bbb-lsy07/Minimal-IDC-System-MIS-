<?php
$ip = $service['ip'] ?? null;
$isNetdataAvailable = false;
if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
    $isNetdataAvailable = monitor_check_netdata_availability($ip, 19999);
}

$logSeries = array_reverse($logs);
$timestamps = array_map(fn($r) => (string)$r['created_at'], $logSeries);
$cpuSeries = array_map(fn($r) => (float)$r['cpu'], $logSeries);
$memSeries = array_map(fn($r) => (float)$r['mem'], $logSeries);
$diskSeries = array_map(fn($r) => (float)$r['disk'], $logSeries);
?>
<div class="mis-card mb-4">
  <h2><i class="fas fa-server text-primary me-2"></i>服务详情</h2>
  
  <div class="row mb-3">
    <div class="col-md-2"><strong>服务ID:</strong></div>
    <div class="col-md-4">#<?= e($service['id']) ?></div>
    <div class="col-md-2"><strong>产品:</strong></div>
    <div class="col-md-4"><?= e($service['product_name']) ?></div>
  </div>
  <div class="row mb-3">
    <div class="col-md-2"><strong>状态:</strong></div>
    <div class="col-md-4">
      <?php 
      $badgeClass = match($service['status']) {
          'active' => 'success',
          'pending' => 'warning',
          'suspended' => 'danger',
          'terminated' => 'secondary',
          default => 'secondary'
      };
      $statusName = match($service['status']) {
          'active' => '运行中',
          'pending' => '开通中',
          'suspended' => '已暂停',
          'terminated' => '已销毁',
          default => $service['status']
      };
      ?>
      <span class="badge bg-<?= $badgeClass ?>"><?= $statusName ?></span>
    </div>
    <div class="col-md-2"><strong>IP地址:</strong></div>
    <div class="col-md-4"><code><?= e($service['ip'] ?? '-') ?></code></div>
  </div>
  <div class="row mb-3">
    <div class="col-md-2"><strong>SSH连接:</strong></div>
    <div class="col-md-4">
      <code class="user-select-all"><?= e(($service['username'] ?? '-') . '@' . ($service['ip'] ?? '-') . ':' . ($service['port'] ?? 22)) ?></code>
    </div>
    <div class="col-md-2"><strong>到期时间:</strong></div>
    <div class="col-md-4"><?= e($service['expire_at'] ?? '永久') ?></div>
  </div>

  <div class="alert alert-info small">
    <i class="fas fa-info-circle"></i> SSH远程操作需要服务器安装PHP SSH2扩展，且管理员必须配置连接信息
  </div>

  <div class="row">
    <div class="col-md-6 mb-2">
      <form method="post" action="<?= e(url_with_action('index.php', 'service_reboot')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e($service['id']) ?>">
        <button class="btn btn-warning w-100" type="submit">
          <i class="fas fa-sync"></i> 重启服务器 (SSH)
        </button>
      </form>
    </div>

    <div class="col-md-6 mb-2">
      <form method="post" action="<?= e(url_with_action('index.php', 'service_install_monitor')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e($service['id']) ?>">
        <button class="btn btn-info w-100 text-white" type="submit">
          <i class="fas fa-upload"></i> 安装Netdata监控
        </button>
      </form>
    </div>
  </div>
</div>

<div class="mis-card">
  <h3><i class="fas fa-chart-line text-success me-2"></i>实时监控</h3>
  
  <?php if ($isNetdataAvailable): ?>
  <div class="alert alert-success">
    <i class="fas fa-check-circle"></i> Netdata监控已激活！<a href="http://<?= e($ip) ?>:19999" target="_blank" class="alert-link">点击访问监控面板</a>
  </div>

  <div class="row">
    <div class="col-lg-6 mb-4">
      <div class="border rounded p-3 h-100">
        <h5><i class="fas fa-microchip text-primary me-1"></i>CPU 使用</h5>
        <div style="height: 250px;">
          <iframe 
            src="http://<?= e($ip) ?>:19999/#theme=slate;chart=system.cpu"
            style="width: 100%; height: 100%; border: none;"
            title="CPU Chart">
          </iframe>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6 mb-4">
      <div class="border rounded p-3 h-100">
        <h5><i class="fas fa-memory text-success me-1"></i>内存使用</h5>
        <div style="height: 250px;">
          <iframe 
            src="http://<?= e($ip) ?>:19999/#theme=slate;chart=system.ram"
            style="width: 100%; height: 100%; border: none;"
            title="Memory Chart">
          </iframe>
        </div>
      </div>
    </div>

    <div class="col-lg-6 mb-4">
      <div class="border rounded p-3 h-100">
        <h5><i class="fas fa-hdd text-warning me-1"></i>磁盘使用</h5>
        <div style="height: 250px;">
          <iframe 
            src="http://<?= e($ip) ?>:19999/#theme=slate;chart=disk_space._"
            style="width: 100%; height: 100%; border: none;"
            title="Disk Chart">
          </iframe>
        </div>
      </div>
    </div>
    
    <div class="col-lg-6 mb-4">
      <div class="border rounded p-3 h-100">
        <h5><i class="fas fa-network-wired text-info me-1"></i>网络流量</h5>
        <div style="height: 250px;">
          <iframe 
            src="http://<?= e($ip) ?>:19999/#theme=slate;chart=net.eth0"
            style="width: 100%; height: 100%; border: none;"
            title="Network Chart">
          </iframe>
        </div>
      </div>
    </div>
  </div>

  <div class="text-center mb-3">
    <a href="http://<?= e($ip) ?>:19999" target="_blank" class="btn btn-success">
      <i class="fas fa-external-link-alt"></i> 访问完整监控面板
    </a>
  </div>

  <?php else: ?>

  <div class="alert alert-info">
    <strong>需要安装Netdata监控代理</strong><br>
    <small>Netdata是一个轻量级、高性能的监控系统，提供实时图表、告警、可视化功能</small>
  </div>

  <div class="row mb-3">
    <div class="col-md-3">
      <div class="alert alert-secondary">
        <strong>安装Token:</strong>
        <pre class="mb-0 user-select-all small"><?= e($token) ?></pre>
      </div>
    </div>
    <div class="col-md-9">
      <div class="alert alert-primary">
        <strong>在服务器上安装Netdata:</strong><br>
        <small class="d-block mb-2">以root用户登录服务器，运行以下命令：</small>
        <pre class="mb-0 user-select-all">curl -fsSL "<?= e(base_url()) ?>/api/agent_install.php?token=<?= e($token) ?>" | bash</pre>
      </div>
    </div>
  </div>

  <div class="text-muted small p-3 bg-light rounded">
    <i class="fas fa-info-circle"></i> Netdata特性：
    <ul class="mb-0 mt-1">
      <li>✅ 一键安装，自动配置</li>
      <li>✅ 数千种实时指标监控</li>
      <li>✅ 美观可交互的Web图表</li>
      <li>✅ < 2% CPU占用</li>
      <li>✅ 支持自定义告警规则</li>
      <li>✅ 防火墙自动配置</li>
    </ul>
  </div>

  <?php endif; ?>

  <?php if (count($logs) > 0): ?>
  <div class="mt-4">
    <h5 class="mb-3"><i class="fas fa-database me-1"></i>历史数据</h5>
    <div style="height: 300px;">
      <div id="chart" style="width:100%;height:100%;"></div>
      <p class="text-muted small mt-2">
        <i class="fas fa-info-circle me-1"></i>
        显示最近30条数据点 (共<?= e(count($logs)) ?>条)
      </p>
    </div>
  </div>
  <?php endif; ?>

</div>

<?php if (count($logs) > 0): ?>
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
  const el = document.getElementById('chart');
  if (el) {
    const chart = echarts.init(el);
    const option = {
      tooltip: { 
        trigger: 'axis',
        backgroundColor: 'rgba(255,255,255,0.95)',
        borderColor: '#dee2e6'
      },
      legend: { 
        data: ['CPU %', '内存 %', '磁盘 %'],
        top: 10
      },
      grid: {
        top: 60,
        bottom: 40,
        left: 40,
        right: 40
      },
      xAxis: { 
        type: 'category', 
        data: <?= json_encode(array_map(function($ts) {
            $datetime = new DateTime($ts);
            return $datetime->format('m-d H:i');
        }, array_slice($timestamps, -30))) ?>,
        axisLabel: { rotate: 45 }
      },
      yAxis: { 
        type: 'value',
        max: 100,
        axisLabel: { formatter: '{value}%' }
      },
      series: [
        { 
          name: 'CPU %', 
          type: 'line', 
          smooth: true, 
          data: <?= json_encode(array_slice($cpuSeries, -30)) ?>,
          itemStyle: { color: '#3cb2b8' }
        },
        { 
          name: '内存 %', 
          type: 'line', 
          smooth: true, 
          data: <?= json_encode(array_slice($memSeries, -30)) ?>,
          itemStyle: { color: '#f55053' }
        },
        { 
          name: '磁盘 %', 
          type: 'line', 
          smooth: true, 
          data: <?= json_encode(array_slice($diskSeries, -30)) ?>,
          itemStyle: { color: '#f5a623' }
        }
      ]
    };
    chart.setOption(option);
    window.addEventListener('resize', () => chart.resize());
  }
</script>
<?php endif; ?>