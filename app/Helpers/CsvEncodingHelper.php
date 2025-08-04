<?php
/**
 * CsvEncodingHelper.php
 * 
 * CSV 檔案編碼偵測和轉換工具
 * 自動處理舊版 Office 的編碼問題
 * 
 * @author     B. R. Network
 * @copyright  2024 B. R. Network
 * @license    MIT License
 * @version    1.0.0
 */

namespace App\Helpers;

class CsvEncodingHelper
{
    /**
     * 支援的編碼格式（按偵測優先順序排列）
     */
    private static $supportedEncodings = [
        'UTF-8',
        'BIG5',
        'GB2312',
        'GBK',
        'ISO-8859-1',
        'Windows-1252',
        'ASCII'
    ];

    /**
     * 自動偵測 CSV 檔案的編碼格式
     *
     * @param string $filePath CSV 檔案路徑
     * @return string 偵測到的編碼格式
     */
    public static function detectEncoding($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("檔案不存在: {$filePath}");
        }

        // 讀取檔案前 8KB 來進行編碼偵測
        $handle = fopen($filePath, 'rb');
        $sample = fread($handle, 8192);
        fclose($handle);

        // 先檢查 BOM
        $bom = substr($sample, 0, 3);
        if ($bom === "\xEF\xBB\xBF") {
            return 'UTF-8';
        }

        // 使用 mb_detect_encoding 進行偵測
        $detectedEncoding = mb_detect_encoding($sample, self::$supportedEncodings, true);
        
        if ($detectedEncoding !== false) {
            return $detectedEncoding;
        }

        // 如果自動偵測失敗，使用啟發式方法
        return self::heuristicDetection($sample);
    }

    /**
     * 啟發式編碼偵測
     * 當 mb_detect_encoding 無法確定時使用
     *
     * @param string $sample 檔案樣本
     * @return string 推測的編碼格式
     */
    private static function heuristicDetection($sample)
    {
        // 檢查常見的中文字符模式
        if (preg_match('/[\x81-\xFE][\x40-\x7E\x80-\xFE]/', $sample)) {
            // Big5 模式
            return 'BIG5';
        }

        if (preg_match('/[\xA1-\xFE][\xA1-\xFE]/', $sample)) {
            // GB2312/GBK 模式
            return 'GB2312';
        }

        // 預設使用 UTF-8
        return 'UTF-8';
    }

    /**
     * 將 CSV 檔案轉換為 UTF-8 編碼
     *
     * @param string $inputPath 輸入檔案路徑
     * @param string $outputPath 輸出檔案路徑（如為空則覆蓋原檔案）
     * @param string $sourceEncoding 來源編碼（如為空則自動偵測）
     * @return array 轉換結果 ['success' => bool, 'encoding' => string, 'message' => string]
     */
    public static function convertToUtf8($inputPath, $outputPath = null, $sourceEncoding = null)
    {
        try {
            if ($outputPath === null) {
                $outputPath = $inputPath;
            }

            // 偵測編碼
            if ($sourceEncoding === null) {
                $sourceEncoding = self::detectEncoding($inputPath);
            }

            // 如果已經是 UTF-8，直接返回
            if (strtoupper($sourceEncoding) === 'UTF-8') {
                return [
                    'success' => true,
                    'encoding' => $sourceEncoding,
                    'message' => '檔案已經是 UTF-8 編碼，無需轉換。'
                ];
            }

            // 讀取原檔案
            $content = file_get_contents($inputPath);
            if ($content === false) {
                throw new \Exception('無法讀取檔案內容');
            }

            // 轉換編碼
            $utf8Content = mb_convert_encoding($content, 'UTF-8', $sourceEncoding);

            // 寫入轉換後的檔案
            if (file_put_contents($outputPath, $utf8Content) === false) {
                throw new \Exception('無法寫入轉換後的檔案');
            }

            return [
                'success' => true,
                'encoding' => $sourceEncoding,
                'message' => "成功將 {$sourceEncoding} 編碼轉換為 UTF-8。"
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'encoding' => $sourceEncoding ?? 'unknown',
                'message' => '編碼轉換失敗：' . $e->getMessage()
            ];
        }
    }

    /**
     * 讀取 CSV 檔案並自動處理編碼問題
     *
     * @param string $filePath CSV 檔案路徑
     * @param string $delimiter 分隔符（預設為逗號）
     * @param string $enclosure 圍繞符（預設為雙引號）
     * @return array CSV 資料陣列
     */
    public static function readCsvWithEncodingDetection($filePath, $delimiter = ',', $enclosure = '"')
    {
        // 建立暫存檔案來儲存轉換後的內容
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_utf8_');
        
        try {
            // 轉換為 UTF-8
            $result = self::convertToUtf8($filePath, $tempFile);
            
            if (!$result['success']) {
                throw new \Exception($result['message']);
            }

            // 讀取轉換後的 CSV
            $csvData = [];
            if (($handle = fopen($tempFile, 'r')) !== false) {
                while (($row = fgetcsv($handle, 0, $delimiter, $enclosure)) !== false) {
                    $csvData[] = $row;
                }
                fclose($handle);
            }

            return [
                'success' => true,
                'data' => $csvData,
                'encoding' => $result['encoding'],
                'message' => $result['message']
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'data' => [],
                'encoding' => 'unknown',
                'message' => '讀取 CSV 失敗：' . $e->getMessage()
            ];
        } finally {
            // 清理暫存檔案
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * 生成指定編碼的 CSV 檔案（用於匯出）
     *
     * @param array $data CSV 資料
     * @param string $filePath 輸出檔案路徑
     * @param string $encoding 目標編碼（預設 UTF-8）
     * @param string $delimiter 分隔符
     * @param string $enclosure 圍繞符
     * @return array 操作結果
     */
    public static function writeCsvWithEncoding($data, $filePath, $encoding = 'UTF-8', $delimiter = ',', $enclosure = '"')
    {
        try {
            $tempFile = tempnam(sys_get_temp_dir(), 'csv_write_');
            
            // 先寫入 UTF-8 格式
            $handle = fopen($tempFile, 'w');
            if (!$handle) {
                throw new \Exception('無法建立暫存檔案');
            }

            foreach ($data as $row) {
                fputcsv($handle, $row, $delimiter, $enclosure);
            }
            fclose($handle);

            // 如果目標編碼不是 UTF-8，進行轉換
            if (strtoupper($encoding) !== 'UTF-8') {
                $content = file_get_contents($tempFile);
                $convertedContent = mb_convert_encoding($content, $encoding, 'UTF-8');
                file_put_contents($filePath, $convertedContent);
            } else {
                // 直接複製檔案
                copy($tempFile, $filePath);
            }

            unlink($tempFile);

            return [
                'success' => true,
                'encoding' => $encoding,
                'message' => "成功生成 {$encoding} 編碼的 CSV 檔案。"
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'encoding' => $encoding,
                'message' => 'CSV 檔案生成失敗：' . $e->getMessage()
            ];
        }
    }
}