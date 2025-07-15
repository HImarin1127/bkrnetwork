-- 這個 SQL 指令用於為 `mail_records` 資料表增加一個新的欄位，
-- 用來標記一筆郵件紀錄是否是從外部檔案（例如 Excel）匯入的。

ALTER TABLE `mail_records`
ADD COLUMN `is_imported` BOOLEAN NOT NULL DEFAULT FALSE COMMENT '標記此筆紀錄是否由檔案匯入' AFTER `notes`; 