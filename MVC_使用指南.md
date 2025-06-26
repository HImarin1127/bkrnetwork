# 讀書共和國員工服務網 - MVC 架構技術指南 v2.0

## 📋 系統總覽

### 🏢 專案簡介
**讀書共和國員工服務網** (BKR Network) 是一個現代化的企業級內部服務平台，採用 **PHP MVC 架構** 設計，為讀書共和國員工提供一站式數位服務。

### ✨ 核心特色
- 🎨 **現代化 UI**: 玻璃質感設計、響應式佈局
- 🔐 **三層權限**: 訪客、使用者、管理員權限體系
- 📱 **全裝置支援**: 桌機、平板、手機完整適配
- 🔗 **外部整合**: 會議室、NAS、郵件系統等真實服務
- 📈 **企業級功能**: 完整 CRUD、批量處理、資料匯入匯出

---

## 🏗️ MVC 架構詳解

### 架構流程
```
📱 用戶請求 → 🎯 Controller → 🗄️ Model → 🎨 View → 👤 用戶回應
```

### 目錄結構
```
bkrnetwork/
├── 🚪 index.php                        # 應用程式統一入口點
├── 📁 app/                              # 核心應用程式邏輯
│   ├── 🎯 Controllers/                  # 控制器層 - 業務邏輯處理
│   │   ├── Controller.php               # 基礎控制器
│   │   ├── HomeController.php           # 首頁控制器
│   │   ├── AuthController.php           # 認證控制器
│   │   ├── MailController.php           # 郵務系統 (核心功能)
│   │   ├── AdminController.php          # 後台管理
│   │   ├── FormsController.php          # 行政表單
│   │   ├── BookingController.php        # 預約系統
│   │   └── GuidesController.php         # 操作指引
│   ├── 🗄️ Models/                       # 資料模型層 - 資料庫操作
│   │   ├── Model.php                    # 基礎模型
│   │   ├── Database.php                 # 資料庫連接 (單例模式)
│   │   ├── User.php                     # 使用者模型
│   │   ├── MailRecord.php               # 郵務記錄 (核心功能)
│   │   ├── Announcement.php             # 公告系統
│   │   └── HolidayCalendar.php          # 假日行事曆
│   ├── 🎨 Views/                        # 視圖模板層 - 使用者介面
│   │   ├── layouts/app.php              # 主要佈局模板
│   │   ├── home/                        # 首頁視圖
│   │   ├── auth/                        # 認證視圖
│   │   ├── mail/                        # 郵務系統 (核心功能)
│   │   ├── admin/                       # 管理後台
│   │   ├── announcements/               # 公告系統
│   │   ├── company/                     # 公司介紹
│   │   ├── booking/                     # 預約系統
│   │   ├── forms/                       # 行政表單
│   │   └── guides/                      # 操作指引
│   └── 🔒 Middleware/                   # 中間件層
│       └── AuthMiddleware.php           # 身份驗證中間件
├── ⚙️ config/                           # 系統設定
│   ├── app.php                          # 應用程式設定
│   └── database.php                     # 資料庫設定
├── 🛣️ routes/web.php                   # 路由配置
├── 🎨 assets/                           # 靜態資源
│   ├── css/styles.css                   # 主要樣式
│   ├── js/scripts.js                    # JavaScript
│   └── images/                          # 圖片資源
├── 📤 uploads/                          # 檔案上傳
└── 🗄️ create_database.sql              # 資料庫腳本
```

---

## 🎯 五大功能模組

### 1️⃣ 📢 公告系統 (公開存取)
**架構**: `HomeController` → `Announcement/HolidayCalendar` → `announcements/*`
```
📢 公告系統
├── 🗞️ 最新公告 - 一般性通知公告
├── 📅 國定假日 - 政府行事曆整合 (自動爬取)
└── 📖 員工手冊 - 公司規章制度
```

### 2️⃣ 🏢 公司資訊 (公開存取)
**架構**: `HomeController` → 靜態內容 → `company/*`
```
🏢 公司資訊
├── 🏗️ 樓層與座位圖 - 3-7樓空間配置
├── 📞 聯絡資訊 - 分機號碼、Email 通訊錄
└── 💾 NAS 公區介紹 - 網路儲存使用說明
```

### 3️⃣ 📋 郵務管理系統 (需登入) **[核心功能]**
**架構**: `MailController` → `MailRecord` → `mail/*` → `mail_records`
```
📋 郵務管理系統
├── 📤 寄件管理
│   ├── 寄件登記 - 表單驗證、自動編號生成
│   ├── 記錄查詢 - 權限過濾、關鍵字搜尋
│   ├── 批量匯入 - CSV 檔案處理、錯誤報告
│   └── 記錄編輯 - 即時更新、權限控制
├── 📨 收件管理 - 郵件收取記錄
└── 💰 郵資查詢 - 自動計算、費用統計
```

### 4️⃣ 📅 預約借用系統 (需登入)
**架構**: `BookingController` → 預約模型 → `booking/*`
```
📅 預約借用系統
├── 🏢 會議室預約 - 外部系統整合
└── 💻 設備借用 - 內部設備管理
```

### 5️⃣ 📝 行政表單系統 (需登入)
**架構**: `FormsController` → `FormSubmission` → `forms/*`
```
📝 行政表單系統
├── 👥 人事相關 - 到職、調職、離職申請
├── 💻 設備相關 - 採購、報廢申請
├── 🔐 權限申請 - MF2000、NAS、EMAIL、VPN
├── 📚 教育訓練 - 資訊部基礎教育
└── 🔧 其他服務 - QR Code 申請
```

### 6️⃣ ❓ 操作指引系統 (需登入)
**架構**: `GuidesController` → 靜態內容 → `guides/*`
```
❓ 操作指引系統
├── 🖥️ Windows 相關 - 遠端連線、音訊設定
├── 🖨️ 印表機使用 - 基本操作、故障排除
├── 🍎 MAC 相關 - 網頁列印、驅動安裝
├── 💾 NAS 公區 - 密碼重設、二次驗證
├── 📧 電子郵件 - 完整設定指引
└── 💸 文化部免稅 - 申請流程、系統操作
```

---

## 🔐 權限架構

### 三層權限體系
```
🔐 權限系統
├── 👤 訪客 (Guest)
│   ├── 存取: 公告區、公司介紹
│   └── 限制: 僅能瀏覽公開內容
├── 👨‍💼 一般使用者 (User)
│   ├── 存取: 所有功能模組
│   └── 限制: 只能管理自己的資料
└── 👨‍💻 管理員 (Admin)
    ├── 存取: 所有功能 + 後台管理
    └── 權限: 管理所有使用者資料
```

### 安全機制
- **身份驗證**: Session 基礎驗證
- **權限控制**: Middleware 層級檢查
- **資料保護**: PDO 參數綁定防 SQL 注入
- **密碼安全**: bcrypt 雜湊加密
- **CSRF 保護**: 表單令牌驗證

---

## 🗄️ 資料庫架構

### 核心資料表
```
📊 資料庫 (bkrnetwork)
├── 👥 users - 使用者基礎資料
│   ├── 登入: username, password
│   ├── 資料: name, email
│   └── 權限: role, status
├── 📢 announcements - 公告系統
│   ├── 內容: title, content
│   ├── 分類: type (general/holiday/handbook)
│   └── 關聯: author_id → users.id
├── 📋 mail_records - 郵務記錄 **[核心資料表]**
│   ├── 郵件: mail_code, mail_type, tracking_number
│   ├── 寄件: sender_name, sender_ext
│   ├── 收件: receiver_name, receiver_address, receiver_phone
│   ├── 業務: declare_department, postage, status
│   └── 關聯: registrar_id → users.id
├── 📅 meeting_room_bookings - 會議室預約
├── 💻 equipment_bookings - 設備借用
├── 📝 form_submissions - 表單申請 (JSON 格式)
└── 📅 holiday_calendar - 假日行事曆 (JSON 格式)
```

---

## 🛣️ 路由系統

### 路由分層
```php
// routes/web.php
🛣️ 路由架構
├── 🌐 公開路由 (無需登入)
│   ├── / → HomeController@index
│   ├── /announcements → HomeController@announcements
│   ├── /company → HomeController@company
│   └── /login, /register → AuthController
├── 🔐 需要登入路由
│   ├── /mail/* → MailController (郵務系統)
│   ├── /forms/* → FormsController (表單系統)
│   ├── /booking/* → BookingController (預約系統)
│   └── /guides/* → GuidesController (指引系統)
└── 👨‍💻 管理員路由
    └── /admin/* → AdminController (後台管理)
```

---

## 🎨 前端架構

### 設計系統
- **品牌色**: #C8102E (讀書共和國紅)
- **設計風格**: 現代玻璃質感 (Glassmorphism)
- **字體**: Microsoft JhengHei
- **響應式**: Mobile-First 設計

### UI 組件
```css
🎨 UI 組件
├── .btn (按鈕系統) - primary, secondary, outline
├── .card (卡片組件) - 玻璃質感、陰影效果
├── .form-group (表單組件) - 統一表單樣式
└── 響應式組件 - dashboard-cards, features-grid
```

---

## 🚀 部署維護

### 系統需求
- **PHP**: 7.4+ (建議 8.0+)
- **MySQL**: 5.7+ 或 MariaDB 10.2+
- **Web Server**: Apache 2.4+ 或 Nginx 1.18+

### 快速部署
```bash
# 1. 資料庫設定
mysql -u root -p < create_database.sql

# 2. 設定檔案 (config/database.php)
'host' => '127.0.0.1'
'username' => 'root'
'password' => 'your_password'
'database' => 'bkrnetwork'

# 3. 權限設定
chmod 755 uploads/

# 4. 測試環境
http://localhost/bkrnetwork/
```

### 預設帳號
```
管理員: admin / password
測試帳號: test / password
```

---

## 📞 技術支援

### 聯絡資訊
- **資訊部**: 分機 701-705
- **LINE**: @375wyssh
- **Email**: it@bookrep.com.tw

### 相關文檔
- **新手指南**: 新手接手指南.md
- **管理指南**: 管理者操作指南.md
- **資料庫圖**: database_architecture.md

---

**© 2025 讀書共和國出版集團**  
**最後更新**: 2025年1月15日 v2.0  
**維護**: 資訊部 701-705 