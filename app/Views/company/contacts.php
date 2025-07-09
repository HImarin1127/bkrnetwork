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
        <!-- 公司基本資訊卡片 -->
        <div class="company-info glass-card">
            <h2>公司基本資訊</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">🏢</div>
                    <div class="info-details">
                        <h4>公司名稱</h4>
                        <p>讀書共和國出版集團股份有限公司</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">📍</div>
                    <div class="info-details">
                        <h4>公司地址</h4>
                        <p>新北市新店區民權路2號9樓No.108</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">📞</div>
                    <div class="info-details">
                        <h4>總機電話</h4>
                        <p>(02) 2500-7008</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">📠</div>
                    <div class="info-details">
                        <h4>傳真號碼</h4>
                        <p>(02) 2500-7759</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">✉️</div>
                    <div class="info-details">
                        <h4>電子信箱</h4>
                        <p>service@bookrep.com.tw</p>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">🌐</div>
                    <div class="info-details">
                        <h4>官方網站</h4>
                        <p>www.bookrep.com.tw</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 部門聯絡方式卡片 -->
        <div class="departments glass-card">
            <h2>各部門聯絡方式</h2>
            <div class="search-bar">
                <input type="text" id="departmentSearch" placeholder="搜尋部門名稱...">
                <button type="button" class="clear-search" id="clearDepartmentSearch">✕</button>
            </div>
            <div class="department-list">
                <?php foreach ($departmentContacts as $dept): ?>
                <div class="department-item" data-department="<?php echo strtolower($dept['department_name']); ?>">
                    <div class="dept-header">
                        <h3><?php echo $dept['department_name']; ?></h3>
                        <div class="dept-location">
                            <span class="dept-building"><?php 
                                switch($dept['building']) {
                                    case '108-2':
                                        echo '108-2號';
                                        break;
                                    case '108-3':
                                        echo '108-3號';
                                        break;
                                    case '108-4':
                                        echo '108-4號';
                                        break;
                                    case 'nankan':
                                        echo '南崁';
                                        break;
                                    default:
                                        echo $dept['building'];
                                }
                            ?></span>
                            <span class="dept-floor"><?php echo $dept['floor_number']; ?>F</span>
                        </div>
                    </div>
                    <div class="dept-contacts">
                        <div class="contact-row">
                            <span class="contact-label">分機</span>
                            <span class="contact-value"><?php echo $dept['extension_range']; ?></span>
                        </div>
                        <div class="contact-row">
                            <span class="contact-label">信箱</span>
                            <span class="contact-value"><?php echo $dept['email']; ?></span>
                        </div>
                        <?php if (!empty($dept['description'])): ?>
                        <div class="contact-row">
                            <span class="contact-label">說明</span>
                            <span class="contact-value"><?php echo $dept['description']; ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 分機目錄卡片 -->
        <div class="extension-directory glass-card">
            <h2>分機目錄</h2>
            <div class="extension-image-container">
                <?php 
                $imagePath = __DIR__ . '/../../../assets/images/extension-table-2025.jpg';
                $imageUrl = $baseUrl . '/assets/images/extension-table-2025.jpg';
                if (file_exists($imagePath)): 
                ?>
                    <div class="extension-image-wrapper">
                        <img src="<?php echo $imageUrl; ?>" 
                             alt="讀書共和國分機表 2025/1/2 更新" 
                             class="extension-table-image"
                             onerror="this.parentElement.style.display='none'; document.getElementById('fallback-message').style.display='block';">
                        <div class="image-overlay">
                            <div class="image-info">
                                <h4>📞 分機表 (2025/1/2 更新)</h4>
                                <p>點擊圖片可放大檢視</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="no-image-fallback" id="fallback-message">
                        <div class="fallback-content">
                            <div class="fallback-icon">📞</div>
                            <h4>分機表尚未上傳</h4>
                            <p>請將分機表圖片儲存為：<br>
                               <code>assets/images/extension-table-2025.jpg</code></p>
                            <div class="upload-instructions">
                                <p><strong>📋 上傳步驟：</strong></p>
                                <ol>
                                    <li>將分機表圖片重新命名為 <code>extension-table-2025.jpg</code></li>
                                    <li>上傳到 <code>assets/images/</code> 目錄</li>
                                    <li>重新整理此頁面</li>
                                </ol>
                            </div>
                            <button type="button" onclick="showTextDirectory()" class="show-text-btn">
                                查看文字版分機目錄
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 備用文字分機目錄 (隱藏但保留功能) -->
            <div class="text-extension-list" style="display: none;">
                <div class="text-directory-header">
                    <h3>📝 文字版分機目錄</h3>
                    <p class="directory-description">使用下方搜尋功能快速找到聯絡人資訊</p>
                </div>
                <div class="search-bar">
                    <input type="text" id="extensionSearch" placeholder="搜尋員工姓名或分機號碼...">
                    <button type="button" class="clear-search" id="clearExtensionSearch">✕</button>
                </div>
                <div class="extension-list">
                    <?php if (!empty($extensionNumbers)): ?>
                        <?php foreach ($extensionNumbers as $ext): ?>
                        <div class="extension-item" 
                             data-name="<?php echo strtolower($ext['employee_name']); ?>" 
                             data-number="<?php echo $ext['extension_number']; ?>">
                            <div class="extension-info">
                                <span class="employee-name"><?php echo $ext['employee_name']; ?></span>
                                <span class="extension-number">分機: <?php echo $ext['extension_number']; ?></span>
                            </div>
                            <?php if (!empty($ext['description'])): ?>
                            <div class="extension-description">
                                <?php echo $ext['description']; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-data">目前沒有分機資料</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 辦公時間卡片 -->
        <div class="office-hours glass-card">
            <h2>辦公時間</h2>
            <div class="hours-info">
                <div class="hours-item">
                    <div class="hours-icon">🕘</div>
                    <div class="hours-details">
                        <h4>上班時間</h4>
                        <p>週一至週五 09:00 - 18:00</p>
                    </div>
                </div>
                <div class="hours-item">
                    <div class="hours-icon">🍽️</div>
                    <div class="hours-details">
                        <h4>午休時間</h4>
                        <p>12:00 - 13:00</p>
                    </div>
                </div>
                <div class="hours-item">
                    <div class="hours-icon">🚫</div>
                    <div class="hours-details">
                        <h4>例假日</h4>
                        <p>週六、週日及國定假日</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 緊急聯絡卡片 -->
        <div class="emergency-contacts glass-card">
            <h2>緊急聯絡</h2>
            <div class="emergency-grid">
                <div class="emergency-item">
                    <div class="emergency-icon">🚨</div>
                    <h4>24小時緊急聯絡</h4>
                    <p>保全室：(02) 2500-7000</p>
                </div>
                <div class="emergency-item">
                    <div class="emergency-icon">🔧</div>
                    <h4>設備維修</h4>
                    <p>總務：分機 506</p>
                </div>
                <div class="emergency-item">
                    <div class="emergency-icon">💻</div>
                    <h4>資訊故障</h4>
                    <p>資訊部：分機 701</p>
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

/* 圖片放大 Modal 樣式 */
.image-modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    padding-top: 50px; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgb(0,0,0); 
    background-color: rgba(0,0,0,0.9);
}

.modal-content {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 1200px;
    animation-name: zoom;
    animation-duration: 0.6s;
}

#modalCaption {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 0;
    height: 150px;
}

@keyframes zoom {
    from {transform:scale(0)} 
    to {transform:scale(1)}
}

.modal-close-btn {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.modal-close-btn:hover,
.modal-close-btn:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

@media only screen and (max-width: 700px){
    .modal-content {
        width: 100%;
    }
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
</style>

<div id="imageModal" class="image-modal">
    <span class="modal-close-btn">&times;</span>
    <img class="modal-content" id="modalImage">
    <div id="modalCaption"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 部門搜尋功能
    const departmentSearch = document.getElementById('departmentSearch');
    const clearDepartmentSearch = document.getElementById('clearDepartmentSearch');
    const departmentItems = document.querySelectorAll('.department-item');

    if (departmentSearch) {
        departmentSearch.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            if(clearDepartmentSearch) clearDepartmentSearch.style.display = searchValue ? 'block' : 'none';

            departmentItems.forEach(item => {
                const departmentName = item.getAttribute('data-department');
                if (departmentName.includes(searchValue)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    }
    
    if (clearDepartmentSearch) {
        clearDepartmentSearch.addEventListener('click', function() {
            departmentSearch.value = '';
            departmentSearch.dispatchEvent(new Event('input'));
        });
    }

    // 分機搜尋功能
    const extensionSearch = document.getElementById('extensionSearch');
    const clearExtensionSearch = document.getElementById('clearExtensionSearch');
    const extensionItems = document.querySelectorAll('.extension-item');

    if (extensionSearch) {
        extensionSearch.addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            if(clearExtensionSearch) clearExtensionSearch.style.display = searchValue ? 'block' : 'none';

            extensionItems.forEach(item => {
                const name = item.getAttribute('data-name');
                const number = item.getAttribute('data-number');
                if (name.includes(searchValue) || number.includes(searchValue)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        });
    }

    if (clearExtensionSearch) {
        clearExtensionSearch.addEventListener('click', function() {
            extensionSearch.value = '';
            extensionSearch.dispatchEvent(new Event('input'));
        });
    }

    // 圖片 Modal 功能
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");
    const captionText = document.getElementById("modalCaption");
    const imageToOpen = document.querySelector('.extension-table-image');
    
    if (imageToOpen) {
        imageToOpen.onclick = function() {
            if(modal) modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
            document.body.style.overflow = 'hidden'; // 禁止背景滾動
        }
    }

    const closeBtn = document.querySelector(".modal-close-btn");
    if (closeBtn) {
        closeBtn.onclick = function() {
            if(modal) modal.style.display = "none";
            document.body.style.overflow = ''; // 恢復背景滾動
        }
    }
    
    if (modal) {
        modal.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
                document.body.style.overflow = ''; // 恢復背景滾動
            }
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape" && modal && modal.style.display === "block") {
            modal.style.display = "none";
            document.body.style.overflow = ''; // 恢復背景滾動
        }
    });
});

// 顯示文字版分機目錄
function showTextDirectory() {
    const fallbackMessage = document.getElementById('fallback-message');
    const textDirectory = document.querySelector('.text-extension-list');
    
    if (fallbackMessage && textDirectory) {
        fallbackMessage.style.display = 'none';
        textDirectory.style.display = 'block';
        
        textDirectory.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    }
}
</script> 