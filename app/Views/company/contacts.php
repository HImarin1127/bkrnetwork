<?php
// 引入必要的 JavaScript 和 CSS
$baseUrl = isset($baseUrl) ? $baseUrl : '';
?>

<div class="contacts-container">
    <div class="page-header">
        <h1>聯絡資訊</h1>
        <p class="page-subtitle">讀書共和國各部門聯絡方式與辦公資訊</p>
    </div>

    <div class="contacts-content">
        <!-- 分機目錄卡片 -->
        <div class="extension-directory glass-card">
            <h2>分機目錄</h2>
            
            <!-- Tabbed Interface -->
            <div class="extension-tabs">
                <button class="ext-tab active" data-tab="summary">分機總表</button>
                <button class="ext-tab" data-tab="floor-plans">樓層分機圖</button>
                <button class="ext-tab" data-tab="mobile-contacts">手機聯絡資訊</button>
            </div>

            <div class="extension-content">
                <!-- Tab 1: 分機總表 -->
                <div class="ext-panel active" id="tab-summary">
                    <div class="extension-image-container">
                        <?php 
                        $imagePath = __DIR__ . '/../../../assets/images/extension-table-2025.jpg';
                        $imageUrl = $baseUrl . '/assets/images/extension-table-2025.jpg';
                        if (file_exists($imagePath)): 
                        ?>
                            <div class="extension-image-wrapper">
                                <a href="<?php echo $imageUrl; ?>" target="_blank" title="點擊在新分頁中開啟大圖">
                                    <img src="<?php echo $imageUrl; ?>" alt="讀書共和國分機表" class="extension-table-image">
                                </a>
                                <div class="image-overlay">
                                    <div class="image-info">
                                        <h4>📞 分機表 (點擊圖片可開新分頁檢視)</h4>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="no-image-fallback">
                                <h4>分機總表尚未上傳</h4>
                                <p>請將圖片儲存至 <code>assets/images/extension-table-2025.jpg</code></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tab 2: 樓層分機圖 -->
                <div class="ext-panel" id="tab-floor-plans">
                    <div class="floor-plan-container">
                        <div class="floor-plan-tabs">
                            <button class="floor-plan-tab active" data-image="108-2-9FEXT.png">108-2 9F</button>
                            <button class="floor-plan-tab" data-image="108-4-5F-8FEXT.png">108-4 5F&8F</button>
                            <button class="floor-plan-tab" data-image="108-3-8FEXT.png">108-3 8F</button>
                            <button class="floor-plan-tab" data-image="108-3-6FEXT.png">108-3 6F</button>
                            <button class="floor-plan-tab" data-image="108-3-3FEXT.png">108-3 3F</button>
                            <button class="floor-plan-tab" data-image="OUTSIDE.png">集團外單位</button>
                        </div>
                        <div class="floor-plan-display">
                            <a href="<?php echo $baseUrl; ?>/assets/images/108-2-9FEXT.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                                <img src="<?php echo $baseUrl; ?>/assets/images/108-2-9FEXT.png" alt="樓層分機圖" id="floorPlanImage">
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: 手機聯絡資訊 -->
                <div class="ext-panel" id="tab-mobile-contacts">
                    <div class="floor-plan-container">
                        <div class="mobile-plan-tabs">
                            <button class="mobile-plan-tab active" data-image="108-2-9FEXTMOBILE.png">108-2 9F</button>
                            <button class="mobile-plan-tab" data-image="108-4-5F-8FEXTMOBILE.png">108-4 5F&8F</button>
                            <button class="mobile-plan-tab" data-image="108-3-8FEXTMOBILE.png">108-3 8F</button>
                            <button class="mobile-plan-tab" data-image="108-3-6FEXTMOBILE.png">108-3 6F</button>
                            <button class="mobile-plan-tab" data-image="108-3-3FEXTMOBILE.png">108-3 3F</button>
                            <button class="mobile-plan-tab" data-image="OUTSIDEEXTMOBILE.png">集團外單位</button>
                        </div>
                        <div class="mobile-plan-display">
                            <a href="<?php echo $baseUrl; ?>/assets/images/108-2-9FEXTMOBILE.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                                <img src="<?php echo $baseUrl; ?>/assets/images/108-2-9FEXTMOBILE.png" alt="手機版聯絡資訊" id="mobilePlanImage">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* 基本樣式 */
.contacts-container {
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
    font-size: 2.5rem;
}

.page-subtitle {
    color: #6b7280;
    font-size: 1.1rem;
}

.contacts-content {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

/* 玻璃擬態卡片效果 */
.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.glass-card:hover {
    transform: translateY(-5px);
}

.glass-card h2 {
    color: #6b46c1;
    margin-bottom: 25px;
    font-size: 1.8rem;
}

/* 搜尋欄位樣式 */
.search-bar {
    position: relative;
    margin-bottom: 20px;
}

.search-bar input {
    width: 100%;
    padding: 12px 40px 12px 15px;
    border: 1px solid rgba(107, 70, 193, 0.2);
    border-radius: 8px;
    font-size: 1rem;
    background: rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.search-bar input:focus {
    outline: none;
    border-color: #6b46c1;
    box-shadow: 0 0 0 3px rgba(107, 70, 193, 0.2);
}

.clear-search {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 5px;
    display: none;
}

.clear-search:hover {
    color: #4b5563;
}

/* 資訊網格樣式 */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.info-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.info-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.info-details h4 {
    color: #6b46c1;
    margin-bottom: 5px;
    font-size: 1rem;
}

.info-details p {
    color: #374151;
    margin: 0;
}

/* 部門列表樣式 */
.department-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.department-item {
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.05);
    padding: 25px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.department-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.dept-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 10px;
}

.dept-header h3 {
    color: #6b46c1;
    margin: 0;
    font-size: 1.3rem;
    flex: 1;
}

.dept-location {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 5px;
}

.dept-building {
    background: #4a5568;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.9rem;
}

.dept-floor {
    background: #6b46c1;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.9rem;
}

/* 分機目錄樣式 */
.extension-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.extension-item {
    padding: 15px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.extension-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.extension-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
}

.employee-name {
    color: #6b46c1;
    font-weight: 500;
}

.extension-number {
    color: #4b5563;
}

.extension-description {
    color: #6b7280;
    font-size: 0.9rem;
    margin-top: 5px;
}

/* 辦公時間和緊急聯絡樣式 */
.hours-info,
.emergency-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.hours-item,
.emergency-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
}

.hours-item:hover,
.emergency-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.hours-icon,
.emergency-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

/* 響應式設計 */
@media (max-width: 768px) {
    .contacts-container {
        padding: 10px;
    }

    .page-header h1 {
        font-size: 2rem;
    }

    .glass-card {
        padding: 20px;
    }

    .info-grid,
    .department-list,
    .extension-list,
    .hours-info,
    .emergency-grid {
        grid-template-columns: 1fr;
    }

    .info-item,
    .department-item,
    .extension-item,
    .hours-item,
    .emergency-item {
        padding: 15px;
    }

    .dept-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .dept-location {
        flex-direction: row;
        gap: 10px;
        margin-top: 5px;
    }

    .extension-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}

/* 分機表圖片樣式 */
.extension-image-container {
    margin-bottom: 20px;
}

.extension-image-wrapper {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    cursor: pointer;
}

.extension-image-wrapper:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.extension-table-image {
    width: 100%;
    height: auto;
    display: block;
    transition: transform 0.3s ease;
}

.extension-image-wrapper:hover .extension-table-image {
    transform: scale(1.02);
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.extension-image-wrapper:hover .image-overlay {
    opacity: 1;
}

.image-info {
    text-align: center;
    color: white;
    background: rgba(0, 0, 0, 0.7);
    padding: 15px 20px;
    border-radius: 8px;
    backdrop-filter: blur(5px);
}

.image-info h4 {
    margin: 0 0 5px 0;
    font-size: 1.1rem;
}

.image-info p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.9;
}

/* 分機表缺失時的備用顯示樣式 */
.no-image-fallback {
    background: rgba(255, 249, 196, 0.3);
    border: 2px dashed rgba(251, 191, 36, 0.5);
    border-radius: 10px;
    padding: 30px;
    text-align: center;
    color: #92400e;
}

.fallback-content {
    max-width: 500px;
    margin: 0 auto;
}

.fallback-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.no-image-fallback h4 {
    color: #92400e;
    margin-bottom: 10px;
    font-size: 1.3rem;
}

.no-image-fallback p {
    margin-bottom: 20px;
    line-height: 1.6;
}

.no-image-fallback code {
    background: rgba(251, 191, 36, 0.2);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}

.upload-instructions {
    background: rgba(255, 255, 255, 0.5);
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
    text-align: left;
}

.upload-instructions p {
    margin: 0 0 10px 0;
    font-weight: bold;
}

.upload-instructions ol {
    margin: 10px 0;
    padding-left: 20px;
}

.upload-instructions li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.show-text-btn {
    background: #6b46c1;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 15px;
}

.show-text-btn:hover {
    background: #553c9a;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(107, 70, 193, 0.3);
}

/* 文字版分機目錄標題樣式 */
.text-directory-header {
    text-align: center;
    margin-bottom: 25px;
    padding: 20px;
    background: rgba(107, 70, 193, 0.1);
    border-radius: 8px;
    border: 1px solid rgba(107, 70, 193, 0.2);
}

.text-directory-header h3 {
    color: #6b46c1;
    margin: 0 0 10px 0;
    font-size: 1.5rem;
}

.directory-description {
    color: #6b7280;
    margin: 0;
    font-size: 1rem;
}

/* 無資料提示樣式 */
.no-data {
    text-align: center;
    padding: 20px;
    color: #6b7280;
    font-style: italic;
}

/* 搜尋結果隱藏效果 */
.department-item.hidden,
.extension-item.hidden {
    display: none;
}

/* Extension Directory Tabs */
.extension-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid rgba(107, 70, 193, 0.2);
}

.ext-tab {
    padding: 10px 20px;
    cursor: pointer;
    border: none;
    background: transparent;
    color: #6b7280;
    font-size: 1rem;
    font-weight: 500;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
}

.ext-tab.active, .ext-tab:hover {
    color: #6b46c1;
    border-bottom-color: #6b46c1;
}

.ext-panel {
    display: none;
}

.ext-panel.active {
    display: block;
    animation: fadeIn 0.5s;
}

.floor-plan-container {
    margin-top: 1rem;
}

.floor-plan-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}

.floor-plan-tab {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 20px;
    background-color: #f9f9f9;
    cursor: pointer;
    transition: all 0.3s ease;
}

.floor-plan-tab.active, .floor-plan-tab:hover {
    background-color: #6b46c1;
    color: white;
    border-color: #6b46c1;
}

.floor-plan-display {
    text-align: center;
}

.floor-plan-display img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.mobile-plan-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}

.mobile-plan-tab {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 20px;
    background-color: #f9f9f9;
    cursor: pointer;
    transition: all 0.3s ease;
}

.mobile-plan-tab.active, .mobile-plan-tab:hover {
    background-color: #6b46c1;
    color: white;
    border-color: #6b46c1;
}

.mobile-plan-display {
    text-align: center;
}

.mobile-plan-display img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Extension Directory Tabs
    const extTabs = document.querySelectorAll('.ext-tab');
    const extPanels = document.querySelectorAll('.ext-panel');

    extTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            extTabs.forEach(t => t.classList.remove('active'));
            extPanels.forEach(p => p.classList.remove('active'));
            
            tab.classList.add('active');
            document.getElementById('tab-' + tab.dataset.tab).classList.add('active');
        });
    });

    // Floor Plan Tabs
    const floorPlanTabs = document.querySelectorAll('.floor-plan-tab');
    const floorPlanImage = document.getElementById('floorPlanImage');
    const baseUrl = '<?php echo $baseUrl; ?>';

    if (floorPlanTabs.length > 0 && floorPlanImage) {
        floorPlanTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                floorPlanTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const newImage = tab.dataset.image;
                floorPlanImage.src = `${baseUrl}/assets/images/${newImage}`;
                floorPlanImage.alt = `樓層分機圖 - ${tab.textContent}`;
            });
        });
    }

    // Mobile Floor Plan Tabs
    const mobilePlanTabs = document.querySelectorAll('.mobile-plan-tab');
    const mobilePlanImage = document.getElementById('mobilePlanImage');

    if (mobilePlanTabs.length > 0 && mobilePlanImage) {
        mobilePlanTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                mobilePlanTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const newImage = tab.dataset.image;
                mobilePlanImage.src = `${baseUrl}/assets/images/${newImage}`;
                mobilePlanImage.alt = `手機版聯絡資訊 - ${tab.textContent}`;
            });
        });
    }
});
</script> 