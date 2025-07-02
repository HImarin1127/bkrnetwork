<div class="page-header">
    <div class="header-content">
        <div class="header-info">
            <h1 class="page-title">📢 最新公告中心</h1>
            <p class="page-subtitle">掌握讀書共和國第一手資訊與重要通知</p>
        </div>
        <?php if ($canManageAnnouncements): ?>
        <div class="header-actions">
            <a href="<?php echo $baseUrl; ?>/admin/announcements/create" class="btn btn-primary">
                <span class="btn-icon">➕</span>
                <span class="btn-text">新增公告</span>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="announcement-stats content-card">
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-icon">📊</div>
            <div class="stat-info">
                <span class="stat-number"><?php echo count($announcements); ?></span>
                <span class="stat-label">則公告</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">⏰</div>
            <div class="stat-info">
                <span class="stat-number"><?php echo !empty($announcements) ? date('m/d', strtotime($announcements[0]['created_at'])) : '--'; ?></span>
                <span class="stat-label">最新更新</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">🔥</div>
            <div class="stat-info">
                <span class="stat-number"><?php echo array_filter($announcements, function($a) { return $a['type'] === 'general'; }) ? count(array_filter($announcements, function($a) { return $a['type'] === 'general'; })) : 0; ?></span>
                <span class="stat-label">重要公告</span>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($announcements)): ?>
    <div class="announcements-grid">
        <?php foreach ($announcements as $index => $announcement): ?>
        <div class="announcement-card <?php echo $index === 0 ? 'featured' : ''; ?>">
            <div class="card-header">
                <div class="announcement-meta">
                    <?php 
                    $typeConfig = [
                        'general' => ['icon' => '📌', 'label' => '一般公告', 'class' => 'type-general'],
                        'holiday' => ['icon' => '🎉', 'label' => '假日公告', 'class' => 'type-holiday'],
                        'handbook' => ['icon' => '📚', 'label' => '員工手冊', 'class' => 'type-handbook']
                    ];
                    $config = $typeConfig[$announcement['type']] ?? $typeConfig['general'];
                    ?>
                    <span class="type-badge <?php echo $config['class']; ?>">
                        <?php echo $config['icon']; ?> <?php echo $config['label']; ?>
                    </span>
                    <span class="announcement-date">
                        <span class="date-icon">📅</span>
                        <time datetime="<?php echo $announcement['created_at']; ?>">
                            <?php echo date('Y年m月d日', strtotime($announcement['created_at'])); ?>
                        </time>
                    </span>
                </div>
            </div>
            
            <div class="card-body">
                <h3 class="announcement-title">
                    <?php if ($index === 0): ?>
                        <span class="hot-badge">🔥 HOT</span>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($announcement['title']); ?>
                </h3>
                
                <div class="announcement-content">
                    <?php 
                    $content = htmlspecialchars($announcement['content']);
                    $preview = mb_strlen($content) > 120 ? mb_substr($content, 0, 120) . '...' : $content;
                    echo nl2br($preview);
                    ?>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="announcement-actions">
                    <button class="btn btn-outline btn-sm" onclick="toggleContent(<?php echo $announcement['id']; ?>)">
                        <span class="btn-icon">👁️</span>
                        <span class="btn-text">查看詳情</span>
                    </button>
                    <?php if (mb_strlen($announcement['content']) > 120): ?>
                        <button class="btn btn-secondary btn-sm" onclick="expandContent(<?php echo $announcement['id']; ?>)">
                            <span class="btn-icon">📖</span>
                            <span class="btn-text">展開全文</span>
                        </button>
                    <?php endif; ?>
                </div>
                
                <div class="announcement-reading-time">
                    <span class="reading-icon">⏱️</span>
                    <span class="reading-text"><?php echo ceil(mb_strlen($announcement['content']) / 200); ?> 分鐘閱讀</span>
                </div>
            </div>
            
            <!-- 隱藏的完整內容 -->
            <div id="full-content-<?php echo $announcement['id']; ?>" class="full-content" style="display: none;">
                <div class="full-content-header">
                    <h4>📄 完整內容</h4>
                    <button class="close-btn" onclick="collapseContent(<?php echo $announcement['id']; ?>)">✕</button>
                </div>
                <div class="full-content-body">
                    <?php echo nl2br(htmlspecialchars($announcement['content'])); ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="content-card">
        <div class="empty-state">
            <div class="empty-illustration">
                <div class="empty-icon">📢</div>
                <div class="empty-circles">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                    <div class="circle circle-3"></div>
                </div>
            </div>
            <h3 class="empty-title">📭 目前暫無最新公告</h3>
            <p class="empty-message">我們會定期發布重要資訊與通知，請稍後再來查看</p>
            <div class="empty-actions">
                <button class="btn btn-primary" onclick="location.reload()">
                    <span>🔄</span> 重新整理
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="content-card">
    <div class="quick-nav">
        <div class="nav-header">
            <h3>🚀 快速導航</h3>
            <p>探索更多公告類型與資源</p>
        </div>
        <div class="nav-grid">
            <a href="<?php echo $baseUrl; ?>announcements/holidays" class="nav-card">
                <div class="nav-icon">🎉</div>
                <div class="nav-content">
                    <h4>假日資訊</h4>
                    <p>查看國定假日與連假安排</p>
                </div>
                <div class="nav-arrow">→</div>
            </a>
            
            <a href="<?php echo $baseUrl; ?>announcements/handbook" class="nav-card">
                <div class="nav-icon">📚</div>
                <div class="nav-content">
                    <h4>員工手冊</h4>
                    <p>重要規章制度與操作指南</p>
                </div>
                <div class="nav-arrow">→</div>
            </a>
            
            <a href="<?php echo $baseUrl; ?>/" class="nav-card">
                <div class="nav-icon">🏠</div>
                <div class="nav-content">
                    <h4>返回首頁</h4>
                    <p>回到主要服務選單</p>
                </div>
                <div class="nav-arrow">→</div>
            </a>
        </div>
    </div>
</div>

<style>
/* 頁面標題區域 */
.page-header {
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-info {
    flex: 1;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
}

.btn-primary {
    background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(200, 16, 46, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(200, 16, 46, 0.4);
}

.btn-icon {
    font-size: 1rem;
}

.btn-text {
    font-size: 0.9rem;
}

/* 響應式設計 */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .header-actions {
        width: 100%;
        justify-content: center;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}

/* 統計卡片 */
.announcement-stats {
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(200,16,46,0.02);
    border-radius: 16px;
    border: 1px solid rgba(200,16,46,0.1);
    transition: all 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(200,16,46,0.1);
}

.stat-icon {
    font-size: 2rem;
    opacity: 0.8;
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    color: #C8102E;
    line-height: 1;
}

.stat-label {
    font-size: 0.85rem;
    color: #666;
    margin-top: 0.25rem;
}

/* 公告網格 */
.announcements-grid {
    display: grid;
    gap: 2rem;
    margin-bottom: 2rem;
}

.announcement-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(200,16,46,0.1);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.announcement-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}

.announcement-card.featured {
    background: linear-gradient(135deg, rgba(200,16,46,0.05) 0%, rgba(255,255,255,0.95) 100%);
    border: 2px solid rgba(200,16,46,0.2);
}

.announcement-card.featured::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #C8102E 0%, #8B0000 100%);
}

/* 卡片標題區 */
.card-header {
    margin-bottom: 1.5rem;
}

.announcement-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.type-general {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1565c0;
    border: 1px solid #90caf9;
}

.type-holiday {
    background: linear-gradient(135deg, #fce4ec 0%, #f8bbd9 100%);
    color: #c2185b;
    border: 1px solid #f48fb1;
}

.type-handbook {
    background: linear-gradient(135deg, #e8f5e8 0%, #c8e6c9 100%);
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}

.announcement-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
    font-size: 0.9rem;
}

.date-icon {
    font-size: 1rem;
}

/* 卡片內容區 */
.card-body {
    margin-bottom: 1.5rem;
}

.announcement-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 1rem 0;
    line-height: 1.4;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.hot-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
    color: white;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.announcement-content {
    color: #4a5568;
    line-height: 1.6;
    margin-bottom: 1rem;
}

/* 卡片底部 */
.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(200,16,46,0.1);
    flex-wrap: wrap;
}

.announcement-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.announcement-reading-time {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #666;
    font-size: 0.85rem;
}

.reading-icon {
    font-size: 1rem;
}

/* 完整內容 */
.full-content {
    margin-top: 1.5rem;
    padding: 1.5rem;
    background: rgba(200,16,46,0.02);
    border-radius: 12px;
    border: 1px solid rgba(200,16,46,0.1);
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.full-content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(200,16,46,0.1);
}

.full-content-header h4 {
    margin: 0;
    color: #C8102E;
    font-size: 1.1rem;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: #666;
    transition: color 0.3s ease;
}

.close-btn:hover {
    color: #C8102E;
}

.full-content-body {
    color: #4a5568;
    line-height: 1.6;
}

/* 空狀態 */
.empty-illustration {
    position: relative;
    margin-bottom: 2rem;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.empty-circles {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.circle {
    position: absolute;
    border: 2px dashed rgba(200,16,46,0.2);
    border-radius: 50%;
    animation: rotate 10s linear infinite;
}

.circle-1 {
    width: 60px;
    height: 60px;
    top: -30px;
    left: -30px;
}

.circle-2 {
    width: 90px;
    height: 90px;
    top: -45px;
    left: -45px;
    animation-delay: -3s;
}

.circle-3 {
    width: 120px;
    height: 120px;
    top: -60px;
    left: -60px;
    animation-delay: -6s;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* 快速導航 */
.quick-nav {
    padding: 0;
}

.nav-header {
    text-align: center;
    margin-bottom: 2rem;
    padding: 2rem 2rem 0 2rem;
}

.nav-header h3 {
    font-size: 1.5rem;
    color: #C8102E;
    margin: 0 0 0.5rem 0;
    font-weight: 700;
}

.nav-header p {
    color: #666;
    margin: 0;
}

.nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 0;
}

.nav-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 2rem;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    border-right: 1px solid rgba(200,16,46,0.1);
    border-bottom: 1px solid rgba(200,16,46,0.1);
}

.nav-card:last-child {
    border-right: none;
}

.nav-card:hover {
    background: rgba(200,16,46,0.02);
    transform: translateX(5px);
}

.nav-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}

.nav-content h4 {
    margin: 0 0 0.5rem 0;
    color: #2d3748;
    font-weight: 600;
}

.nav-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.nav-arrow {
    font-size: 1.5rem;
    color: #C8102E;
    margin-left: auto;
    transition: transform 0.3s ease;
}

.nav-card:hover .nav-arrow {
    transform: translateX(5px);
}

/* 響應式設計 */
@media (max-width: 768px) {
    .announcement-meta {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .card-footer {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .nav-grid {
        grid-template-columns: 1fr;
    }
    
    .nav-card {
        border-right: none;
    }
 }
 </style>
 
 <script>
 // 展開/摺疊完整內容
 function expandContent(id) {
     const fullContent = document.getElementById('full-content-' + id);
     if (fullContent) {
         fullContent.style.display = 'block';
         fullContent.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
     }
 }
 
 function collapseContent(id) {
     const fullContent = document.getElementById('full-content-' + id);
     if (fullContent) {
         fullContent.style.display = 'none';
     }
 }
 
 function toggleContent(id) {
     const fullContent = document.getElementById('full-content-' + id);
     if (fullContent) {
         if (fullContent.style.display === 'none' || fullContent.style.display === '') {
             expandContent(id);
         } else {
             collapseContent(id);
         }
     }
 }
 
 // 頁面載入完成後的初始化
 document.addEventListener('DOMContentLoaded', function() {
     // 為統計數字添加計數動畫
     const statNumbers = document.querySelectorAll('.stat-number');
     statNumbers.forEach(stat => {
         const finalValue = parseInt(stat.textContent) || 0;
         if (!isNaN(finalValue) && finalValue > 0) {
             animateNumber(stat, 0, finalValue, 1000);
         }
     });
 });
 
 // 數字計數動畫
 function animateNumber(element, start, end, duration) {
     const startTime = performance.now();
     
     function update(currentTime) {
         const elapsed = currentTime - startTime;
         const progress = Math.min(elapsed / duration, 1);
         
         const current = Math.floor(start + (end - start) * easeOutCubic(progress));
         element.textContent = current;
         
         if (progress < 1) {
             requestAnimationFrame(update);
         }
     }
     
     requestAnimationFrame(update);
 }
 
 // 緩動函數
 function easeOutCubic(t) {
     return 1 - Math.pow(1 - t, 3);
 }
 </script> 