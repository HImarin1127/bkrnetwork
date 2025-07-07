<?php
// test_path_final.php

header('Content-Type: text/html; charset=utf-8');

echo '<h1>最終路徑存取測試 (使用原生 Backslash)</h1>';

// Path with native Windows backslashes, as a UTF-8 string
$utf8_path = '\\\\192.168.2.61\\共和國公告區';

$windows_charset = 'CP950';

echo '<p><b>測試目標路徑 (UTF-8, Windows Style):</b><br><code>' . htmlspecialchars($utf8_path) . '</code></p>';

// Convert the path from UTF-8 to the Windows charset
$win_path = iconv('UTF-8', $windows_charset, $utf8_path);

echo '<hr>';

echo '<h2>測試結果</h2>';

echo '<h3>1. 測試 is_dir() - 路徑是否為有效資料夾:</h3>';
clearstatcache();
if (@is_dir($win_path)) {
    echo '<p style="color: green; font-weight: bold;">✅ 成功！ is_dir() 回傳 true。</p>';
} else {
    echo '<p style="color: red; font-weight: bold;">❌ 失敗！ is_dir() 回傳 false。</p>';
    $error = error_get_last();
    if($error) {
       echo "<p>系統最後的錯誤訊息: " . htmlspecialchars($error['message']) . "</p>";
    }
}

echo '<h3>2. 測試 scandir() - 是否能讀取資料夾內容:</h3>';
$files = @scandir($win_path);

if ($files !== false) {
    echo '<p style="color: green; font-weight: bold;">✅ 成功！ scandir() 成功讀取到目錄內容。</p>';
    echo '<h4>偵測到的檔案/資料夾清單:</h4>';
    echo '<pre style="background-color: #f0f0f0; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">';
    foreach($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo htmlspecialchars(iconv($windows_charset, 'UTF-8', $file)) . "\n";
        }
    }
    echo '</pre>';
} else {
    echo '<p style="color: red; font-weight: bold;">❌ 失敗！ scandir() 回傳 false。</p>';
    $error = error_get_last();
    if($error) {
       echo "<p>系統最後的錯誤訊息: " . htmlspecialchars($error['message']) . "</p>";
    }
}

?> 