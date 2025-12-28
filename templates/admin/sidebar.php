<?php
/** @var string $__template */
$admin_actions = [
    ['action' => 'dashboard', 'label' => '管理概览', 'icon' => 'fas fa-tachometer-alt', 'match' => 'admin/dashboard.php'],
    ['action' => 'products', 'label' => '产品管理', 'icon' => 'fas fa-box', 'match' => 'product'],
    ['action' => 'services', 'label' => '服务管理', 'icon' => 'fas fa-server', 'match' => 'service'],
    ['action' => 'orders', 'label' => '订单管理', 'icon' => 'fas fa-file-invoice', 'match' => 'admin/orders_list.php'],
    ['action' => 'users', 'label' => '用户管理', 'icon' => 'fas fa-users', 'match' => 'user'],
];
?>
<div class="sidebar--navContent--15V3f">
    <div class="fb-sidebar-account-and-notifications">
        <div class="fb-circle-16" style="width:24px; height:24px; border-radius:50%;"></div>
        <div class="fw-bold" style="font-size: 14px;">MIS Admin</div>
    </div>
    
    <div class="fb-sidebar-searchbar">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" class="form-control border-start-0" placeholder="搜索...">
        </div>
    </div>

    <div class="sidebar--section--COZVK">
        <?php foreach ($admin_actions as $item): 
            $isActive = ($__template === $item['match'] || str_contains($__template, $item['match']));
        ?>
            <a href="<?= e(url_with_action('admin.php', $item['action'])) ?>" class="sidebar_row--sidebarRowContainer--LCEYg text-decoration-none">
                <div class="sidebar_row--sidebarRowRedesigned--sjKQW <?= $isActive ? 'sidebar_row--sidebarRowSelected--3D77W' : '' ?>">
                    <i class="<?= $item['icon'] ?> <?= $isActive ? '' : 'text-muted' ?>" style="width: 20px;"></i>
                    <div style="font-size: 14px;"><?= e($item['label']) ?></div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="sidebar--divider--sHsz4"></div>

    <div class="px-3 mt-4">
        <div class="base_upgrade_section--content--95XrT">
            <div class="base_upgrade_section--textContainer--ObHaO">
                <p class="fw-bold">系统版本 v1.0.0</p>
                <p>保持系统更新以获得最新功能和安全修复。</p>
            </div>
            <button class="btn btn-sm btn-primary mt-2 w-100">检查更新</button>
        </div>
    </div>
</div>
