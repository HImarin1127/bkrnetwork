<?php
// public/dashboard.php
require_once __DIR__ . '/../src/auth.php';
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// 取得登入者姓名
$userName = $_SESSION['fullname'];

// 撈取符合使用者姓名的匯入紀錄
$db = db();
$stmt = $db->prepare("
    SELECT name, address, phone, x, split_account
    FROM contacts
    WHERE name = ?
    ORDER BY id DESC
");
$stmt->bind_param('s', $userName);
$stmt->execute();
$result = $stmt->get_result();
$records = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pageTitle = '匯入紀錄';
include __DIR__ . '/include/header.php';
?>

<div class="dashboard-layout">
  <!-- 側邊欄 -->
  <div class="sidebar">
    <div class="sidebar-header"><h3>員工內部網頁</h3></div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="nav-item active">
        <i class="fas fa-history"></i> 匯入紀錄
      </a>
      <a href="import.php" class="nav-item">
        <i class="fas fa-upload"></i> CSV 匯入
      </a>
      <a href="settings.php" class="nav-item">
        <i class="fas fa-cog"></i> 系統設定
      </a>
      <!-- 新增：新人到職 -->
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
    <div class="welcome-card">
      <h2>歡迎回來，<?= htmlspecialchars($userName) ?>！</h2>
      <p style="color:#6b7280;">今天是 <?= date('Y年n月j日') ?>，系統運行正常</p>
    </div>

    <div class="screen-header">
      <div class="screen-title">匯入紀錄</div>
      <div class="screen-subtitle"><?= htmlspecialchars($userName) ?> 的歷史紀錄</div>
    </div>
    <div class="screen-content">
      <?php if (empty($records)): ?>
        <div class="alert alert-info">目前尚無任何匯入紀錄。</div>
      <?php else: ?>
        <div class="data-table">
          <div class="table-header"><div class="table-title">匯入明細</div></div>
          <div class="table-content">
            <table>
              <thead>
                <tr>
                  <th>姓名</th>
                  <th>地址</th>
                  <th>電話</th>
                  <th>X</th>
                  <th>分帳</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($records as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td><?= htmlspecialchars($row['address']) ?></td>
                  <td><?= htmlspecialchars($row['phone'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['x'] ?? '') ?></td>
                  <td><?= htmlspecialchars($row['split_account'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/include/footer.php'; ?>