<?php
$logSeries = array_reverse($logs);
$timestamps = array_map(fn($r) => (string)$r['created_at'], $logSeries);
$cpuSeries = array_map(fn($r) => (float)$r['cpu'], $logSeries);
$memSeries = array_map(fn($r) => (float)$r['mem'], $logSeries);
$diskSeries = array_map(fn($r) => (float)$r['disk'], $logSeries);
?>
<div class="card">
  <h2>Service #<?= e($service['id']) ?> - <?= e($service['product_name']) ?></h2>
  <div>Status: <b><?= e($service['status']) ?></b></div>
  <div>IP: <b><?= e($service['ip'] ?? '-') ?></b></div>
  <div>SSH: <b><?= e(($service['username'] ?? '-') . '@' . ($service['ip'] ?? '-') . ':' . ($service['port'] ?? '-')) ?></b></div>
  <div>Expire: <b><?= e($service['expire_at'] ?? '-') ?></b></div>

  <div style="margin-top:12px;" class="row">
    <div>
      <form method="post" action="<?= e(url_with_action('index.php', 'service_reboot')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e($service['id']) ?>">
        <button class="btn" type="submit">Reboot (SSH)</button>
      </form>
    </div>

    <div>
      <form method="post" action="<?= e(url_with_action('index.php', 'service_install_monitor')) ?>">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= e($service['id']) ?>">
        <button class="btn secondary" type="submit">Install Monitor (SSH)</button>
      </form>
    </div>
  </div>

  <div class="small" style="margin-top:8px;">
    SSH actions require ext-ssh2 and admin must deliver IP/username/password.
  </div>
</div>

<div class="card">
  <h3>Monitoring</h3>
  <div class="small">Push token:</div>
  <pre><?= e($token) ?></pre>

  <div class="small">Install agent (run on the target server as root):</div>
  <pre>curl -fsSL "<?= e(base_url()) ?>/api/agent_install.php?token=<?= e($token) ?>" | bash</pre>

  <div id="chart" style="width:100%;height:360px;"></div>
  <div class="small">Data points: <?= e(count($logs)) ?> (latest first in DB)</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
  const el = document.getElementById('chart');
  const chart = echarts.init(el);
  const option = {
    tooltip: { trigger: 'axis' },
    legend: { data: ['CPU %', 'Mem %', 'Disk %'] },
    xAxis: { type: 'category', data: <?= json_encode($timestamps) ?> },
    yAxis: { type: 'value' },
    series: [
      { name: 'CPU %', type: 'line', smooth: true, data: <?= json_encode($cpuSeries) ?> },
      { name: 'Mem %', type: 'line', smooth: true, data: <?= json_encode($memSeries) ?> },
      { name: 'Disk %', type: 'line', smooth: true, data: <?= json_encode($diskSeries) ?> }
    ]
  };
  chart.setOption(option);
</script>
