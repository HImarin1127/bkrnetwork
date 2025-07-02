<div class="auth-container">
    <div class="auth-background">
        <div class="auth-pattern"></div>
        <div class="auth-pattern-overlay"></div>
    </div>
    
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <div class="logo-circle">
                    <span class="logo-text">讀</span>
                </div>
            </div>
            <h2 class="auth-title">🔐 員工服務系統</h2>
            <p class="auth-subtitle">歡迎登入讀書共和國內部服務平台</p>
        </div>
        
        <div class="auth-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <div class="alert-content">
                        <span class="alert-icon">❌</span>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <div class="alert-content">
                        <span class="alert-icon">✅</span>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo $baseUrl; ?>/login" class="auth-form">
                <div class="login-options">
                    <div class="login-methods">
                        <div class="method-badge ldap-badge">
                            <span class="badge-icon">🌐</span>
                            <span class="badge-text">LDAP 企業帳號登入</span>
                        </div>
                        <div class="method-info">
                            <p>可使用公司域帳號或本地帳號登入</p>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="username" class="form-label">👤 員工帳號</label>
                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>
                        <input type="text" id="username" name="username" class="form-input" required 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               placeholder="請輸入您的LDAP帳號或本地帳號">
                    </div>
                    <div class="field-hint">
                        <span class="hint-icon">💡</span>
                        <span class="hint-text">支援公司域帳號(LDAP)和本地帳號登入</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">🔑 登入密碼</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔑</span>
                        <input type="password" id="password" name="password" class="form-input" required 
                               placeholder="請輸入您的密碼">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <span id="toggleIcon">👁️</span>
                        </button>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-full">
                        <span class="btn-icon">🚀</span>
                        <span class="btn-text">安全登入</span>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <div class="auth-links">
                <a href="<?php echo $baseUrl; ?>/register" class="auth-link">
                    <span>➕</span> 申請新帳號
                </a>
                <a href="<?php echo $baseUrl; ?>/ldap-test" class="auth-link">
                    <span>🔍</span> LDAP 測試工具
                </a>
                <a href="<?php echo $baseUrl; ?>/" class="auth-link">
                    <span>🏠</span> 返回首頁
                </a>
            </div>
            <div class="auth-info">
                <p class="info-text">
                    <span class="info-icon">ℹ️</span>
                    如有帳號問題，請聯繫資訊部門
                </p>
            </div>
        </div>
    </div>
</div>

<style>
/* 認證頁面容器 */
.auth-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

/* 背景裝飾 */
.auth-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: -1;
}

.auth-pattern {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: repeating-linear-gradient(
        45deg,
        rgba(200,16,46,0.03) 0px,
        rgba(200,16,46,0.03) 2px,
        transparent 2px,
        transparent 20px
    );
    animation: patternMove 20s linear infinite;
}

.auth-pattern-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(
        circle at center,
        rgba(255,255,255,0.9) 0%,
        rgba(255,255,255,0.7) 100%
    );
}

@keyframes patternMove {
    0% { transform: translate(0, 0); }
    100% { transform: translate(20px, 20px); }
}

/* 認證卡片 */
.auth-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(200,16,46,0.15);
    border: 1px solid rgba(200,16,46,0.1);
    padding: 3rem;
    width: 100%;
    max-width: 450px;
    position: relative;
    z-index: 1;
}

/* 登入選項區 */
.login-options {
    margin-bottom: 2rem;
}

.login-methods {
    text-align: center;
}

.method-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(200,16,46,0.3);
    margin-bottom: 1rem;
}

.method-badge .badge-icon {
    font-size: 1.1rem;
}

.method-info {
    margin-top: 0.5rem;
}

.method-info p {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
}

/* 欄位提示 */
.field-hint {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    padding: 0.5rem;
    background: rgba(200,16,46,0.05);
    border-radius: 8px;
    border-left: 3px solid #C8102E;
}

.hint-icon {
    font-size: 0.9rem;
    color: #C8102E;
}

.hint-text {
    font-size: 0.85rem;
    color: #666;
    line-height: 1.4;
}

/* 認證標題區 */
.auth-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.auth-logo {
    margin-bottom: 1.5rem;
}

.logo-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 8px 25px rgba(200,16,46,0.3);
    animation: logoFloat 3s ease-in-out infinite;
}

@keyframes logoFloat {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-5px); }
}

.logo-text {
    color: white;
    font-size: 2rem;
    font-weight: 700;
    font-family: 'Microsoft JhengHei', sans-serif;
}

.auth-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
}

.auth-subtitle {
    color: #666;
    margin: 0;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* 表單樣式 */
.auth-form {
    margin-bottom: 2rem;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 1rem;
    font-size: 1.1rem;
    color: #666;
    z-index: 2;
}

.form-input {
    padding-left: 3rem !important;
    padding-right: 3rem !important;
}

.password-toggle {
    position: absolute;
    right: 1rem;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
    color: #666;
    transition: color 0.3s ease;
    z-index: 2;
}

.password-toggle:hover {
    color: #C8102E;
}

/* 按鈕樣式 */
.btn-full {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 1.2rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 16px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-full:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.btn-full:hover:before {
    left: 100%;
}

.btn-icon {
    font-size: 1.2rem;
}

.btn-text {
    letter-spacing: 0.5px;
}

/* 警告訊息 */
.alert {
    padding: 1rem 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    border: 1px solid;
}

.alert-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-icon {
    font-size: 1.2rem;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border-color: #c3e6cb;
}

.alert-error {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border-color: #f5c6cb;
}

/* 底部連結 */
.auth-footer {
    border-top: 1px solid rgba(200,16,46,0.1);
    padding-top: 2rem;
}

.auth-links {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.auth-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #C8102E;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    background: rgba(200,16,46,0.05);
}

.auth-link:hover {
    background: rgba(200,16,46,0.1);
    transform: translateY(-1px);
}

.auth-info {
    text-align: center;
}

.info-text {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    color: #666;
    font-size: 0.85rem;
    margin: 0;
}

.info-icon {
    font-size: 1rem;
}

/* 響應式設計 */
@media (max-width: 480px) {
    .auth-container {
        padding: 1rem;
    }
    
    .auth-card {
        padding: 2rem;
    }
    
    .auth-links {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.textContent = '🙈';
    } else {
        passwordInput.type = 'password';
        toggleIcon.textContent = '👁️';
    }
}
</script> 