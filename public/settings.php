<?php
// public/settings.php
require_once __DIR__ . '/../src/auth.php';
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = '';

// 處理密碼更新表單
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old      = $_POST['old_password']     ?? '';
    $new      = $_POST['new_password']     ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    // 1. 檢查新密碼與確認密碼是否一致
    if ($new !== $confirm) {
        $errors[] = '新密碼與確認密碼不相同';
    } else {
        // 2. 從 DB 撈取目前密碼雜湊
        $db = db();
        $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
        $stmt->bind_result($hash);
        if ($stmt->fetch()) {
            // 3. 驗證舊密碼
            if (password_verify($old, $hash)) {
                $stmt->close();
                // 4. 產生新雜湊並更新
                $newHash = password_hash($new, PASSWORD_DEFAULT);
                $upd = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $upd->bind_param('si', $newHash, $_SESSION['user_id']);
                if ($upd->execute()) {
                    $success = '密碼已成功更新';
                } else {
                    $errors[] = '更新失敗，請稍後再試';
                }
                $upd->close();
            } else {
                $errors[] = '舊密碼錯誤';
                $stmt->close();
            }
        } else {
            $errors[] = '找不到使用者資料';
            $stmt->close();
        }
    }
}

$pageTitle = '系統設定';
include __DIR__ . '/include/header.php';
?>
<div class="dashboard-layout">
  <!-- 側邊欄 -->
  <div class="sidebar">
    <div class="sidebar-header"><h3>員工內部網頁</h3></div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="nav-item">
        <i class="fas fa-history"></i> 匯入紀錄
      </a>
      <a href="import.php" class="nav-item">
        <i class="fas fa-upload"></i> CSV 匯入
      </a>
      <a href="settings.php" class="nav-item active">
        <i class="fas fa-cog"></i> 系統設定
      </a>
      <a href="onboarding.php" class="nav-item">
        <i class="fas fa-user-plus"></i> 新人到職
      </a>
      <a href="logout.php" class="nav-item">
        <i class="fas fa-sign-out-alt"></i> 登出
      </a>
    </nav>
  </div>

  <!-- 主內容 -->
  <div class="main-content">
    <div class="screen-header">
      <div class="screen-title">系統設定</div>
      <div class="screen-subtitle">修改帳號密碼</div>
    </div>
    <div class="screen-content">
      <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
      <?php foreach ($errors as $e): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
      <?php endforeach; ?>

      <form method="post">
        <div class="form-group">
          <label class="form-label">舊密碼</label>
          <input type="password" name="old_password" class="form-input" required>
        </div>
        <div class="form-group">
          <label class="form-label">新密碼</label>
          <input type="password" name="new_password" class="form-input" required>
        </div>
        <div class="form-group">
          <label class="form-label">確認新密碼</label>
          <input type="password" name="confirm_password" class="form-input" required>
        </div>
        <button type="submit" class="btn btn-success">更新密碼</button>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/include/footer.php'; ?>
