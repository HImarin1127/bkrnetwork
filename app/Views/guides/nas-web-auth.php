<div class="guide-container">
    <div class="page-header">
        <h1>網頁版使用與二次驗證</h1>
        <p class="page-subtitle">NAS 外部存取、二次驗證設定與相關資源</p>
    </div>

    <div class="guide-content">
        <div class="content-card">
            <h2>外部存取與二次驗證</h2>
            <div class="section-content">
                <p>為了確保帳戶安全，從公司外部網路存取 NAS 需要進行二次驗證。詳細設定請參考以下文件：</p>
                <a href="https://drive.google.com/file/d/1y7BH12v9KewvwSpFqs0BkRkPAoh3Zg6Q/view?usp=drive_link" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    <i class="fas fa-shield-alt"></i> NAS外部存取二次驗證 教學
                </a>
            </div>
        </div>

        <div class="content-card">
            <h2>外部存取入口</h2>
            <div class="section-content">
                <p>設定完成後，您可以透過以下專屬連結從外部網路存取公司 NAS：</p>
                <ul>
                    <li>
                        <strong>NAS外部存取-BKNAS1:</strong>
                        <a href="http://QuickConnect.to/bknas61" target="_blank" rel="noopener noreferrer">http://QuickConnect.to/bknas61</a>
                    </li>
                    <li>
                        <strong>NAS外部存取-BKNAS2:</strong>
                        <a href="http://QuickConnect.to/bknas62" target="_blank" rel="noopener noreferrer">http://QuickConnect.to/bknas62</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="content-card">
            <h2>NAS 雲端捷徑</h2>
            <div class="section-content">
                <p>為了方便您快速存取，可下載 NAS 的雲端捷徑：</p>
                 <a href="https://drive.bookrep.com.tw/nextcloud/index.php/s/EnXr7SJ2JBPJ2AG" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                    <i class="fas fa-cloud-download-alt"></i> NAS雲端捷徑下載
                </a>
            </div>
        </div>
    </div>
</div>

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
}

.page-subtitle {
    font-size: 1.1rem;
    color: #666;
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
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.section-content p {
    margin-bottom: 20px;
    line-height: 1.6;
}

.section-content ul {
    list-style-type: none;
    padding: 0;
}

.section-content li {
    margin-bottom: 10px;
    padding: 10px;
    background-color: #f9f9f9;
    border-radius: 5px;
}

.section-content ul li a {
    color: #C8102E;
    text-decoration: none;
    font-weight: 500;
}

.section-content li a {
    margin-left: 10px;
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

.btn-secondary {
    background-color: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background-color: #5a6268;
}
</style> 