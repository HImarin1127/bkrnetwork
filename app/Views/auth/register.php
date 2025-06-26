<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>註冊讀書共和國員工帳號</h2>
            <p>建立您的員工帳號以使用內部便民服務</p>
        </div>
        
        <div class="auth-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($errors) && is_array($errors)): ?>
                <div class="alert alert-error">
                    <ul class="error-list">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <p><a href="<?php echo $baseUrl; ?>/login">立即登入</a></p>
                </div>
            <?php else: ?>
            
            <form method="POST" action="<?php echo $baseUrl; ?>/register" class="auth-form">
                <div class="form-group">
                    <label for="username">帳號</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($formData['username'] ?? ''); ?>"
                           placeholder="請輸入帳號">
                </div>
                
                <div class="form-group">
                    <label for="name">姓名</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>"
                           placeholder="請輸入您的真實姓名">
                </div>
                
                <div class="form-group">
                    <label for="email">電子郵件</label>
                    <input type="email" id="email" name="email" required 
                           value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                           placeholder="請輸入電子郵件地址">
                </div>
                
                <div class="form-group">
                    <label for="password">密碼</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="請輸入密碼（至少6個字元）">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">確認密碼</label>
                    <input type="password" id="confirm_password" name="confirm_password" required 
                           placeholder="請再次輸入密碼">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-full">註冊</button>
                </div>
            </form>
            
            <?php endif; ?>
        </div>
        
        <div class="auth-footer">
            <p>已有帳號？ <a href="<?php echo $baseUrl; ?>/login">立即登入</a></p>
            <p><a href="<?php echo $baseUrl; ?>/">返回首頁</a></p>
        </div>
    </div>
</div> 