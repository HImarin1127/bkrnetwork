<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h2>登入讀書共和國員工服務網</h2>
            <p>請使用您的員工帳號密碼存取內部便民服務</p>
        </div>
        
        <div class="auth-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo $baseUrl; ?>/login" class="auth-form">
                <div class="form-group">
                    <label for="username">帳號</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                           placeholder="請輸入您的帳號">
                </div>
                
                <div class="form-group">
                    <label for="password">密碼</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="請輸入您的密碼">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-full">登入</button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p>還沒有帳號？ <a href="<?php echo $baseUrl; ?>/register">立即註冊</a></p>
            <p><a href="<?php echo $baseUrl; ?>/">返回首頁</a></p>
        </div>
    </div>
</div> 