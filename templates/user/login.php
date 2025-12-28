<div class="row justify-content-center" style="margin-top: 50px;">
    <div class="col-md-5">
        <div class="mis-card">
            <div class="text-center mb-4">
                <h2 class="border-0">用户登录</h2>
                <p class="text-muted">欢迎回来，请输入您的账号信息</p>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $err): ?>
                        <div><?= e($err) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= e(url_with_action('index.php', 'login')) ?>">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <div class="mb-3">
                    <label class="form-label">电子邮箱</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input class="form-control" name="email" type="email" placeholder="name@example.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">密码</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input class="form-control" name="password" type="password" placeholder="请输入密码" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" type="submit">立即登录</button>
                </div>

                <div class="text-center mt-3">
                    还没有账号？ <a href="<?= e(url_with_action('index.php', 'register')) ?>">立即注册</a>
                </div>
            </form>
        </div>
    </div>
</div>
