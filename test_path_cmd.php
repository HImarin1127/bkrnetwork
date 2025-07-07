<?php
// test_path_cmd.php

header('Content-Type: text/html; charset=utf-8');

echo '<h1>最終路徑存取測試 (透過 CMD.EXE)</h1>';

// The path in UTF-8
$utf8_path = '\\\\192.168.2.61\\共和國公告區';
$windows_charset = 'CP950';

// Convert the path for the command prompt
$win_path = iconv('UTF-8', $windows_charset, $utf8_path);

// Construct the command. Using double quotes around the path is crucial.
$command = 'dir "' . $win_path . '"';

echo '<p><b>將要執行的指令:</b><br><code>' . htmlspecialchars(iconv($windows_charset, 'UTF-8', $command)) . '</code></p>';
echo '<p><i>(註: 上面顯示的是轉換回 UTF-8 以便在瀏覽器中閱讀的指令，實際執行的是 CP950 編碼的版本)</i></p>';

echo '<hr>';

echo '<h2>測試結果</h2>';

// Execute the command
// Use 2>&1 to redirect stderr to stdout to capture any errors
$output = [];
$return_var = -1;
exec($command . ' 2>&1', $output, $return_var);

echo '<h3>1. 指令執行回傳代碼 (Return Code):</h3>';
if ($return_var === 0) {
    echo '<p style="color: green; font-weight: bold;">✅ 成功！ 指令回傳 0，表示成功執行。</p>';
} else {
    echo '<p style="color: red; font-weight: bold;">❌ 失敗！ 指令回傳 ' . $return_var . '，表示發生錯誤。</p>';
}

echo '<h3>2. 指令輸出內容:</h3>';
echo '<pre style="background-color: #f0f0f0; border: 1px solid #ccc; padding: 10px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word;">';
if (!empty($output)) {
    // The output from cmd.exe will be in CP950, convert it to UTF-8 for display
    foreach ($output as $line) {
        echo htmlspecialchars(iconv($windows_charset, 'UTF-8//IGNORE', $line)) . "\n";
    }
} else {
    echo '(無任何輸出)';
}
echo '</pre>';

?> 