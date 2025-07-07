<?php

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';

class GroupAnnouncementsController extends Controller
{
    private const BASE_PATH = '\\\\192.168.2.61\\共和國公告區';

    /**
     * 建構函式
     * 
     * 初始化控制器並設定全域視圖資料
     */
    public function __construct()
    {
        $this->setGlobalViewData();
    }

    /**
     * 顯示共享檔案頁面
     * 
     * 使用我們最終確認的 exec('dir') 方案來讀取網路共享資料夾，
     * 該方案能夠正確處理包含特殊中文字元的路徑。
     */
    public function sharedFiles()
    {
        // 基礎路徑（UTF-8），使用 Windows 反斜線
        $base_unc_path = self::BASE_PATH;
        $windows_charset = 'CP950'; // Windows 命令提示字元的目標編碼

        // 從查詢字串中取得並清理相對路徑
        $relative_path_utf8 = '';
        if (isset($_GET['path'])) {
            $relative_path_utf8 = urldecode($_GET['path']);
            // 安全性：防止目錄遍歷攻擊
            $relative_path_utf8 = str_replace('..', '', $relative_path_utf8);
            // 將路徑分隔符號統一為 Windows 的反斜線
            $relative_path_utf8 = trim(str_replace('/', '\\', $relative_path_utf8), '\\');
        }

        // 組合出完整的 UTF-8 路徑
        $full_path_utf8 = $base_unc_path . (empty($relative_path_utf8) ? '' : '\\' . $relative_path_utf8);

        // 將完整路徑轉換為 Windows 命令提示字元可讀的編碼
        $full_path_win = iconv('UTF-8', $windows_charset . '//IGNORE', $full_path_utf8);
        if ($full_path_win === false) {
             return $this->view('announcements/index', ['error' => '路徑編碼轉換失敗 (Code: E02)。']);
        }

        // 建立並執行 'dir' 指令
        $command = 'dir "' . $full_path_win . '"';
        $output = [];
        $return_var = -1;
        exec($command . ' 2>&1', $output, $return_var);

        $directories = [];
        $files = [];
        $error = null;

        if ($return_var !== 0) {
            // 指令執行出錯
            $error_message = '指定的路徑不存在或無法存取 (Code: F01)。';
            if (isset($output[0])) {
                $error_message .= ' 錯誤詳情: ' . htmlspecialchars(iconv($windows_charset, 'UTF-8//IGNORE', $output[0]));
            }
            $error = $error_message;
        } else {
            // 解析 'dir' 指令的輸出
            foreach ($output as $line) {
                $line_utf8 = iconv($windows_charset, 'UTF-8//IGNORE', $line);

                // 跳過非檔案/目錄資訊的行
                if (!preg_match('/^\d{4}\/\d{2}\/\d{2}/', $line_utf8)) {
                    continue;
                }

                if (strpos($line_utf8, '<DIR>') !== false) {
                    // 處理目錄
                    $name = trim(substr($line_utf8, strpos($line_utf8, '<DIR>') + 5));
                    if ($name !== '.' && $name !== '..') {
                        $directories[] = [
                            'name' => $name,
                            'path' => str_replace('\\', '/', ltrim($relative_path_utf8 . '\\' . $name, '\\'))
                        ];
                    }
                } else {
                    // 處理檔案
                    if (preg_match('/\d{2}:\d{2}\s+(.*)$/', $line_utf8, $matches)) {
                        $file_info = trim($matches[1]);
                         if (preg_match('/^([\d,]+)\s+(.+)/s', $file_info, $file_matches)) {
                            $files[] = [
                                'name' => $file_matches[2],
                                'size' => str_replace(',', '', $file_matches[1]),
                                'path' => str_replace('\\', '/', ltrim($relative_path_utf8 . '\\' . $file_matches[2], '\\')),
                                'type' => strtolower(pathinfo($file_matches[2], PATHINFO_EXTENSION))
                            ];
                        }
                    }
                }
            }
        }
        
        // 準備麵包屑導航
        $breadcrumbs = [['name' => '集團公告', 'path' => '']];
        if (!empty($relative_path_utf8)) {
            $parts = explode('\\', $relative_path_utf8);
            $current_path_for_breadcrumb = '';
            foreach ($parts as $part) {
                $current_path_for_breadcrumb .= ($current_path_for_breadcrumb ? '/' : '') . $part;
                $breadcrumbs[] = ['name' => $part, 'path' => $current_path_for_breadcrumb];
            }
        }
        
        $current_path_for_view = str_replace('\\', '/', $relative_path_utf8);

        return $this->view('announcements/shared-files', [
            'directories' => $directories,
            'files' => $files,
            'breadcrumbs' => $breadcrumbs,
            'current_path' => $current_path_for_view,
            'error' => $error
        ]);
    }

    /**
     * 處理檔案下載或瀏覽請求
     * 徹底改用 Windows CMD 指令來處理，繞過 PHP fs 函式的編碼問題
     */
    public function download()
    {
        $windows_charset = 'CP950';
        
        // 1. 標準化路徑，確保所有分隔符都是 Windows 的 '\'
        $file_path_from_get = $_GET['path'] ?? '';
        $normalized_path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file_path_from_get);
        
        $base_path_win = iconv('UTF-8', $windows_charset . '//IGNORE', self::BASE_PATH);
        $full_path_utf8 = self::BASE_PATH . DIRECTORY_SEPARATOR . $normalized_path;
        $file_path_win = iconv('UTF-8', $windows_charset . '//IGNORE', $full_path_utf8);

        // 使用 'dir' 來檢查檔案是否存在
        $command_check = 'dir ' . escapeshellarg($file_path_win);
        $output_check = [];
        exec($command_check . ' 2>&1', $output_check, $return_var_check);

        $file_found = false;
        $file_size = 0;

        if ($return_var_check === 0) {
            foreach ($output_check as $line) {
                // 2. 使用一個更寬鬆的正規表示式，不再依賴於完整的時間格式
                //    它只尋找 日期/時間 之後，檔名之前，那個靠右對齊的數字（檔案大小）
                if (preg_match('/^\d{4}\/\d{2}\/\d{2}.*?\s+([\d,]+)\s+/', $line, $matches)) {
                    $file_size_str = trim($matches[1]);
                    if (is_numeric(str_replace(',', '', $file_size_str))) {
                        $file_size = (int)str_replace(',', '', $file_size_str);
                        $file_found = true;
                        break; 
                    }
                }
            }
        }
        
        if (!$file_found) {
            http_response_code(404);
            die('錯誤：檔案不存在或無法讀取 (Code: F02)。');
        }

        // 清除任何可能的輸出緩衝
        if (ob_get_level()) { ob_end_clean(); }

        $original_file_name = basename($full_path_utf8);
        
        $ext = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
        $mime_map = [
            'pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
            'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint', 'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip' => 'application/zip', 'rar' => 'application/x-rar-compressed',
        ];
        $mime_type = $mime_map[$ext] ?? 'application/octet-stream';
        
        // 確保 PDF 和圖片等檔案是在新分頁瀏覽，而不是直接下載
        $disposition = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']) ? 'inline' : 'attachment';

        // 設定 HTTP 標頭
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($original_file_name) . '"');
        if ($file_size > 0) { header('Content-Length: ' . $file_size); }
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // --- 核心修改：使用 passthru('type') 輸出檔案 ---
        $command_read = 'type ' . escapeshellarg($file_path_win);
        passthru($command_read, $return_var_read);
        exit;
    }
} 