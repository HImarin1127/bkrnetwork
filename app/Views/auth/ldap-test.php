<div class="auth-container">
    <div class="auth-background">
        <div class="auth-pattern"></div>
        <div class="auth-pattern-overlay"></div>
    </div>
    
    <div class="auth-card" style="max-width: 900px;">
        <div class="auth-header">
            <div class="auth-logo">
                <div class="logo-circle">
                    <span class="logo-text">🔍</span>
                </div>
            </div>
            <h2 class="auth-title">LDAP 診斷測試工具</h2>
            <p class="auth-subtitle">檢查LDAP連接狀態和帳號認證</p>
        </div>
        
        <div class="auth-body">
            <!-- 重要提示 -->
            <div class="test-section" style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-color: #ffeaa7; margin-bottom: 2rem;">
                <h3>🔍 LDAP 帳號測試說明</h3>
                <div style="background: rgba(255,255,255,0.9); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                    <p style="margin: 0 0 1rem 0; font-weight: 600; color: #856404;">
                        ⚠️ 請使用您在公司其他系統中使用的真實LDAP帳號和密碼進行測試
                    </p>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                        <div>
                            <strong>1️⃣ 選擇帳號：</strong><br>
                            從下方使用者清單中找到您的帳號
                        </div>
                        <div>
                            <strong>2️⃣ 輸入密碼：</strong><br>
                            使用您平時登入電腦/系統的密碼
                        </div>
                        <div>
                            <strong>3️⃣ 測試認證：</strong><br>
                            點擊測試按鈕驗證LDAP整合
                        </div>
                    </div>
                </div>
            </div>

            <!-- LDAP配置顯示 -->
            <div class="test-section info" style="margin-bottom: 2rem;">
                <h3>📋 LDAP 配置狀態</h3>
                <div class="config-grid">
                    <div class="config-item">
                        <strong>LDAP啟用:</strong> 
                        <span class="<?= $ldapConfig['enabled'] ? 'text-success' : 'text-error' ?>">
                            <?= $ldapConfig['enabled'] ? '✅ 是' : '❌ 否' ?>
                        </span>
                    </div>
                    <div class="config-item">
                        <strong>伺服器:</strong> <?= htmlspecialchars($ldapConfig['server']) ?>:<?= $ldapConfig['port'] ?>
                    </div>
                    <div class="config-item">
                        <strong>搜尋基礎:</strong> <?= htmlspecialchars($ldapConfig['user_search_base']) ?>
                    </div>
                    <div class="config-item">
                        <strong>過濾器:</strong> <?= htmlspecialchars($ldapConfig['user_filter']) ?>
                    </div>
                </div>
            </div>

            <!-- LDAP連接測試結果 -->
            <?php if ($connectionTest): ?>
                <div class="test-section <?= $connectionTest['success'] ? 'success' : 'error' ?>" style="margin-bottom: 2rem;">
                    <h3>🔗 LDAP 連接測試</h3>
                    <p><strong><?= $connectionTest['success'] ? '✅' : '❌' ?> <?= htmlspecialchars($connectionTest['message']) ?></strong></p>
                    <?php if (!empty($connectionTest['details'])): ?>
                        <ul style="margin-top: 1rem;">
                            <?php foreach ($connectionTest['details'] as $detail): ?>
                                <li><?= htmlspecialchars($detail) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- 帳號認證測試 -->
            <div class="test-section" style="margin-bottom: 2rem;">
                <h3>🧪 帳號認證測試</h3>
                <p style="color: #666; margin-bottom: 1rem;">請使用您的LDAP帳號進行測試</p>
                
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="test_username" class="form-label">👤 LDAP 帳號</label>
                        <div class="input-wrapper">
                            <span class="input-icon">👤</span>
                            <input type="text" id="test_username" name="test_username" class="form-input" 
                                   value="<?= htmlspecialchars($testUsername) ?>"
                                   placeholder="請輸入您的LDAP帳號" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="test_password" class="form-label">🔑 密碼</label>
                        <div class="input-wrapper">
                            <span class="input-icon">🔑</span>
                            <input type="password" id="test_password" name="test_password" class="form-input" 
                                   placeholder="請輸入您的LDAP密碼" required>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-full">
                            <span class="btn-icon">🔍</span>
                            <span class="btn-text">測試認證</span>
                        </button>
                    </div>
                </form>

                <!-- 測試結果顯示 -->
                <?php if ($testResult): ?>
                    <div class="alert <?= $testResult['success'] ? 'alert-success' : 'alert-error' ?>" style="margin-top: 1.5rem;">
                        <div class="alert-content">
                            <span class="alert-icon"><?= $testResult['success'] ? '✅' : '❌' ?></span>
                            <span><strong><?= htmlspecialchars($testResult['message']) ?></strong></span>
                        </div>
                        
                        <?php if ($testResult['success'] && isset($testResult['data'])): ?>
                            <div style="margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.8); border-radius: 8px;">
                                <h4>使用者資料:</h4>
                                <div class="user-data-grid">
                                    <div><strong>ID:</strong> <?= htmlspecialchars($testResult['data']['id'] ?? '未設定') ?></div>
                                    <div><strong>帳號:</strong> <?= htmlspecialchars($testResult['data']['username'] ?? '未設定') ?></div>
                                    <div><strong>姓名:</strong> <?= htmlspecialchars($testResult['data']['name'] ?? '未設定') ?></div>
                                    <div><strong>郵件:</strong> <?= htmlspecialchars($testResult['data']['email'] ?? '未設定') ?></div>
                                    <div><strong>角色:</strong> <?= htmlspecialchars($testResult['data']['role'] ?? '未設定') ?></div>
                                    <div><strong>認證來源:</strong> <?= htmlspecialchars($testResult['data']['auth_source'] ?? '未設定') ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

                         <!-- LDAP使用者清單 -->
             <?php if (!empty($ldapUsers)): ?>
                 <div class="test-section success" style="margin-bottom: 2rem;">
                     <h3>👥 LDAP 伺服器中的使用者清單</h3>
                     <p style="color: #155724; margin-bottom: 1rem;">
                         <strong>找到 <?= count($ldapUsers) ?> 個LDAP使用者帳號</strong> - 您可以使用這些帳號進行登入測試
                     </p>
                     
                     <div class="users-table-container">
                         <table class="users-table">
                             <thead>
                                 <tr>
                                     <th>帳號 (uid)</th>
                                     <th>姓名 (cn)</th>
                                     <th>郵件</th>
                                     <th>部門</th>
                                     <th>職稱</th>
                                     <th>操作</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 <?php foreach ($ldapUsers as $user): ?>
                                     <tr>
                                         <td><strong style="color: #C8102E;"><?= htmlspecialchars($user['uid']) ?></strong></td>
                                         <td><?= htmlspecialchars($user['cn']) ?></td>
                                         <td><?= htmlspecialchars($user['mail']) ?></td>
                                         <td><?= htmlspecialchars($user['department']) ?></td>
                                         <td><?= htmlspecialchars($user['title']) ?></td>
                                         <td>
                                             <button type="button" class="btn-use-account" 
                                                     onclick="useAccount('<?= htmlspecialchars($user['uid']) ?>')">
                                                 使用此帳號
                                             </button>
                                         </td>
                                     </tr>
                                 <?php endforeach; ?>
                             </tbody>
                         </table>
                     </div>
                     
                     <div style="margin-top: 1rem; padding: 1rem; background: rgba(255,255,255,0.8); border-radius: 8px;">
                         <h4>📋 使用說明：</h4>
                         <ul style="margin: 0.5rem 0 0 1.5rem;">
                             <li>點擊 <strong>"使用此帳號"</strong> 按鈕會自動填入帳號到測試欄位</li>
                             <li>然後輸入該帳號的真實LDAP密碼進行測試</li>
                             <li>如果您是 <strong><?= htmlspecialchars($testUsername) ?></strong>，請在清單中找到您的帳號</li>
                         </ul>
                     </div>
                 </div>
             <?php elseif ($connectionTest && $connectionTest['success']): ?>
                 <div class="test-section warning" style="margin-bottom: 2rem;">
                     <h3>⚠️ 找不到LDAP使用者</h3>
                     <p>LDAP連接成功，但在搜尋基礎 <code><?= htmlspecialchars($ldapConfig['user_search_base']) ?></code> 下找不到任何使用者。</p>
                     <p>可能的原因：</p>
                     <ul style="margin-left: 1.5rem;">
                         <li>搜尋基礎DN設定不正確</li>
                         <li>使用者在不同的組織單位中</li>
                         <li>LDAP過濾器設定有問題</li>
                         <li>服務帳號沒有讀取權限</li>
                     </ul>
                 </div>
             <?php endif; ?>

             <!-- 診斷建議 -->
             <div class="test-section info">
                 <h3>💡 診斷建議</h3>
                 <div class="diagnostic-tips">
                    <h4>✅ 如果認證成功：</h4>
                    <ul>
                        <li>您的LDAP整合運作正常</li>
                        <li>可以使用相同帳號在登入頁面登入</li>
                        <li>其他員工也可以使用他們的LDAP帳號登入</li>
                    </ul>
                    
                                         <h4>❌ 如果認證失敗：</h4>
                     <ul>
                         <li><strong>使用正確帳號:</strong> 請從上方的使用者清單中選擇存在的帳號</li>
                         <li><strong>使用真實LDAP密碼:</strong> 請輸入您在公司其他系統中使用的真實LDAP密碼</li>
                         <li><strong>檢查帳號狀態:</strong> 確認帳號沒有被鎖定或停用</li>
                         <li><strong>密碼正確性:</strong> 確認密碼沒有過期或需要更改</li>
                         <li><strong>大小寫敏感:</strong> 注意密碼的大小寫是否正確</li>
                     </ul>
                    
                    <h4>🔧 常見問題：</h4>
                    <ul>
                        <li><strong>連接超時:</strong> 檢查網路連接和防火牆設定</li>
                        <li><strong>搜尋失敗:</strong> 檢查搜尋基礎DN設定</li>
                        <li><strong>綁定失敗:</strong> 檢查服務帳號權限</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="auth-footer">
            <div class="auth-links">
                <a href="<?php echo $baseUrl; ?>/login" class="auth-link">
                    <span>🔐</span> 返回登入頁面
                </a>
                <a href="<?php echo $baseUrl; ?>/" class="auth-link">
                    <span>🏠</span> 返回首頁
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* 測試區塊樣式 */
.test-section {
    margin-bottom: 1.5rem;
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid #ddd;
}

.test-section.info {
    background: rgba(209, 236, 241, 0.3);
    border-color: #bee5eb;
}

.test-section.success {
    background: rgba(212, 237, 218, 0.3);
    border-color: #c3e6cb;
}

.test-section.error {
    background: rgba(248, 215, 218, 0.3);
    border-color: #f5c6cb;
}

.test-section h3 {
    margin: 0 0 1rem 0;
    color: #333;
    font-size: 1.2rem;
}

/* 配置網格 */
.config-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.config-item {
    padding: 0.75rem;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 8px;
    font-size: 0.9rem;
}

/* 使用者資料網格 */
.user-data-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
    font-size: 0.9rem;
}

/* 文字顏色 */
.text-success { color: #155724; }
.text-error { color: #721c24; }

/* 診斷提示 */
.diagnostic-tips h4 {
    margin: 1rem 0 0.5rem 0;
    color: #333;
}

.diagnostic-tips ul {
    margin: 0.5rem 0 1rem 1.5rem;
    padding: 0;
}

.diagnostic-tips li {
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

 /* 使用者表格樣式 */
 .users-table-container {
     max-height: 400px;
     overflow-y: auto;
     border: 1px solid #ddd;
     border-radius: 8px;
     background: white;
 }
 
 .users-table {
     width: 100%;
     border-collapse: collapse;
     margin: 0;
     font-size: 0.9rem;
 }
 
 .users-table thead {
     background: #f8f9fa;
     position: sticky;
     top: 0;
     z-index: 10;
 }
 
 .users-table th,
 .users-table td {
     padding: 0.75rem;
     text-align: left;
     border-bottom: 1px solid #ddd;
     vertical-align: middle;
 }
 
 .users-table th {
     font-weight: 600;
     color: #333;
     border-bottom: 2px solid #ddd;
 }
 
 .users-table tbody tr:hover {
     background: rgba(200, 16, 46, 0.05);
 }
 
 .btn-use-account {
     background: #28a745;
     color: white;
     border: none;
     padding: 0.5rem 1rem;
     border-radius: 4px;
     cursor: pointer;
     font-size: 0.85rem;
     font-weight: 500;
     transition: all 0.2s;
 }
 
 .btn-use-account:hover {
     background: #218838;
     transform: translateY(-1px);
     box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
 }

 /* 響應式設計 */
 @media (max-width: 768px) {
     .config-grid {
         grid-template-columns: 1fr;
     }
     
     .user-data-grid {
         grid-template-columns: 1fr;
     }
     
     .users-table-container {
         overflow-x: auto;
     }
     
     .users-table {
         min-width: 600px;
     }
     
     .users-table th,
     .users-table td {
         padding: 0.5rem;
         font-size: 0.8rem;
     }
 }
 </style>

<script>
function useAccount(username) {
    // 將帳號填入測試欄位
    document.getElementById('test_username').value = username;
    
    // 聚焦到密碼欄位
    document.getElementById('test_password').focus();
    
    // 顯示提示訊息
    showMessage('已填入帳號：' + username + '，請輸入密碼', 'info');
}

function showMessage(message, type) {
    // 移除現有的提示訊息
    const existingMessage = document.querySelector('.temp-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // 創建新的提示訊息
    const messageDiv = document.createElement('div');
    messageDiv.className = 'temp-message alert alert-' + (type === 'info' ? 'success' : type);
    messageDiv.style.cssText = 'margin: 1rem 0; padding: 0.75rem; border-radius: 8px; animation: fadeInOut 3s forwards;';
    messageDiv.innerHTML = '<div class="alert-content"><span class="alert-icon">ℹ️</span><span>' + message + '</span></div>';
    
    // 插入到表單前面
    const form = document.querySelector('.auth-form');
    form.parentNode.insertBefore(messageDiv, form);
    
    // 3秒後自動移除
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 3000);
}

// CSS動畫
const style = document.createElement('style');
style.textContent = `
@keyframes fadeInOut {
    0% { opacity: 0; transform: translateY(-10px); }
    20% { opacity: 1; transform: translateY(0); }
    80% { opacity: 1; transform: translateY(0); }
    100% { opacity: 0; transform: translateY(-10px); }
}
`;
document.head.appendChild(style);
</script> 