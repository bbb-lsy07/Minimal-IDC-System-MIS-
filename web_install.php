<?php
// web_install.php - 简单的中文 Web 安装向导
error_reporting(E_ALL);
ini_set('display_errors', '1');

$configFile = __DIR__ . '/config.local.php';
$sqlFile = __DIR__ . '/sql/schema.sql';

// 如果配置文件已存在，禁止访问安装程序
if (file_exists($configFile)) {
    die('<div style="text-align:center;margin-top:50px;font-family:sans-serif;">
            <h1>系统已安装</h1>
            <p>检测到 config.local.php 已存在。</p>
            <p>为了安全，请删除 web_install.php 或手动修改配置。</p>
            <a href="index.php">前往首页</a>
         </div>');
}

$message = '';
$status = 'primary';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['db_host'] ?? '127.0.0.1';
    $name = $_POST['db_name'] ?? 'mis';
    $user = $_POST['db_user'] ?? 'root';
    $pass = $_POST['db_pass'] ?? '';
    $port = $_POST['db_port'] ?? '3306';
    
    $adminEmail = $_POST['admin_email'] ?? 'admin@example.com';
    $adminPass = $_POST['admin_pass'] ?? 'admin123';
    $baseUrl = rtrim($_POST['base_url'] ?? 'http://'.$_SERVER['HTTP_HOST'], '/');

    try {
        // 1. 测试数据库连接
        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        
        // 2. 创建数据库
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$name`");
        
        // 3. 导入 SQL
        if (!file_exists($sqlFile)) {
            throw new Exception("找不到 sql/schema.sql 文件");
        }
        $sqlContent = file_get_contents($sqlFile);
        // 简单的 SQL 分割 (实际生产环境建议用更健壮的解析)
        $queries = explode(';', $sqlContent);
        foreach ($queries as $query) {
            if (trim($query) !== '') {
                $pdo->exec($query);
            }
        }
        
        // 4. 创建管理员账号
        $hash = password_hash($adminPass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, balance, status, is_admin) VALUES (?, ?, 9999, 'active', 1) ON DUPLICATE KEY UPDATE password_hash = ?");
        $stmt->execute([$adminEmail, $hash, $hash]);
        
        // 5. 生成配置文件
        $appKey = bin2hex(random_bytes(32));
        $configContent = "<?php
return [
    'db' => [
        'dsn' => 'mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4',
        'user' => '$user',
        'pass' => '$pass',
    ],
    'app_key' => '$appKey',
    'base_url' => '$baseUrl',
    'timezone' => 'Asia/Shanghai',
];
";
        if (file_put_contents($configFile, $configContent) === false) {
            throw new Exception("无法写入 config.local.php，请检查目录权限。");
        }
        
        $message = "安装成功！请删除 web_install.php 文件以确保安全。";
        $status = 'success';
        
    } catch (Exception $e) {
        $message = "安装失败：" . $e->getMessage();
        $status = 'danger';
    }
}
?><!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MIS 系统安装</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">MIS 系统一键安装</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($status === 'success'): ?>
                            <div class="alert alert-success text-center">
                                <h4><i class="fas fa-check-circle"></i> <?= $message ?></h4>
                                <div class="mt-4">
                                    <a href="index.php" class="btn btn-primary w-100 mb-2">访问前台</a>
                                    <a href="admin.php" class="btn btn-secondary w-100">访问后台</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if ($message): ?>
                                <div class="alert alert-<?= $status ?>"><?= $message ?></div>
                            <?php endif; ?>
                            
                            <form method="post">
                                <h5 class="mb-3 border-bottom pb-2">数据库配置</h5>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-8">
                                        <label class="form-label">数据库地址</label>
                                        <input type="text" name="db_host" class="form-control" value="127.0.0.1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">端口</label>
                                        <input type="text" name="db_port" class="form-control" value="3306" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">数据库名</label>
                                    <input type="text" name="db_name" class="form-control" value="mis" required>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">数据库用户</label>
                                        <input type="text" name="db_user" class="form-control" value="root" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">数据库密码</label>
                                        <input type="password" name="db_pass" class="form-control">
                                    </div>
                                </div>

                                <h5 class="mb-3 border-bottom pb-2 mt-4">管理员配置</h5>
                                <div class="mb-3">
                                    <label class="form-label">管理员邮箱</label>
                                    <input type="email" name="admin_email" class="form-control" value="admin@example.com" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">管理员密码</label>
                                    <input type="text" name="admin_pass" class="form-control" value="admin123" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">站点 URL</label>
                                    <input type="text" name="base_url" class="form-control" value="http://<?= $_SERVER['HTTP_HOST'] ?>" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 btn-lg">开始安装</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-center mt-3 text-muted">
                    <small>Minimal IDC System Installer</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>