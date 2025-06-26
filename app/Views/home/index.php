<div class="home-container">
    <div class="hero-section">
        <h1 class="hero-title">歡迎使用讀書共和國員工服務網</h1>
        <p class="hero-subtitle">專為讀書共和國員工打造的便民服務平台，讓工作更加便利高效</p>
        
        <?php if (!$isLoggedIn): ?>
        <div class="hero-actions">
            <a href="<?php echo $baseUrl; ?>/login" class="btn btn-primary">立即登入</a>
            <a href="<?php echo $baseUrl; ?>/register" class="btn btn-secondary">註冊帳號</a>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($isLoggedIn): ?>
    <div class="dashboard-section">
        <h2>個人儀表板</h2>
        <div class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <h3>快速連結</h3>
                </div>
                <div class="card-body">
                    <div class="quick-links">
                        <a href="<?php echo $baseUrl; ?>/mail/request" class="quick-link">
                            <i class="icon">📮</i>
                            <span>寄件登記</span>
                        </a>
                        <a href="<?php echo $baseUrl; ?>/mail/records" class="quick-link">
                            <i class="icon">📋</i>
                            <span>寄件記錄</span>
                        </a>
                        <a href="<?php echo $baseUrl; ?>/announcements" class="quick-link">
                            <i class="icon">📢</i>
                            <span>最新公告</span>
                        </a>
                        <a href="<?php echo $baseUrl; ?>/booking/meeting-room" class="quick-link">
                            <i class="icon">🏢</i>
                            <span>會議室</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>使用者資訊</h3>
                </div>
                <div class="card-body">
                    <div class="user-profile">
                        <p><strong>使用者名稱：</strong> <?php echo htmlspecialchars($currentUser['name'] ?? $currentUser['username']); ?></p>
                        <p><strong>帳號：</strong> <?php echo htmlspecialchars($currentUser['username']); ?></p>
                        <p><strong>角色：</strong> <?php echo $currentUser['role'] === 'admin' ? '管理員' : '一般使用者'; ?></p>
                        <p><strong>登入時間：</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="announcements-section">
        <h2>最新公告</h2>
        <?php if (!empty($announcements)): ?>
            <div class="announcements-list">
                <?php foreach ($announcements as $announcement): ?>
                <div class="announcement-item">
                    <div class="announcement-header">
                        <h3 class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                        <span class="announcement-date"><?php echo date('Y-m-d', strtotime($announcement['created_at'])); ?></span>
                    </div>
                    <div class="announcement-content">
                        <?php echo nl2br(htmlspecialchars(mb_strimwidth($announcement['content'], 0, 200, '...'))); ?>
                    </div>
                    <a href="<?php echo $baseUrl; ?>/announcements" class="read-more">查看詳情</a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center">
                <a href="<?php echo $baseUrl; ?>/announcements" class="btn btn-outline">查看所有公告</a>
            </div>
        <?php else: ?>
            <div class="no-announcements">
                <p>目前暫無公告。</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="features-section">
        <h2>系統功能</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">📢</div>
                <h3>公告查詢</h3>
                <p>即時查看讀書共和國最新公告、假日資訊及員工手冊</p>
            </div>
            
            <?php if ($isLoggedIn): ?>
            <div class="feature-card">
                <div class="feature-icon">📝</div>
                <h3>表單申請</h3>
                <p>線上處理各類表單申請，包含郵務、請假、出差、採購等服務</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>資源預約</h3>
                <p>便利的會議室、設備預約系統</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">💡</div>
                <h3>操作指引</h3>
                <p>詳細的系統操作說明，助您快速上手各項功能</p>
            </div>
            <?php endif; ?>
            
            <div class="feature-card">
                <div class="feature-icon">🏢</div>
                <h3>公司資訊</h3>
                <p>查看讀書共和國簡介、樓層圖及相關聯絡資訊</p>
            </div>
        </div>
    </div>
</div> 