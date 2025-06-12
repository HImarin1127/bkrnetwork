<?php
// public/import.php
require_once __DIR__ . '/../src/auth.php';
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$success = '';
$errors = [];
$processedCount = 0;
$importedCount = 0;
$importedRows = [];

// 處理 CSV 上傳
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['csv_file']['tmp_name']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $fh = fopen($_FILES['csv_file']['tmp_name'], 'r');
        if ($fh !== false) {
            $db = db();
            $db->begin_transaction();
            $stmt = $db->prepare("INSERT INTO contacts (name,address,phone,x,split_account) VALUES (?,?,?,?,?)");
            while (($row = fgetcsv($fh)) !== false) {
                $processedCount++;
                if ($processedCount === 1 && strpos($row[0], "\xEF\xBB\xBF") === 0) {
                    // 去 BOM
                    $row[0] = substr($row[0], 3);
                }
                if ($row[0] === '姓名') continue; // 跳過標題列
                list($name, $address, $phone, $x, $split) = array_map('trim', $row + [null,null,null,null,null]);
                if ($name === '' || $address === '') continue;
                $stmt->bind_param('sssss', $name, $address, $phone, $x, $split);
                if ($stmt->execute()) $importedCount++;
            }
            $stmt->close();
            $db->commit();
            fclose($fh);
            if ($importedCount) {
                $success = "已處理 {$processedCount} 列，共匯入 {$importedCount} 筆資料。";
                $res = $db->query("SELECT * FROM contacts ORDER BY id DESC LIMIT {$importedCount}");
                $importedRows = $res->fetch_all(MYSQLI_ASSOC);
            } else {
                $errors[] = '未匯入任何資料，請檢查檔案內容。';
            }
        } else {
            $errors[] = '無法開啟檔案。';
        }
    } else {
        $errors[] = '請選擇 CSV 檔案並重新上傳。';
    }
}

$pageTitle = 'CSV 匯入';
include __DIR__ . '/include/header.php';
?>

<div class="dashboard-layout">
  <!-- 側邊欄 -->
  <div class="sidebar">
    <div class="sidebar-header"><h3>員工內部網頁</h3></div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="nav-item">
        <i class="fas fa-mail-bulk"></i> 郵資紀錄
      </a>
      <a href="import.php" class="nav-item active">
        <i class="fas fa-upload"></i> 郵資匯入
      </a>
      <a href="settings.php" class="nav-item">
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
      <div class="screen-title">CSV 匯入</div>
      <div class="screen-subtitle">批量匯入聯絡人資料</div>
    </div>
    <div class="screen-content">
      <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
      <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; ?>

      <form method="post" enctype="multipart/form-data">
        <div class="import-area">
          <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
          <h3 style="margin-bottom:15px;color:#374151;">拖拽或點擊選擇 CSV</h3>
          <input type="file" name="csv_file" class="file-input" accept=".csv" required>
          <button type="button" class="upload-btn">選擇檔案</button>
        </div>
        <button type="submit" class="btn btn-success">開始匯入</button>
      </form>

      <?php if ($importedRows): ?>
      <div class="data-table" style="margin-top:30px;">
        <div class="table-header"><div class="table-title">匯入結果</div></div>
        <div class="table-content">
          <table>
            <thead><tr><th>ID</th><th>姓名</th><th>地址</th><th>電話</th><th>X</th><th>分帳</th></tr></thead>
            <tbody>
            <?php foreach ($importedRows as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['x']) ?></td>
                <td><?= htmlspecialchars($row['split_account']) ?></td>
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