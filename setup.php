<?php
// setup.php - 讀書共和國員工服務網快速設定腳本

echo "🚀 讀書共和國員工服務網 MVC 架構設定\n";
echo "==========================================\n\n";

// 檢查 PHP 版本
if (version_compare(PHP_VERSION, '7.4.0') < 0) {
    echo "❌ 錯誤：需要 PHP 7.4.0 或更高版本，目前版本：" . PHP_VERSION . "\n";
    exit(1);
}

echo "✅ PHP 版本檢查通過：" . PHP_VERSION . "\n";

// 檢查必要擴展
$required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
$missing_extensions = [];

foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $missing_extensions[] = $ext;
    }
}

if (!empty($missing_extensions)) {
    echo "❌ 錯誤：缺少必要的 PHP 擴展：" . implode(', ', $missing_extensions) . "\n";
    exit(1);
}

echo "✅ PHP 擴展檢查通過\n";

// 檢查目錄權限
$directories = [
    'uploads',
    'app/Views',
    'config'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        echo "📁 建立目錄：$dir\n";
        mkdir($dir, 0755, true);
    }
    
    if (!is_writable($dir)) {
        echo "⚠️  警告：目錄 $dir 不可寫入，請檢查權限\n";
    }
}

echo "✅ 目錄權限檢查完成\n";

// 資料庫連接測試
echo "\n🔧 資料庫設定\n";
echo "==================\n";

$db_config = require __DIR__ . '/config/database.php';

try {
    $dsn = "mysql:host={$db_config['host']};charset={$db_config['charset']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $db_config['options']);
    
    echo "✅ 資料庫連接成功\n";
    
    // 檢查資料庫是否存在
    $stmt = $pdo->query("SHOW DATABASES LIKE '{$db_config['database']}'");
    if ($stmt->rowCount() == 0) {
        echo "📊 建立資料庫：{$db_config['database']}\n";
        $pdo->exec("CREATE DATABASE `{$db_config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
    
    // 連接到指定資料庫
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};charset={$db_config['charset']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $db_config['options']);
    
    // 檢查表格是否存在
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "📋 執行資料庫初始化腳本\n";
        $sql = file_get_contents(__DIR__ . '/database_setup.sql');
        $pdo->exec($sql);
        echo "✅ 資料庫表格建立完成\n";
    } else {
        echo "✅ 資料庫表格已存在\n";
    }
    
} catch (PDOException $e) {
    echo "❌ 資料庫連接失敗：" . $e->getMessage() . "\n";
    echo "請檢查 config/database.php 中的設定\n";
    exit(1);
}

// 設定檢查
echo "\n⚙️  系統設定檢查\n";
echo "==================\n";

$app_config = require __DIR__ . '/config/app.php';
echo "✅ 應用程式名稱：{$app_config['name']}\n";
echo "✅ 時區設定：{$app_config['timezone']}\n";

// 檢查重要檔案
$important_files = [
    'index.php',
    'app/Controllers/Controller.php',
    'app/Models/Database.php',
    'app/Middleware/AuthMiddleware.php',
    'routes/web.php'
];

foreach ($important_files as $file) {
    if (!file_exists($file)) {
        echo "❌ 錯誤：重要檔案不存在：$file\n";
        exit(1);
    }
}

echo "✅ 重要檔案檢查完成\n";

// 預設資料檢查
echo "\n👤 預設帳號資訊\n";
echo "==================\n";

try {
    require_once __DIR__ . '/app/Models/Database.php';
    require_once __DIR__ . '/app/Models/Model.php';
    require_once __DIR__ . '/app/Models/User.php';
    
    $userModel = new User();
    $adminUser = $userModel->findBy('username', 'admin');
    
    if ($adminUser) {
        echo "✅ 管理員帳號已建立\n";
        echo "   帳號：admin\n";
        echo "   密碼：password\n";
        echo "   角色：{$adminUser['role']}\n";
    } else {
        echo "⚠️  警告：找不到管理員帳號\n";
    }
    
} catch (Exception $e) {
    echo "⚠️  警告：無法檢查使用者資料：" . $e->getMessage() . "\n";
}

// 功能測試
echo "\n🧪 功能測試\n";
echo "==================\n";

// 測試路由
$routes = require __DIR__ . '/routes/web.php';
echo "✅ 已載入 " . count($routes) . " 個路由\n";

// 測試視圖
$layout_file = __DIR__ . '/app/Views/layouts/app.php';
if (file_exists($layout_file)) {
    echo "✅ 主要佈局檔案存在\n";
} else {
    echo "❌ 錯誤：找不到主要佈局檔案\n";
}

// 安全性檢查
echo "\n🔒 安全性檢查\n";
echo "==================\n";

if (file_exists('.htaccess')) {
    echo "✅ .htaccess 檔案存在\n";
} else {
    echo "⚠️  建議：建立 .htaccess 檔案以增強安全性\n";
    
    $htaccess_content = <<<'EOL'
# 隱藏敏感檔案
<Files "*.php">
    Order Deny,Allow
    Deny from all
</Files>

<Files "index.php">
    Order Allow,Deny
    Allow from all
</Files>

# 禁止訪問設定檔案
<FilesMatch "\.(sql|md|json)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# 啟用 URL 重寫（可選）
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=/$1 [QSA,L]
EOL;
    
    file_put_contents('.htaccess', $htaccess_content);
    echo "✅ 已建立基本 .htaccess 檔案\n";
}

// 完成
echo "\n🎉 設定完成！\n";
echo "=====================================\n";
echo "系統已準備就緒，您可以：\n\n";
echo "1. 在瀏覽器中訪問：http://localhost/bkrnetwork/\n";
echo "2. 使用管理員帳號登入：\n";
echo "   帳號：admin\n";
echo "   密碼：password\n";
echo "3. 查看使用指南：MVC_使用指南.md\n\n";
echo "🚀 開始使用讀書共和國員工服務網！\n";
?> 