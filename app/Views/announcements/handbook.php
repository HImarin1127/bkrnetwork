<div class="handbook-container">
    <div class="page-header">
        <h1>📚 員工手冊</h1>
        <p class="page-subtitle">讀書共和國員工工作指南與規章制度</p>
    </div>

    <?php if (!empty($handbook)): ?>
        <div class="handbook-navigation">
            <h3>目錄</h3>
            <ul class="handbook-toc">
                <?php foreach ($handbook as $index => $section): ?>
                <li><a href="#section-<?php echo $index; ?>"><?php echo htmlspecialchars($section['title']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="handbook-content">
            <?php foreach ($handbook as $index => $section): ?>
            <div id="section-<?php echo $index; ?>" class="handbook-section glass-card">
                <h2 class="section-title"><?php echo htmlspecialchars($section['title']); ?></h2>
                <div class="section-content">
                    <?php echo nl2br(htmlspecialchars($section['content'])); ?>
                </div>
                <?php if (isset($section['updated_at'])): ?>
                <div class="section-meta">
                    <small>最後更新：<?php echo date('Y-m-d', strtotime($section['updated_at'])); ?></small>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-content glass-card">
            <div class="no-content-icon">📖</div>
            <h3>員工手冊建置中</h3>
            <p>員工手冊內容正在整理中，敬請期待。</p>
        </div>
    <?php endif; ?>
</div>

<style>
.handbook-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
}

.page-header h1 {
    color: #6b46c1;
    margin-bottom: 10px;
}

.page-subtitle {
    color: #6b7280;
    font-size: 1.1rem;
}

.handbook-navigation {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 30px;
}

.handbook-navigation h3 {
    color: #6b46c1;
    margin-bottom: 15px;
}

.handbook-toc {
    list-style: none;
    padding: 0;
    margin: 0;
}

.handbook-toc li {
    margin-bottom: 8px;
}

.handbook-toc a {
    color: #374151;
    text-decoration: none;
    padding: 8px 15px;
    display: block;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.handbook-toc a:hover {
    background: rgba(107, 70, 193, 0.1);
    color: #6b46c1;
}

.handbook-content {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.handbook-section {
    padding: 30px;
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.section-title {
    color: #2d3748;
    margin-bottom: 20px;
    font-size: 1.5rem;
    border-bottom: 2px solid rgba(107, 70, 193, 0.2);
    padding-bottom: 10px;
}

.section-content {
    color: #374151;
    line-height: 1.7;
    font-size: 1rem;
    margin-bottom: 15px;
}

.section-meta {
    text-align: right;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 15px;
}

.section-meta small {
    color: #6b7280;
}

.no-content {
    text-align: center;
    padding: 60px 40px;
}

.no-content-icon {
    font-size: 4rem;
    margin-bottom: 20px;
}

.no-content h3 {
    color: #6b46c1;
    margin-bottom: 10px;
}

.no-content p {
    color: #6b7280;
}

@media (max-width: 768px) {
    .handbook-container {
        padding: 15px;
    }
    
    .handbook-navigation,
    .handbook-section {
        padding: 20px;
    }
    
    .section-title {
        font-size: 1.3rem;
    }
}
</style> 