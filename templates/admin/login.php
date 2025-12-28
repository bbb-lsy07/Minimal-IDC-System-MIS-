<?php
// Admin Login Template
?>
<div class="row justify-content-center my-5">
    <div class="col-md-5">
        <div class="mis-card shadow-lg p-4 border-top border-4 border-primary">
            <div class="text-center mb-4">
                <h2 class="border-0 mb-2" style="color: #3cb2b8;"><i class="fas fa-user-shield me-2"></i>管理后台登录</h2>
                <p class="text-muted">请输入管理员凭据以继续</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <?php foreach ($errors as $err): ?>
                        <div><i class="fas fa-exclamation-triangle me-2"></i><?= e($err) ?></div>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(url_with_action('admin.php', 'login')) ?>">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">管理员邮箱</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input class="form-control" name="email" type="email" placeholder="admin@example.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">登录密码</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input class="form-control" name="password" type="password" placeholder="请输入密码" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="fas fa-sign-in-alt me-2"></i>登录管理系统
                    </button>
                    <a class="btn btn-link btn-sm text-muted" href="<?= e(url_with_action('index.php', 'login')) ?>">
                        <i class="fas fa-arrow-left me-1"></i>返回用户主站
                    </a>
                </div>
            </form>

            <div class="mt-4 p-2 bg-light rounded border text-center">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i> 提示：管理员权限需在数据库中设置 <code>is_admin = 1</code>
                </small>
            </div>
        </div>
    </div>
</div>
