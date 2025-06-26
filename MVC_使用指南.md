# 讀書共和國員工服務網 - 系統使用指南

## 🏢 系統概述

這是一個專為讀書共和國員工設計的企業級服務網站，採用 PHP MVC 架構，整合了五大核心功能模組，提供完整的內部服務體驗。

### ✨ 核心特色
- **品牌統一**：採用讀書共和國官方品牌色彩 (#C8102E) 和 LOGO
- **三階層導航**：最大深度限制為3層，確保使用簡潔性
- **響應式設計**：支援桌機、平板、手機等各種裝置
- **權限分級**：訪客、一般使用者、管理員三級權限
- **真實整合**：連接實際外部系統和服務

## 🎯 五大功能模組

### 1️⃣ 公告區 (公開存取)
```
📢 公告區
├── 國定假日公告     # 政府公告假日資訊
└── 員工手冊         # 公司規章制度
```

### 2️⃣ 公司介紹 (公開存取)
```
🏢 公司介紹
├── 樓層與座位圖     # 3-7樓互動式樓層圖
├── 社內分機與Email  # 部門聯絡資訊
└── NAS公區介紹      # 網路儲存系統說明
```

### 3️⃣ 資源預約借用 (需登入)
```
📅 資源預約借用
├── 會議室預約       # 外部系統 → https://meeting.bookrep.com.tw/
└── 器材設備預約     # 內部設備借用申請
```

### 4️⃣ 行政表單申請 (需登入)
```
📋 行政表單申請
├── 郵務 (3階層)
│   ├── 寄件處理     # 郵件寄送登記和查詢
│   ├── 收件處理     # 郵件收取登記和查詢  
│   └── 郵資查詢     # 郵資計算和記錄
├── 到職申請         # 新進員工報到手續
├── 調職申請         # 部門異動申請
├── 離職申請         # 離職手續申請
├── 設備採購         # IT設備採購申請
├── 設備報廢         # 舊設備報廢登記
├── 系統權限申請 (3階層)
│   ├── MF2000權限   # 公文系統權限
│   ├── NAS公區權限  # 網路儲存權限
│   ├── 公司EMAIL    # 電子郵件帳號
│   └── VPN權限申請  # 遠端連線權限
├── 資訊部基礎教育   # IT技能培訓申請
└── QRcode申請       # QR碼產生需求
```

### 5️⃣ 操作問題與指引 (需登入)
```
❓ 操作問題與指引
├── Windows相關 (3階層)
│   ├── Windows遠端連線
│   └── Windows音訊更新
├── 印表機使用 (3階層)
│   ├── 基本操作說明   # 影印、掃描、傳真
│   └── 印表機疑難處理
├── MAC影印 (3階層)
│   ├── WEB列印網頁
│   └── MAC影印機驅動安裝
├── POS收銀機操作手冊
├── NAS公區相關 (3階層)
│   ├── 忘記密碼
│   └── 網頁版使用與二次驗證
├── 電子郵件相關 (3階層)
│   └── EMAIL完整設定指引
└── 文化部免稅相關 (3階層)
    ├── 免稅申請流程
    └── 免稅系統操作說明
```

## 🔧 技術架構

### 目錄結構
```
bkrnetwork/
├── index.php                 # 統一入口點
├── app/                      # 應用程式核心
│   ├── Controllers/          # 控制器層
│   │   ├── HomeController.php      # 首頁和公開頁面
│   │   ├── AuthController.php      # 登入認證
│   │   ├── MailController.php      # 郵務功能
│   │   ├── FormsController.php     # 行政表單
│   │   ├── BookingController.php   # 資源預約
│   │   ├── GuidesController.php    # 操作指引
│   │   └── AdminController.php     # 後台管理
│   ├── Models/               # 資料模型層
│   │   ├── Database.php            # 資料庫連接
│   │   ├── User.php                # 使用者模型
│   │   ├── MailRecord.php          # 郵務記錄
│   │   └── Announcement.php        # 公告模型
│   ├── Views/                # 視圖模板層
│   │   ├── layouts/app.php         # 主要佈局
│   │   ├── home/                   # 首頁視圖
│   │   ├── announcements/          # 公告視圖
│   │   ├── company/                # 公司介紹
│   │   ├── booking/                # 預約系統
│   │   ├── forms/                  # 表單視圖
│   │   ├── guides/                 # 操作指引
│   │   ├── mail/                   # 郵務系統
│   │   ├── auth/                   # 認證頁面
│   │   └── admin/                  # 管理後台
│   └── Middleware/           # 中間件
│       └── AuthMiddleware.php      # 權限控制
├── config/                   # 設定檔案
│   ├── app.php                     # 應用設定
│   └── database.php                # 資料庫設定
├── routes/                   # 路由定義
│   └── web.php                     # 路由配置
├── assets/                   # 靜態資源
│   ├── css/styles.css              # 主要樣式
│   ├── js/scripts.js               # JavaScript
│   └── images/                     # 圖片資源
│       ├── logo-horizontal.png     # 橫式LOGO
│       └── logo-square.jpg         # 方形LOGO
├── uploads/                  # 上傳檔案
├── 資訊_暫存/                # 開發參考資料
│   ├── 附件資料/                   # 操作手冊PDF
│   ├── 讀書共和國logo/             # 官方LOGO檔案
│   └── 一頁式網頁/                 # 架構規劃文件
├── create_database.sql       # 資料庫建立腳本
└── database_setup.sql        # 資料庫初始化
```

## 🌐 外部系統整合

### 真實連結服務
- **會議室預約**：https://meeting.bookrep.com.tw/
- **Webmail系統**：mail.bookrep.com.tw
- **NAS儲存系統**：
  - BKNAS1：http://QuickConnect.to/bknas61
  - BKNAS2：http://QuickConnect.to/bknas62
- **Nextcloud雲端**：https://drive.bookrep.com.tw/nextcloud/
- **電子公文系統**：https://eflow.bookrep.com.tw/docm
- **免稅系統相關**：各種文化部免稅申請系統
- **技術支援LINE**：@375wyssh

### 教學文件連結
- **Gmail設定教學**：https://sites.google.com/view/bkrep-g/gmail設定公司信
- **手機設定PDF**：Google Drive 分享連結
- **遠端工作手冊**：https://sites.google.com/view/bookrepvpntest/
- **MAC驅動程式**：Google Drive 下載連結

## 🔐 權限管理

### 三級權限制度
1. **訪客** (未登入)
   - 公告區：完整存取
   - 公司介紹：完整存取
   - 其他功能：需要登入

2. **一般使用者** (已登入)
   - 所有功能：完整存取
   - 郵務系統：可新增、編輯、刪除自己的記錄
   - 各種申請表單：提交和查詢

3. **管理員** (admin角色)
   - 所有功能：完整存取
   - 後台管理：使用者管理、公告管理
   - 郵務系統：管理所有記錄

### 預設管理員帳號
```
帳號：admin
密碼：password
```

## 🚀 安裝與部署

### 1. 環境需求
- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本
- Apache/Nginx 網頁伺服器
- 支援 URL Rewrite

### 2. 資料庫設定
```bash
# 1. 建立資料庫
mysql -u root -p
CREATE DATABASE bkrnetwork CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 2. 匯入資料表結構
mysql -u root -p bkrnetwork < create_database.sql
```

### 3. 設定檔案
編輯 `config/database.php`：
```php
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'username' => 'root',
    'password' => 'your_password',
    'database' => 'bkrnetwork',
    'charset' => 'utf8mb4',
];
```

### 4. 網頁伺服器設定
**Apache (.htaccess)**：
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

**測試環境**：
```bash
# 使用 XAMPP
http://localhost/bkrnetwork/

# 使用 PHP 內建伺服器
cd C:\xampp\htdocs\bkrnetwork
C:\xampp\php\php.exe -S localhost:8080
```

## 🎨 品牌設計規範

### 色彩配置
- **主要品牌色**：#C8102E (讀書共和國紅)
- **輔助色彩**：#A50E26 (深紅)
- **背景漸層**：#f5f7fa 到 #c3cfe2
- **文字色彩**：#2d3748 (深灰)

### LOGO使用
- **檔案位置**：assets/images/
- **橫式LOGO**：適用於 Header 導航
- **方形LOGO**：適用於圖示使用
- **透明背景**：支援各種背景色

### UI組件
- **玻璃擬態效果**：backdrop-filter: blur(10px)
- **圓角設計**：border-radius: 15px
- **陰影效果**：box-shadow 層次感
- **動畫過渡**：transition: all 0.3s ease

## 📱 響應式設計

### 中斷點設定
```css
/* 桌機版 */
@media (min-width: 1024px) { ... }

/* 平板版 */
@media (max-width: 1024px) { ... }

/* 手機版 */
@media (max-width: 768px) { ... }
```

### 行動裝置優化
- 觸控友善的按鈕大小
- 適應螢幕寬度的表格
- 摺疊式導航選單
- 圖片自動縮放

## 🔄 版本更新記錄

### v2.0 (目前版本)
- ✅ 重新架構為標準五大模組
- ✅ 實施三階層導航限制
- ✅ 整合讀書共和國品牌設計
- ✅ 連接真實外部系統
- ✅ 完整的權限管理制度
- ✅ 響應式設計優化

### v1.0 (初始版本)
- 基本 MVC 架構
- 郵務系統功能
- 使用者認證系統

## 📞 技術支援

### 聯絡資訊
- **資訊部分機**：701-705
- **LINE官方帳號**：[@375wyssh](https://line.me/R/ti/p/%40375wyssh)
- **Email**：it@bookrep.com.tw
- **緊急聯絡**：(02) 2500-7701

### 服務時間
- **平日服務**：09:00 - 18:00
- **緊急支援**：24小時待命

---

**© 2025 讀書共和國出版集團 版權所有**  
**系統版本：MVC 2.0**  
**最後更新：2025-06-25** 