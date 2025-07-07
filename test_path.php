<?php
// test_path.php

// Set the encoding for the browser output
header('Content-Type: text/html; charset=utf-8');

echo '<h1>網路路徑 (UNC Path) 存取測試</h1>';

// The path we want to test (UTF-8 encoded)
$utf8_path = '//192.168.2.61/共和國公告區';

// The character set for your Windows system (Traditional Chinese)
$windows_charset = 'BIG5';

echo '<p><b>測試目標路徑 (UTF-8):</b><br><code>' . htmlspecialchars($utf8_path) . '</code></p>';
echo '<p><b>嘗試轉換為 Windows 編碼:</b><br><code>' . $windows_charset . '</code></p>';

// Check if iconv function exists
if (!function_exists('iconv')) {
    echo '<p style="color: red; font-weight: bold;">錯誤: iconv() 函式不存在或未啟用。這是進行編碼轉換的必要功能。</p>';
    exit;
}

// Convert the path from UTF-8 to the Windows charset
$win_path = iconv('UTF-8', $windows_charset, $utf8_path);
if ($win_path === false) {
    echo '<p style="color: red; font-weight: bold;">錯誤: iconv() 函式轉換路徑字串失敗。</p>';
    exit;
}

echo '<hr>';

echo '<h2>測試結果</h2>';

// Use @ to suppress warnings, we will check the return value manually
echo '<h3>1. 測試 is_dir() - 路徑是否為有效資料夾:</h3>';
// Clear file status cache
clearstatcache();
if (is_dir($win_path)) {
    echo '<p style="color: green; font-weight: bold;">✅ 成功！ is_dir() 回傳 true。PHP 認為這是一個有效且可存取的資料夾。</p>';
} else {
    echo '<p style="color: red; font-weight: bold;">❌ 失敗！ is_dir() 回傳 false。PHP 認為這不是一個資料夾，或無法存取。</p>';
    echo '<p><b>這是最常見的失敗點。</b> 通常意味著以下三種可能之一：</p>';
    echo '<ol><li>路徑字串不正確。</li><li>編碼轉換錯誤 (例如系統不是 CP950)。</li><li>權限問題：執行 Apache 的帳號沒有權限存取此網路路徑。</li></ol>';
}

echo '<h3>2. 測試 scandir() - 是否能讀取資料夾內容:</h3>';
// Use @ to suppress warnings
$files = @scandir($win_path);

if ($files !== false) {
    echo '<p style="color: green; font-weight: bold;">✅ 成功！ scandir() 成功讀取到目錄內容。</p>';
    echo '<h4>偵測到的檔案/資料夾清單:</h4>';
    echo '<pre style="background-color: #f0f0f0; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">';
    // Convert filenames back to UTF-8 for display
    foreach($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo htmlspecialchars(iconv($windows_charset, 'UTF-8', $file)) . "\n";
        }
    }
    echo '</pre>';
} else {
    echo '<p style="color: red; font-weight: bold;">❌ 失敗！ scandir() 回傳 false。無法讀取此目錄的內容。</p>';
    $error = error_get_last();
    if($error) {
       echo "<p>系統最後的錯誤訊息: " . htmlspecialchars($error['message']) . "</p>";
    }
}

?> 