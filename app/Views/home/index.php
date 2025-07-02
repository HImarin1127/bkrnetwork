<!-- 歡迎區塊 -->
<div class="hero-banner">
    <div class="hero-content">
        <div class="hero-logo">
            <div class="logo-main">
                <span class="logo-icon">📚</span>
                <span class="logo-text">讀書共和國</span>
            </div>
            <div class="logo-subtitle">員工服務平台</div>
        </div>
    </div>
</div>



<!-- 最新公告區塊 -->
<div class="announcements-showcase">
    <div class="section-header" style="margin-bottom: 1.5rem;">
        <h2 class="section-title" style="font-size: 1.8rem; margin-bottom: 0.3rem;">📢 最新公告</h2>
        <p class="section-subtitle" style="font-size: 0.9rem;">掌握第一手重要資訊</p>
    </div>
    
    <?php if (!empty($announcements)): ?>
        <div class="announcements-carousel">
            <?php foreach (array_slice($announcements, 0, 3) as $index => $announcement): ?>
            <div class="announcement-spotlight <?php echo $index === 0 ? 'featured' : ''; ?>">
                <div class="spotlight-header">
                    <div class="spotlight-meta">
                        <?php 
                        $typeConfig = [
                            'general' => ['icon' => '📌', 'label' => '重要公告', 'class' => 'meta-general'],
                            'holiday' => ['icon' => '🎉', 'label' => '假日資訊', 'class' => 'meta-holiday'],
                            'handbook' => ['icon' => '📚', 'label' => '員工手冊', 'class' => 'meta-handbook']
                        ];
                        $config = $typeConfig[$announcement['type']] ?? $typeConfig['general'];
                        ?>
                        <span class="meta-badge <?php echo $config['class']; ?>">
                            <?php echo $config['icon']; ?> <?php echo $config['label']; ?>
                        </span>
                        <?php if ($index === 0): ?>
                            <span class="featured-badge">🔥 最新</span>
                        <?php endif; ?>
                    </div>
                    <time class="spotlight-date" datetime="<?php echo $announcement['created_at']; ?>">
                        <?php echo date('m月d日', strtotime($announcement['created_at'])); ?>
                    </time>
                </div>
                
                <div class="spotlight-content">
                    <h3 class="spotlight-title"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                    <p class="spotlight-preview">
                        <?php echo htmlspecialchars(mb_strimwidth($announcement['content'], 0, 120, '...')); ?>
                    </p>
                </div>
                
                <div class="spotlight-footer">
                    <a href="<?php echo $baseUrl; ?>/announcements" class="spotlight-link">
                        <span>閱讀全文</span>
                        <span class="link-arrow">→</span>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="announcements-actions">
            <a href="<?php echo $baseUrl; ?>/announcements" class="btn btn-primary">
                <span>📰</span> 查看所有公告
            </a>
            <a href="<?php echo $baseUrl; ?>/announcements/holidays" class="btn btn-outline">
                <span>📅</span> 假日資訊
            </a>
            <a href="<?php echo $baseUrl; ?>/announcements/handbook" class="btn btn-outline">
                <span>📖</span> 員工手冊
            </a>
        </div>
    <?php else: ?>
        <div class="content-card">
            <div class="empty-announcements">
                <div class="empty-icon">📭</div>
                <h3 class="empty-title">目前暫無最新公告</h3>
                <p class="empty-message">我們會定期發布重要資訊，請稍後再來查看</p>
                <button class="btn btn-outline" onclick="location.reload()">
                    <span>🔄</span> 重新整理
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- 系統功能展示 -->
<div class="features-showcase">
    <div class="section-header">
        <h2 class="section-title" style="font-size: 1.8rem; margin-bottom: 0.3rem;">🚀 介面總覽</h2>
        <p class="section-subtitle" style="font-size: 0.9rem;">完整的企業服務解決方案</p>
    </div>
    
    <div class="features-grid">
        <!-- 公告查詢 -->
        <div class="feature-module">
            <div class="module-header">
                <div class="module-icon">📢</div>
                <div class="module-info">
                    <h3 class="module-title">公告查詢</h3>
                    <span class="module-badge public">公開存取</span>
                </div>
            </div>
            <div class="module-content">
                <p class="module-description">即時查看讀書共和國最新公告、假日資訊及員工手冊</p>
                <div class="module-features">
                    <span class="feature-tag">📰 最新公告</span>
                    <span class="feature-tag">🎉 假日資訊</span>
                    <span class="feature-tag">📚 員工手冊</span>
                </div>
            </div>
            <div class="module-footer">
                <a href="<?php echo $baseUrl; ?>/announcements" class="module-link">
                    <span>立即查看</span>
                    <span class="link-arrow">→</span>
                </a>
            </div>
        </div>
        
        <?php if ($isLoggedIn): ?>
        <!-- 表單申請 -->
        <div class="feature-module">
            <div class="module-header">
                <div class="module-icon">📝</div>
                <div class="module-info">
                    <h3 class="module-title">表單申請</h3>
                    <span class="module-badge member">會員專用</span>
                </div>
            </div>
            <div class="module-content">
                <p class="module-description">線上處理各類表單申請，包含郵務、請假、出差、採購等服務</p>
                <div class="module-features">
                    <span class="feature-tag">📮 郵務系統</span>
                    <span class="feature-tag">🏖️ 請假申請</span>
                    <span class="feature-tag">✈️ 出差申請</span>
                    <span class="feature-tag">🛒 設備採購</span>
                </div>
            </div>
            <div class="module-footer">
                <a href="<?php echo $baseUrl; ?>/forms/leave-request" class="module-link">
                    <span>開始申請</span>
                    <span class="link-arrow">→</span>
                </a>
            </div>
        </div>
        
        <!-- 資源預約 -->
        <div class="feature-module">
            <div class="module-header">
                <div class="module-icon">📅</div>
                <div class="module-info">
                    <h3 class="module-title">資源預約</h3>
                    <span class="module-badge member">會員專用</span>
                </div>
            </div>
            <div class="module-content">
                <p class="module-description">便利的會議室、設備預約系統，智慧排程避免衝突</p>
                <div class="module-features">
                    <span class="feature-tag">🏢 會議室</span>
                    <span class="feature-tag">💻 設備借用</span>
                </div>
            </div>
            <div class="module-footer">
                <a href="<?php echo $baseUrl; ?>/booking/meeting-room" class="module-link">
                    <span>立即預約</span>
                    <span class="link-arrow">→</span>
                </a>
            </div>
        </div>
        
        <!-- 操作指引 -->
        <div class="feature-module">
            <div class="module-header">
                <div class="module-icon">💡</div>
                <div class="module-info">
                    <h3 class="module-title">操作指引</h3>
                    <span class="module-badge member">會員專用</span>
                </div>
            </div>
            <div class="module-content">
                <p class="module-description">詳細的系統操作說明，助您快速上手各項功能</p>
                <div class="module-features">
                    <span class="feature-tag">🖥️ Windows</span>
                    <span class="feature-tag">🍎 Mac</span>
                    <span class="feature-tag">📧 Email</span>
                    <span class="feature-tag">🖨️ 印表機</span>
                </div>
            </div>
            <div class="module-footer">
                <a href="<?php echo $baseUrl; ?>/guides/email" class="module-link">
                    <span>查看指引</span>
                    <span class="link-arrow">→</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- 公司資訊 -->
        <div class="feature-module">
            <div class="module-header">
                <div class="module-icon">🏢</div>
                <div class="module-info">
                    <h3 class="module-title">公司資訊</h3>
                    <span class="module-badge public">公開存取</span>
                </div>
            </div>
            <div class="module-content">
                <p class="module-description">查看讀書共和國簡介、樓層圖及相關聯絡資訊</p>
                <div class="module-features">
                    <span class="feature-tag">📖 公司簡介</span>
                    <span class="feature-tag">🗺️ 樓層圖</span>
                    <span class="feature-tag">📞 聯絡資訊</span>
                    <span class="feature-tag">💾 NAS資源</span>
                </div>
            </div>
            <div class="module-footer">
                <a href="<?php echo $baseUrl; ?>/company" class="module-link">
                    <span>了解更多</span>
                    <span class="link-arrow">→</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* 英雄橫幅區塊 */
.hero-banner {
    position: relative;
    background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
    padding: 2rem 0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2rem;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
}

.hero-pattern {
    position: absolute;
    width: 200%;
    height: 200%;
    background: repeating-linear-gradient(
        45deg,
        rgba(255,255,255,0.05) 0px,
        rgba(255,255,255,0.05) 2px,
        transparent 2px,
        transparent 20px
    );
    animation: patternMove 30s linear infinite;
}

@keyframes patternMove {
    0% { transform: translate(-50%, -50%); }
    100% { transform: translate(-48%, -48%); }
}

.hero-circles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.circle {
    position: absolute;
    border: 2px solid rgba(255,255,255,0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.circle-1 {
    width: 200px;
    height: 200px;
    top: 10%;
    left: 10%;
    animation-delay: 0s;
}

.circle-2 {
    width: 150px;
    height: 150px;
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.circle-3 {
    width: 100px;
    height: 100px;
    bottom: 20%;
    left: 60%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.hero-content {
    text-align: center;
    color: white;
    padding: 0 2rem;
}

.hero-logo {
    margin: 0;
}

.logo-main {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.8rem;
    margin-bottom: 0.3rem;
}

.logo-icon {
    font-size: 2.2rem;
    display: inline-block;
    animation: bookFloating 3s ease-in-out infinite;
    transition: all 0.3s ease;
    cursor: pointer;
}

.logo-icon:hover {
    animation-play-state: paused;
    transform: scale(1.1) translateY(-5px);
    filter: drop-shadow(0 5px 15px rgba(200,16,46,0.3));
}

@keyframes bookFloating {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-8px);
    }
    100% {
        transform: translateY(0px);
    }
}

.logo-text {
    font-size: 2rem;
    font-weight: 700;
    letter-spacing: 1px;
}

.logo-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    letter-spacing: 0.5px;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    margin: 2rem 0;
    line-height: 1.2;
}

.title-line {
    display: block;
    font-size: 1.5rem;
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.title-highlight {
    display: block;
    font-size: 3.5rem;
    color: #FFD700;
    text-shadow: 0 0 30px rgba(255,215,0,0.5);
}

.hero-description {
    font-size: 1.3rem;
    margin-bottom: 3rem;
    opacity: 0.95;
    line-height: 1.6;
}

.hero-actions {
    margin-bottom: 3rem;
}

.btn-hero {
    font-size: 1.2rem;
    padding: 1.2rem 2.5rem;
    margin: 0 1rem;
    border-radius: 50px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    border: none;
    background: rgba(255,255,255,0.1);
    color: white;
    backdrop-filter: blur(10px);
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-hero:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.btn-hero:hover:before {
    left: 100%;
}

.btn-hero:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    text-decoration: none;
}

.hero-features {
    display: flex;
    justify-content: center;
    gap: 3rem;
    flex-wrap: wrap;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.1rem;
    opacity: 0.9;
}

.feature-icon {
    font-size: 1.5rem;
}

/* 區塊標題 */
.section-header {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 1rem 0;
}

.section-subtitle {
    font-size: 1.4rem;
    color: #666;
    margin: 0;
}





/* 公告展示區 */
.announcements-showcase {
    margin-bottom: 3rem;
}

.announcements-carousel {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.announcement-spotlight {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    border: 1px solid rgba(200,16,46,0.1);
    transition: all 0.3s ease;
}

.announcement-spotlight:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.announcement-spotlight.featured {
    background: linear-gradient(135deg, rgba(200,16,46,0.05) 0%, rgba(255,255,255,0.95) 100%);
    border: 2px solid rgba(200,16,46,0.2);
    position: relative;
}

.announcement-spotlight.featured::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
    border-radius: 20px 20px 0 0;
}

.spotlight-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.spotlight-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.meta-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.3rem 0.6rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 600;
}

.meta-general {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1565c0;
    border: 1px solid #90caf9;
}

.meta-holiday {
    background: linear-gradient(135deg, #fce4ec 0%, #f8bbd9 100%);
    color: #c2185b;
    border: 1px solid #f48fb1;
}

.meta-handbook {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

.featured-badge {
    background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
    color: white;
    padding: 0.3rem 0.7rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    animation: pulse 2s infinite;
}

.spotlight-date {
    color: #666;
    font-size: 0.9rem;
    font-weight: 500;
}

.spotlight-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 0.8rem 0;
    line-height: 1.4;
}

.spotlight-preview {
    color: #4a5568;
    line-height: 1.5;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.spotlight-footer {
    border-top: 1px solid rgba(200,16,46,0.1);
    padding-top: 0.8rem;
}

.spotlight-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #C8102E;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.spotlight-link:hover {
    color: #8B0000;
}

.link-arrow {
    font-size: 1.2rem;
    transition: transform 0.3s ease;
}

.spotlight-link:hover .link-arrow {
    transform: translateX(3px);
}

.announcements-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

/* 空狀態 */
.empty-announcements {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.empty-title {
    font-size: 1.5rem;
    color: #2d3748;
    margin-bottom: 1rem;
    font-weight: 600;
}

.empty-message {
    color: #666;
    margin-bottom: 2rem;
}

/* 功能展示區 */
.features-showcase {
    margin-bottom: 4rem;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 1.5rem;
    max-width: 1400px;
    margin: 0 auto;
}

.feature-module {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 1.8rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(200,16,46,0.1);
    transition: all 0.3s ease;
}

.feature-module:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.module-header {
    display: flex;
    align-items: center;
    gap: 1.2rem;
    margin-bottom: 1.2rem;
}

.module-icon {
    font-size: 2.2rem;
    opacity: 0.8;
}

.module-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
}

.module-badge {
    display: inline-block;
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.module-badge.public {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

.module-badge.member {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border: 1px solid #ffeaa7;
}

.module-description {
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 1.2rem;
}

.module-features {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    margin-bottom: 1.2rem;
}

.feature-tag {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    background: rgba(200,16,46,0.05);
    color: #C8102E;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid rgba(200,16,46,0.1);
}

.module-footer {
    border-top: 1px solid rgba(200,16,46,0.1);
    padding-top: 1.2rem;
}

.module-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #C8102E;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.module-link:hover {
    color: #8B0000;
}

.module-link:hover .link-arrow {
    transform: translateX(5px);
}

/* 響應式設計 */
@media (max-width: 1200px) {
    .features-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.2rem;
    }
}

@media (max-width: 768px) {
    .features-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .feature-module {
        padding: 1.5rem;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .title-highlight {
        font-size: 2.5rem;
    }
    
    .hero-features {
        gap: 1.5rem;
    }
    
    .announcements-carousel {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .announcement-spotlight {
        padding: 1rem;
    }
    
    .announcements-actions {
        flex-direction: column;
        align-items: center;
        gap: 0.8rem;
    }
}

@media (max-width: 480px) {
    .hero-content {
        padding: 0 1rem;
    }
    
    .btn-hero {
        display: block;
        margin: 0.5rem 0;
    }
    
    .hero-features {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>