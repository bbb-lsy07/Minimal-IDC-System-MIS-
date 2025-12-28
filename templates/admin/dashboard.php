<div class="mis-card border-0 bg-transparent shadow-none p-0">
  <div class="d-flex justify-content-between align-items-center mb-4 px-2">
    <h4 class="m-0 fw-bold">管理概览 / Management Overview</h4>
    <div class="d-flex gap-2">
      <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-list"></i></button>
      <button class="btn btn-sm btn-primary"><i class="fas fa-th-large"></i></button>
    </div>
  </div>
  
  <div class="row g-4">
    <!-- 产品管理 "File" Card -->
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card h-100 border-0 shadow-sm overflow-hidden">
        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
           <i class="fas fa-box fa-3x text-primary opacity-50"></i>
        </div>
        <div class="card-body p-3 border-top">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="card-title mb-1 fw-bold text-truncate" style="max-width: 150px;">产品管理</h6>
              <p class="text-muted small mb-0">Products</p>
            </div>
            <a href="<?= e(url_with_action('admin.php', 'products')) ?>" class="text-primary"><i class="fas fa-external-link-alt"></i></a>
          </div>
        </div>
      </div>
    </div>

    <!-- 订单管理 "File" Card -->
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card h-100 border-0 shadow-sm overflow-hidden">
        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
           <i class="fas fa-file-invoice fa-3x text-success opacity-50"></i>
        </div>
        <div class="card-body p-3 border-top">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="card-title mb-1 fw-bold text-truncate" style="max-width: 150px;">订单管理</h6>
              <p class="text-muted small mb-0">Orders</p>
            </div>
            <a href="<?= e(url_with_action('admin.php', 'orders')) ?>" class="text-success"><i class="fas fa-external-link-alt"></i></a>
          </div>
        </div>
      </div>
    </div>

    <!-- 服务管理 "File" Card -->
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card h-100 border-0 shadow-sm overflow-hidden">
        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
           <i class="fas fa-server fa-3x text-info opacity-50"></i>
        </div>
        <div class="card-body p-3 border-top">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="card-title mb-1 fw-bold text-truncate" style="max-width: 150px;">服务管理</h6>
              <p class="text-muted small mb-0">Services</p>
            </div>
            <a href="<?= e(url_with_action('admin.php', 'services')) ?>" class="text-info"><i class="fas fa-external-link-alt"></i></a>
          </div>
        </div>
      </div>
    </div>

    <!-- 用户管理 "File" Card -->
    <div class="col-6 col-md-4 col-lg-3">
      <div class="card h-100 border-0 shadow-sm overflow-hidden">
        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
           <i class="fas fa-users fa-3x text-warning opacity-50"></i>
        </div>
        <div class="card-body p-3 border-top">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="card-title mb-1 fw-bold text-truncate" style="max-width: 150px;">用户管理</h6>
              <p class="text-muted small mb-0">Users</p>
            </div>
            <a href="<?= e(url_with_action('admin.php', 'users')) ?>" class="text-warning"><i class="fas fa-external-link-alt"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-5">
    <div class="col-12">
      <div class="mis-card">
         <h5 class="fw-bold mb-4"><i class="fas fa-chart-line me-2 text-primary"></i>系统状态 / System Status</h5>
         <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="border rounded p-3">
                    <div class="text-muted small mb-1">PHP 版本</div>
                    <div class="fw-bold"><?= PHP_VERSION ?></div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="border rounded p-3">
                    <div class="text-muted small mb-1">服务器时间</div>
                    <div class="fw-bold"><?= date('Y-m-d H:i:s') ?></div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="border rounded p-3">
                    <div class="text-muted small mb-1">数据库状态</div>
                    <div class="fw-bold text-success">正常连接</div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="border rounded p-3">
                    <div class="text-muted small mb-1">安全模式</div>
                    <div class="fw-bold <?= ini_get('safe_mode') ? 'text-danger' : 'text-success' ?>">
                        <?= ini_get('safe_mode') ? '已开启' : '已关闭' ?>
                    </div>
                </div>
            </div>
         </div>
      </div>
    </div>
  </div>
</div>
