<div class="mis-card">
  <h2><i class="fas fa-tachometer-alt text-primary me-2"></i>管理概览</h2>
  
  <div class="row g-4">
    <div class="col-md-3">
      <div class="card h-100 border-0 shadow-sm bg-light">
        <div class="card-body text-center">
          <div class="text-muted mb-2">产品管理</div>
          <h3><i class="fas fa-box text-primary"></i></h3>
          <a href="<?= e(url_with_action('admin.php', 'products')) ?>" class="btn btn-sm btn-outline-primary mt-2">管理产品</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 border-0 shadow-sm bg-light">
        <div class="card-body text-center">
          <div class="text-muted mb-2">订单管理</div>
          <h3><i class="fas fa-file-invoice text-success"></i></h3>
          <a href="<?= e(url_with_action('admin.php', 'orders')) ?>" class="btn btn-sm btn-outline-success mt-2">查看订单</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 border-0 shadow-sm bg-light">
        <div class="card-body text-center">
          <div class="text-muted mb-2">服务管理</div>
          <h3><i class="fas fa-server text-info"></i></h3>
          <a href="<?= e(url_with_action('admin.php', 'services')) ?>" class="btn btn-sm btn-outline-info mt-2">交付服务</a>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 border-0 shadow-sm bg-light">
        <div class="card-body text-center">
          <div class="text-muted mb-2">用户管理</div>
          <h3><i class="fas fa-users text-warning"></i></h3>
          <a href="<?= e(url_with_action('admin.php', 'users')) ?>" class="btn btn-sm btn-outline-warning mt-2">用户/余额</a>
        </div>
      </div>
    </div>
  </div>
</div>
