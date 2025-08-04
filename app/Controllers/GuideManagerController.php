<?php
/**
 * GuideManagerController.php
 *
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 * @description 處理操作指引後台管理的控制器（無資料庫）。
 */

namespace App\Controllers;

require_once __DIR__ . '/Controller.php';

class GuideManagerController extends Controller
{
    /** @var string 指引檔案的根儲存路徑。 */
    private const GUIDES_BASE_PATH = BASE_PATH . 'uploads/guides/';

    /**
     * 建構函式
     */
    public function __construct()
    {
        $this->setGlobalViewData();
        // 確保根目錄存在
        if (!is_dir(self::GUIDES_BASE_PATH)) {
            mkdir(self::GUIDES_BASE_PATH, 0777, true);
        }
    }

    /**
     * 顯示後台管理主頁面。
     *
     * @return void
     */
    public function index()
    {
        $categories = [];
        $items = array_diff(scandir(self::GUIDES_BASE_PATH), ['.', '..']);

        foreach ($items as $item) {
            $category_path = self::GUIDES_BASE_PATH . $item;
            if (is_dir($category_path)) {
                $files = [];
                $file_items = array_diff(scandir($category_path), ['.', '..']);
                foreach ($file_items as $file_item) {
                    $file_path = $category_path . '/' . $file_item;
                    if (is_file($file_path)) {
                        $files[] = [
                            'name' => $file_item,
                            'size' => filesize($file_path),
                        ];
                    }
                }
                $categories[] = [
                    'name' => $item,
                    'files' => $files
                ];
            }
        }
        
        return $this->view('admin/guides_manager', [
            'pageTitle' => '操作指引管理',
            'categories' => $categories
        ]);
    }

    /**
     * 建立新的分類（資料夾）。
     *
     * @return void
     */
    public function createCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category_name'])) {
            $new_category_name = trim($_POST['category_name']);
            // 進行基本的名稱清理，防止不合法的資料夾名稱
            $new_category_name = preg_replace('/[^A-Za-z0-9\x{4e00}-\x{9fa5}_-]/u', '', $new_category_name);

            if (!empty($new_category_name)) {
                $new_path = self::GUIDES_BASE_PATH . $new_category_name;
                if (!is_dir($new_path)) {
                    mkdir($new_path, 0777, true);
                    $_SESSION['flash_message'] = ['type' => 'success', 'message' => '分類已成功建立！'];
                } else {
                    $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：同名的分類已存在。'];
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：分類名稱不能為空。'];
            }
        }
        $this->redirect($this->viewData['baseUrl'] . '/admin/guides-manager');
    }

    /**
     * 上傳檔案到指定的分類。
     *
     * @return void
     */
    public function uploadFile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category']) && isset($_FILES['guide_file']) && $_FILES['guide_file']['error'] === UPLOAD_ERR_OK) {
            $category_name = $_POST['category'];
            $category_path = self::GUIDES_BASE_PATH . $category_name;

            if (is_dir($category_path)) {
                $file_tmp_path = $_FILES['guide_file']['tmp_name'];
                $file_name = basename($_FILES['guide_file']['name']);
                $dest_path = $category_path . '/' . $file_name;

                if (move_uploaded_file($file_tmp_path, $dest_path)) {
                    $_SESSION['flash_message'] = ['type' => 'success', 'message' => '檔案已成功上傳！'];
                } else {
                    $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：檔案上傳失敗。'];
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：指定的分類不存在。'];
            }
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：上傳資料不完整。'];
        }
        $this->redirect($this->viewData['baseUrl'] . '/admin/guides-manager');
    }

    /**
     * 建立或編輯網頁內容。
     *
     * @return void
     */
    public function createWebContent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category']) && !empty($_POST['content_title']) && !empty($_POST['content_body'])) {
            $category_name = $_POST['category'];
            $content_title = trim($_POST['content_title']);
            $content_body = $_POST['content_body'];
            $category_path = self::GUIDES_BASE_PATH . $category_name;

            if (is_dir($category_path)) {
                // 清理標題，建立檔名
                $safe_title = preg_replace('/[^A-Za-z0-9\x{4e00}-\x{9fa5}_-]/u', '', $content_title);
                $file_name = $safe_title . '.html';
                $dest_path = $category_path . '/' . $file_name;

                // 建立完整的 HTML 內容
                $html_content = $this->generateHTMLContent($content_title, $content_body);

                if (file_put_contents($dest_path, $html_content) !== false) {
                    $_SESSION['flash_message'] = ['type' => 'success', 'message' => '網頁內容已成功建立！'];
                } else {
                    $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：無法儲存檔案。'];
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：指定的分類不存在。'];
            }
        } else {
            $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：請填寫所有必要欄位。'];
        }
        $this->redirect($this->viewData['baseUrl'] . '/admin/guides-manager');
    }

    /**
     * 產生完整的 HTML 內容，套用與現有操作指引相同的樣式。
     */
    private function generateHTMLContent($title, $body)
    {
        return '<?php
// Auto-generated guide content
$pageTitle = "' . htmlspecialchars($title) . '";
$pageType = "guides";
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
.guide-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
}

.page-header h1 {
    margin-bottom: 10px;
    color: #333;
}

.guide-content {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.content-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    padding: 30px;
}

.content-card h2 {
    margin-top: 0;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
    color: #C8102E;
}

.content-card h3 {
    color: #666;
    margin-top: 20px;
}

.section-content {
    line-height: 1.6;
}

.section-content p {
    margin-bottom: 15px;
}

.section-content ul, .section-content ol {
    margin-left: 20px;
    margin-bottom: 15px;
}

.section-content li {
    margin-bottom: 8px;
}

.section-content a {
    color: #C8102E;
    text-decoration: none;
}

.section-content a:hover {
    text-decoration: underline;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.2s;
}

.btn-primary {
    background-color: #C8102E;
    color: white;
}

.btn-primary:hover {
    background-color: #a00d25;
}

.alert {
    padding: 12px 16px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.alert-warning {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.table th, .table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.table th {
    background-color: #f8f9fa;
    font-weight: bold;
}
    </style>
</head>
<body>

<div class="guide-container">
    <div class="page-header">
        <h1><?php echo $pageTitle; ?></h1>
    </div>

    <div class="guide-content">
        <div class="content-card">
            <div class="section-content">
                ' . $body . '
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 40px;">
        <a href="javascript:history.back()" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> 返回
        </a>
    </div>
</div>

</body>
</html>';
    }
    
    /**
     * 刪除指定的檔案。
     *
     * @return void
     */
    public function deleteFile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category']) && !empty($_POST['file_name'])) {
            $category_name = $_POST['category'];
            $file_name = $_POST['file_name'];
            $file_path = self::GUIDES_BASE_PATH . $category_name . '/' . $file_name;

            // 安全性檢查，確保不會刪除到意料之外的檔案
            if (strpos(realpath($file_path), realpath(self::GUIDES_BASE_PATH)) === 0 && file_exists($file_path)) {
                unlink($file_path);
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => '檔案已成功刪除。'];
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：檔案不存在或路徑不合法。'];
            }
        }
        $this->redirect($this->viewData['baseUrl'] . '/admin/guides-manager');
    }

    /**
     * 刪除指定的分類（資料夾）。
     *
     * @return void
     */
    public function deleteCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['category_name'])) {
            $category_name = $_POST['category_name'];
            $category_path = self::GUIDES_BASE_PATH . $category_name;

            if (is_dir($category_path)) {
                // 為了安全，只允許刪除空的資料夾
                if (count(scandir($category_path)) == 2) {
                    rmdir($category_path);
                    $_SESSION['flash_message'] = ['type' => 'success', 'message' => '分類已成功刪除。'];
                } else {
                    $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：請先刪除分類下的所有檔案，才能刪除分類。'];
                }
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => '錯誤：分類不存在。'];
            }
        }
        $this->redirect($this->viewData['baseUrl'] . '/admin/guides-manager');
    }
}
