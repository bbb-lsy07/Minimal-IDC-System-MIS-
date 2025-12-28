<div class="row">
    <!-- 用户信息卡片 -->
    <div class="col-md-12 mb-4">
        <div class="mis-card">
            <h2><i class="fas fa-tachometer-alt text-primary me-2"></i>控制台概览</h2>
            <div class="row g-4 text-center">
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light">
                        <div class="text-muted mb-1">账户余额</div>
                        <h3 class="text-success fw-bold">￥<?= e($user['balance']) ?></h3>
                        <a href="#" class="btn btn-sm btn-outline-success mt-2">充值 (联系管理员)</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light">
                        <div class="text-muted mb-1">我的服务</div>
                        <h3 class="text-primary fw-bold"><?= e($serviceCount) ?></h3>
                        <a href="<?= e(url_with_action('index.php', 'services')) ?>" class="btn btn-sm btn-outline-primary mt-2">查看服务</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light">
                        <div class="text-muted mb-1">订单总数</div>
                        <h3 class="text-dark fw-bold"><?= e($orderCount) ?></h3>
                        <a href="<?= e(url_with_action('index.php', 'orders')) ?>" class="btn btn-sm btn-outline-secondary mt-2">查看订单</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 border rounded bg-light">
                        <div class="text-muted mb-1">账号状态</div>
                        <h3 class="text-info fw-bold">正常</h3>
                        <span class="badge bg-success mt-2">已激活</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 快捷入口 -->
    <div class="col-md-12">
        <div class="mis-card">
            <h4><i class="fas fa-bolt text-warning me-2"></i>快速开始</h4>
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm bg-light">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-cloud text-primary"></i> 购买云产品</h5>
                            <p class="card-text text-muted small">浏览我们的高性能服务器，即刻开通使用。</p>
                            <a href="<?= e(url_with_action('index.php', 'products')) ?>" class="btn btn-primary btn-sm">前往选购</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100 border-0 shadow-sm bg-light">
                        <div class="card-body">
                            <h5 class="card-title"><i class="fas fa-headset text-info"></i> 技术支持</h5>
                            <p class="card-text text-muted small">遇到问题？我们的技术团队随时为您服务。</p>
                            <a href="#" class="btn btn-info text-white btn-sm">提交工单</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
