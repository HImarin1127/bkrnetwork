#!/bin/bash
# 正式環境修復腳本
# 解決PDF附件上傳的權限和目錄問題

echo "=== 正式環境PDF附件修復腳本 ==="
echo "適用於 Linux 伺服器環境"
echo ""

# 1. 建立英文目錄名稱的附件目錄
UPLOAD_DIR="/var/www/websites/eip/bkrnetwork/uploads"
ANNOUNCEMENT_DIR="$UPLOAD_DIR/announcements"

echo "1. 建立附件上傳目錄..."
if [ ! -d "$ANNOUNCEMENT_DIR" ]; then
    mkdir -p "$ANNOUNCEMENT_DIR"
    echo "✅ 已建立目錄: $ANNOUNCEMENT_DIR"
else
    echo "✅ 目錄已存在: $ANNOUNCEMENT_DIR"
fi

# 2. 設定正確的目錄權限
echo ""
echo "2. 設定目錄權限..."
chown -R www-data:www-data "$UPLOAD_DIR"
chmod -R 755 "$UPLOAD_DIR"
chmod -R 775 "$ANNOUNCEMENT_DIR"

echo "✅ 已設定目錄權限:"
echo "   - 擁有者: www-data:www-data" 
echo "   - 權限: uploads/ = 755, announcements/ = 775"

# 3. 遷移現有的中文目錄檔案（如果存在）
CHINESE_DIR="$UPLOAD_DIR/最新公告區"
if [ -d "$CHINESE_DIR" ]; then
    echo ""
    echo "3. 遷移現有檔案..."
    echo "發現中文目錄: $CHINESE_DIR"
    
    # 移動檔案到英文目錄
    if [ "$(ls -A $CHINESE_DIR 2>/dev/null)" ]; then
        mv "$CHINESE_DIR"/* "$ANNOUNCEMENT_DIR/"
        echo "✅ 已將檔案從中文目錄移動到英文目錄"
    fi
    
    # 刪除空的中文目錄
    rmdir "$CHINESE_DIR" 2>/dev/null && echo "✅ 已刪除空的中文目錄"
fi

# 4. 檢查 PHP 相關設定
echo ""
echo "4. 檢查 PHP 設定..."

# 檢查 upload_max_filesize
UPLOAD_MAX=$(php -r "echo ini_get('upload_max_filesize');")
echo "upload_max_filesize: $UPLOAD_MAX"

# 檢查 post_max_size  
POST_MAX=$(php -r "echo ini_get('post_max_size');")
echo "post_max_size: $POST_MAX"

# 檢查 max_execution_time
MAX_TIME=$(php -r "echo ini_get('max_execution_time');")
echo "max_execution_time: $MAX_TIME"

# 5. 測試權限
echo ""
echo "5. 測試目錄權限..."
TEST_FILE="$ANNOUNCEMENT_DIR/test_write.txt"
if echo "test" > "$TEST_FILE" 2>/dev/null; then
    rm "$TEST_FILE"
    echo "✅ 目錄寫入權限正常"
else
    echo "❌ 目錄寫入權限異常，請檢查權限設定"
fi

# 6. 更新資料庫中的附件路徑（如果需要）
echo ""
echo "6. 檢查資料庫中的附件路徑..."
echo "如果資料庫中有舊的中文路徑記錄，請執行以下 SQL："
echo ""
echo "UPDATE announcements"
echo "SET attachment_url = REPLACE(attachment_url, 'uploads/最新公告區/', 'uploads/announcements/')"
echo "WHERE attachment_url LIKE '%uploads/最新公告區/%';"
echo ""

echo "=== 修復完成 ==="
echo ""
echo "📋 後續檢查項目："
echo "1. 確認 Web 伺服器可以訪問 /uploads/announcements/ 目錄"
echo "2. 測試新增公告並上傳 PDF 附件"
echo "3. 確認前台可以正常下載附件"
echo "4. 檢查 Apache/Nginx 錯誤日誌是否還有相關錯誤"