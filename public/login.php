<?php
// public/login.php
require_once __DIR__ . '/../src/auth.php';

// 如已登入，直接導向首頁
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errorLogin = '';
$errorRegister = '';
// 處理登入或註冊表單提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 判斷提交的是註冊還是登入（透過是否有 fullname 欄位）
    if (isset($_POST['fullname'])) {
        // 註冊表單提交
        $res = register($_POST['username'] ?? '', $_POST['password'] ?? '', $_POST['fullname'] ?? '');
        if ($res['success']) {
            // 註冊成功後，跳轉回本頁並提示成功訊息
            header('Location: login.php?registered=1');
            exit;
        } else {
            // 註冊失敗，記錄錯誤訊息
            $errorRegister = $res['message'];
        }
    } else {
        // 登入表單提交
        if (login($_POST['username'] ?? '', $_POST['password'] ?? '')) {
            // 登入成功，跳轉至系統主頁或儀表板
            header('Location: dashboard.php');
            exit;
        } else {
            // 登入失敗，記錄錯誤訊息
            $errorLogin = '帳號或密碼錯誤';
        }
    }
}

$pageTitle = '登入 / 註冊';
include __DIR__ . '/include/header.php';
?>
<!-- 頁面主要內容：登入/註冊表單 -->
<div class="wireframe-header">
    <h1>BKR管理系統</h1>
</div>
<div class="login-container">
    <!-- 登入表單區塊 -->
    <div id="login" class="wireframe-screen <?= ($errorRegister || isset($_GET['register'])) ? '' : 'active' ?>">
        <div class="login-form">
            <h2 style="margin-bottom: 30px; color: #374151;">歡迎回來</h2>
            <?php if (!empty($_GET['registered'])): ?>
                <div class="alert alert-success">註冊成功，請使用帳號密碼登入。</div>
            <?php endif; ?>
            <?php if ($errorLogin): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorLogin) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label class="form-label">帳號</label>
                    <input type="text" name="username" class="form-input" placeholder="請輸入帳號" required />
                </div>
                <div class="form-group">
                    <label class="form-label">密碼</label>
                    <input type="password" name="password" class="form-input" placeholder="請輸入密碼" required />
                </div>
                <button type="submit" class="btn">登入</button>
                <button type="button" class="btn btn-secondary" onclick="toggleAuth(true)">建立新帳號</button>
            </form>
        </div>
    </div>
    <!-- 註冊表單區塊 -->
    <div id="register" class="wireframe-screen <?= ($errorRegister || isset($_GET['register'])) ? 'active' : '' ?>">
        <div class="login-form">
            <h2 style="margin-bottom: 30px; color: #374151;">建立帳號</h2>
            <?php if ($errorRegister): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errorRegister) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label class="form-label">全名</label>
                    <input type="text" name="fullname" class="form-input" placeholder="請輸入全名" required />
                </div>
                <div class="form-group">
                    <label class="form-label">帳號</label>
                    <input type="text" name="username" class="form-input" placeholder="請輸入帳號" required />
                </div>
                <div class="form-group">
                    <label class="form-label">密碼</label>
                    <input type="password" name="password" class="form-input" placeholder="請輸入密碼" required />
                </div>
                <button type="submit" class="btn btn-success">註冊</button>
                <button type="button" class="btn btn-secondary" onclick="toggleAuth(false)">已有帳號？請登入</button>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/include/footer.php'; ?>