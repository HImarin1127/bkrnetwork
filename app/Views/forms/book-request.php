<div class="under-development">
    <div class="development-container">
        <div class="development-icon">🚧</div>
        <h1>購書申請</h1>
        <p class="development-message">此功能正在開發中，敬請期待！</p>
        
        <div class="development-info">
            <div class="info-card">
                <h3>📋 功能說明</h3>
                <p>購書申請功能將提供：</p>
                <ul>
                    <li>書籍搜尋與選擇</li>
                    <li>申請理由填寫</li>
                    <li>預算審核流程</li>
                    <li>申請狀態追蹤</li>
                </ul>
            </div>
        </div>
        
        <div class="development-actions">
            <a href="<?php echo $baseUrl; ?>" class="btn btn-primary">返回首頁</a>
            <a href="<?php echo $baseUrl; ?>mail/request" class="btn btn-secondary">使用郵務功能</a>
        </div>
    </div>
</div>

<style>
.under-development {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.development-container {
    max-width: 600px;
    text-align: center;
    background: rgba(255, 255, 255, 0.95);
    padding: 3rem;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.development-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.development-container h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 1rem;
}

.development-message {
    font-size: 1.1rem;
    color: #666;
    margin-bottom: 2rem;
}

.info-card {
    background: rgba(123, 97, 255, 0.05);
    padding: 1.5rem;
    border-radius: 12px;
    text-align: left;
    margin-bottom: 2rem;
}

.info-card h3 {
    color: #7b61ff;
    margin-bottom: 1rem;
}

.development-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #7b61ff, #4caaff);
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
}
</style> 