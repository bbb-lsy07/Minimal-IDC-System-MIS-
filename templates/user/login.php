<?php
require_once MIS_ROOT . '/includes/bootstrap.php'; // 确保 bootstrap.php 已包含
// 登录逻辑已在 UserController 中处理，这里只需渲染视图
?>
<div class="row justify-content-center my-5">
    <div class="col-md-5">
        <div class="mis-card shadow-lg p-4">
            <div class="text-center mb-4">
                <h2 class="border-0 mb-2" style="color: #3cb2b8;"><i class="fas fa-lock me-2"></i>用户登录</h2>
                <p class="text-muted">欢迎回来，请输入您的账号信息</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <?php foreach ($errors as $err): ?>
                        <div><i class="fas fa-exclamation-triangle me-2"></i><?= e($err) ?></div>
                    <?php endforeach; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(url_with_action('index.php', 'login')) ?>">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <div class="mb-3">
                    <label class="form-label fw-bold">电子邮箱</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input class="form-control" name="email" type="email" placeholder="name@example.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">密码</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input class="form-control" name="password" type="password" placeholder="请输入密码" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" type="submit">
                        <i class="fas fa-sign-in-alt me-2"></i>立即登录
                    </button>
                </div>

                <div class="text-center mt-3">
                    还没有账号？ <a href="<?= e(url_with_action('index.php', 'register')) ?>" class="text-primary fw-bold">立即注册</a>
                </div>
            </form>
        </div>
    </div>
</div>
