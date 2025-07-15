<?php
/**
 * GroupAnnouncementsController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @link       https://www.brnetwork.com
 * @description 處理從 Windows 網路共享讀取集團公告的控制器。
 */
namespace App\Controllers;

require_once __DIR__ . '/Controller.php';

/**
 * Class GroupAnnouncementsController
 *
 * 這個控制器專門用於與 Windows 網路共享資料夾進行互動。
 * 由於 PHP 的標準檔案系統函式在處理帶有特殊字元 (如中文) 的 UNC 路徑時可能存在編碼問題，
 * 此控制器採用直接執行 Windows 命令列指令 (`dir`, `type`) 的方式來繞過這些限制，
 * 以確保能穩定地讀取目錄列表和檔案內容。
 *
 * @package App\Controllers
 */
class GroupAnnouncementsController extends Controller
{
    /** @var string Windows 共享資料夾的 UNC 路徑。 */
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
     * 顯示共享資料夾中的檔案和目錄列表。
     *
     * @return void
     */
    public function sharedFiles()
    {
        // 基礎路徑（UTF-8），使用 Windows 反斜線
        $base_unc_path = self::BASE_PATH;
        $windows_charset = 'CP950'; // Windows 命令提示字元的目標編碼（繁體中文）

        // 從查詢字串中取得並清理相對路徑
        $relative_path_utf8 = '';
        if (isset($_GET['path'])) {
            $relative_path_utf8 = urldecode($_GET['path']);
            // 安全性：防止目錄遍歷攻擊，移除 '..' 字元
            $relative_path_utf8 = str_replace('..', '', $relative_path_utf8);
            // 將 URL 的 '/' 分隔符號統一為 Windows 的反斜線
            $relative_path_utf8 = trim(str_replace('/', '\\', $relative_path_utf8), '\\');
        }

        // 組合出完整的 UTF-8 UNC 路徑
        $full_path_utf8 = $base_unc_path . (empty($relative_path_utf8) ? '' : '\\' . $relative_path_utf8);

        // 將 UTF-8 路徑轉換為 Windows 命令提示字元可讀的編碼 (CP950)
        // 這是因為 `exec` 執行的指令需要使用系統本地的編碼
        $full_path_win = iconv('UTF-8', $windows_charset . '//IGNORE', $full_path_utf8);
        if ($full_path_win === false) {
             // 如果路徑中有無法轉換的字元，則回傳錯誤
             return $this->view('announcements/index', ['error' => '路徑編碼轉換失敗 (Code: E02)。']);
        }

        // 建立並執行 'dir' 指令來列出目錄內容
        // 使用 "2>&1" 將標準錯誤（stderr）重導向到標準輸出（stdout），以便捕獲如 "找不到檔案" 之類的錯誤訊息
        $command = 'dir "' . $full_path_win . '"';
        $output = [];
        $return_var = -1;
        exec($command . ' 2>&1', $output, $return_var);

        $directories = [];
        $files = [];
        $error = null;

        if ($return_var !== 0) {
            // 指令執行出錯 (exit code 不為 0)
            $error_message = '指定的路徑不存在或無法存取 (Code: F01)。';
            if (isset($output[0])) {
                // 將從 CMD 收到的錯誤訊息 (CP950) 轉回 UTF-8 以便在網頁上正確顯示
                $error_message .= ' 錯誤詳情: ' . htmlspecialchars(iconv($windows_charset, 'UTF-8//IGNORE', $output[0]));
            }
            $error = $error_message;
        } else {
            // 解析 'dir' 指令的輸出
            foreach ($output as $line) {
                // 將每一行輸出從 CP950 轉回 UTF-8 進行處理
                $line_utf8 = iconv($windows_charset, 'UTF-8//IGNORE', $line);

                // dir 指令的檔案/目錄行通常以日期開頭，以此過濾掉標頭和摘要資訊
                if (!preg_match('/^\d{4}\/\d{2}\/\d{2}/', $line_utf8)) {
                    continue;
                }

                if (strpos($line_utf8, '<DIR>') !== false) {
                    // 處理目錄行
                    $name = trim(substr($line_utf8, strpos($line_utf8, '<DIR>') + 5));
                    // 過濾掉代表當前和上層目錄的 '.' 和 '..'
                    if ($name !== '.' && $name !== '..') {
                        $directories[] = [
                            'name' => $name,
                            'path' => str_replace('\\', '/', ltrim($relative_path_utf8 . '\\' . $name, '\\'))
                        ];
                    }
                } else {
                    // 處理檔案行
                    // 從 "HH:MM" 時間格式後開始擷取，取得包含檔案大小和檔名的部分
                    if (preg_match('/\d{2}:\d{2}\s+(.*)$/', $line_utf8, $matches)) {
                        $file_info = trim($matches[1]);
                        // 從中分離出檔案大小和檔名
                         if (preg_match('/^([\d,]+)\s+(.+)/s', $file_info, $file_matches)) {
                            $files[] = [
                                'name' => $file_matches[2],
                                'size' => str_replace(',', '', $file_matches[1]), // 移除大小中的千分位逗號
                                'path' => str_replace('\\', '/', ltrim($relative_path_utf8 . '\\' . $file_matches[2], '\\')),
                                'type' => strtolower(pathinfo($file_matches[2], PATHINFO_EXTENSION))
                            ];
                        }
                    }
                }
            }
        }
        
        // 準備麵包屑導航資料，方便使用者追蹤目前路徑
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
     * 處理檔案下載或瀏覽請求。
     * 
     * 透過 Windows CMD 的 `type` 指令來讀取檔案內容並輸出，以繞過 PHP 原生函式的編碼問題。
     *
     * @return void
     */
    public function download()
    {
        $windows_charset = 'CP950';
        
        // 從 GET 參數獲取檔案路徑
        $file_path_from_get = $_GET['path'] ?? '';
        // 標準化路徑，將 '/' 和 '\' 都統一為系統預設的分隔符
        $normalized_path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file_path_from_get);
        
        $base_path_win = iconv('UTF-8', $windows_charset . '//IGNORE', self::BASE_PATH);
        $full_path_utf8 = self::BASE_PATH . DIRECTORY_SEPARATOR . $normalized_path;
        // 將完整的 UTF-8 路徑轉換為 Windows CMD 可辨識的編碼
        $file_path_win = iconv('UTF-8', $windows_charset . '//IGNORE', $full_path_utf8);

        // 同樣使用 'dir' 指令來檢查檔案是否存在並順便獲取檔案大小
        $command_check = 'dir ' . escapeshellarg($file_path_win);
        $output_check = [];
        exec($command_check . ' 2>&1', $output_check, $return_var_check);

        $file_found = false;
        $file_size = 0;

        if ($return_var_check === 0) {
            foreach ($output_check as $line) {
                // 從 'dir' 的輸出中，透過正規表示式解析出檔案大小
                // 此處的 regex 較為寬鬆，以應對不同格式
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

        // 在輸出檔案內容前，清除任何可能已存在的輸出緩衝
        if (ob_get_level()) { ob_end_clean(); }

        $original_file_name = basename($full_path_utf8);
        
        $ext = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));
        // 建立一個副檔名與 MIME 類型的映射表
        $mime_map = [
            'pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif',
            'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint', 'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'zip' => 'application/zip', 'rar' => 'application/x-rar-compressed',
        ];
        $mime_type = $mime_map[$ext] ?? 'application/octet-stream';
        
        // 根據副檔名決定檔案是在瀏覽器內直接顯示 (inline) 還是作為附件下載 (attachment)
        $disposition = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']) ? 'inline' : 'attachment';

        // 設定 HTTP 標頭，告知瀏覽器如何處理這個檔案
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($original_file_name) . '"');
        if ($file_size > 0) { header('Content-Length: ' . $file_size); }
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // --- 核心技巧：使用 Windows 的 'type' 指令讀取檔案內容，並用 passthru() 直接輸出到瀏覽器 ---
        $command_read = 'type ' . escapeshellarg($file_path_win);
        passthru($command_read, $return_var_read);
        exit;
    }
} 