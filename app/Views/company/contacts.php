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
                        <p>台北市中山區民生東路二段141號</p>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 部門搜尋功能
    const departmentSearch = document.getElementById('departmentSearch');
    const clearDepartmentSearch = document.getElementById('clearDepartmentSearch');
    const departmentItems = document.querySelectorAll('.department-item');

    departmentSearch.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase().trim();
        clearDepartmentSearch.style.display = searchValue ? 'block' : 'none';

        departmentItems.forEach(item => {
            const departmentName = item.getAttribute('data-department');
            if (departmentName.includes(searchValue)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });

    clearDepartmentSearch.addEventListener('click', function() {
        departmentSearch.value = '';
        departmentSearch.dispatchEvent(new Event('input'));
    });

    // 分機搜尋功能
    const extensionSearch = document.getElementById('extensionSearch');
    const clearExtensionSearch = document.getElementById('clearExtensionSearch');
    const extensionItems = document.querySelectorAll('.extension-item');

    extensionSearch.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase().trim();
        clearExtensionSearch.style.display = searchValue ? 'block' : 'none';

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

    clearExtensionSearch.addEventListener('click', function() {
        extensionSearch.value = '';
        extensionSearch.dispatchEvent(new Event('input'));
    });
});
</script> 