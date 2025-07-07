<div class="floor-container">
    <div class="page-header">
        <h1>樓層配置圖</h1>
        <p class="page-subtitle">讀書共和國辦公室樓層分布與空間配置</p>
    </div>

    <div class="floor-content">
        <div class="floor-overview glass-card">
            <h2>辦公大樓概覽</h2>
            <div class="building-info">
                <div class="building-stats">
                    <div class="stat-item">
                        <div class="stat-icon">🏢</div>
                        <div class="stat-details">
                            <h4>總樓層數</h4>
                            <span>5層辦公樓</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">👥</div>
                        <div class="stat-details">
                            <h4>容納人數</h4>
                            <span>約150人</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">📐</div>
                        <div class="stat-details">
                            <h4>總面積</h4>
                            <span>約3500坪</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="floor-selector-card glass-card">
            <h2>樓層導覽</h2>
            <div class="floor-tabs">
                <button class="floor-tab active" data-floor="7">108-2 9F 出版單位+社長辦公室</button>
                <button class="floor-tab" data-floor="6">108-4 8F 營業單位辦公室</button>
                <button class="floor-tab" data-floor="6">108-3 8F 出版單位</button>
                <button class="floor-tab" data-floor="5">108-3 6F 出版單位</button>
                <button class="floor-tab" data-floor="4">108-4 5F 出版單位+營業單位</button>
                <button class="floor-tab" data-floor="3">108-3 3F 出版單位</button>
            </div>

            <div class="floor-content-area">
                <?php foreach ($floorInfo as $floor): ?>
                    <div class="floor-panel <?php echo $floor['floor_number'] == 7 ? 'active' : ''; ?>" id="floor-<?php echo $floor['floor_number']; ?>">
                        <div class="floor-header">
                            <h3><?php echo $floor['floor_number']; ?>樓 - <?php echo $floor['floor_name']; ?></h3>
                            <span class="floor-badge <?php echo $floor['floor_type']; ?>"><?php echo $floor['floor_description']; ?></span>
                        </div>
                        <div class="floor-layout">
                            <div class="office-grid">
                                <?php
                                $floorEmployees = array_filter($employeeSeats, function($seat) use ($floor) {
                                    return $seat['floor_number'] == $floor['floor_number'];
                                });
                                foreach ($floorEmployees as $employee):
                                ?>
                                <div class="employee-seat" data-seat="<?php echo $employee['seat_number']; ?>">
                                    <div class="seat-info">
                                        <span class="employee-name"><?php echo $employee['employee_name']; ?></span>
                                        <?php if ($employee['extension_number']): ?>
                                            <span class="extension-number">分機: <?php echo $employee['extension_number']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="facilities glass-card">
            <h2>公共設施</h2>
            <div class="facilities-grid">
                <div class="facility-card">
                    <div class="facility-header">
                        <div class="facility-icon">🚗</div>
                        <h4>地下停車場</h4>
                    </div>
                    <div class="facility-details">
                        <p><strong>位置：</strong>地下1樓</p>
                        <p><strong>車位：</strong>80個專用車位</p>
                        <p><strong>開放時間：</strong>24小時</p>
                    </div>
                </div>

                <div class="facility-card">
                    <div class="facility-header">
                        <div class="facility-icon">🍽️</div>
                        <h4>員工餐廳</h4>
                    </div>
                    <div class="facility-details">
                        <p><strong>位置：</strong>2樓</p>
                        <p><strong>座位：</strong>可容納100人</p>
                        <p><strong>供餐時間：</strong>11:30-14:00</p>
                    </div>
                </div>

                <div class="facility-card">
                    <div class="facility-header">
                        <div class="facility-icon">📚</div>
                        <h4>圖書閱覽室</h4>
                    </div>
                    <div class="facility-details">
                        <p><strong>位置：</strong>1樓</p>
                        <p><strong>藏書：</strong>公司出版品展示</p>
                        <p><strong>開放時間：</strong>09:00-18:00</p>
                    </div>
                </div>

                <div class="facility-card">
                    <div class="facility-header">
                        <div class="facility-icon">🏃</div>
                        <h4>健身房</h4>
                    </div>
                    <div class="facility-details">
                        <p><strong>位置：</strong>地下2樓</p>
                        <p><strong>設備：</strong>基本健身器材</p>
                        <p><strong>開放時間：</strong>07:00-21:00</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.floor-container {
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

.floor-content {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.glass-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    padding: 30px;
}

.glass-card h2 {
    color: #6b46c1;
    margin-bottom: 25px;
}

.building-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.05);
}

.stat-icon {
    font-size: 2.5rem;
    flex-shrink: 0;
}

.stat-details h4 {
    color: #6b46c1;
    margin-bottom: 5px;
}

.stat-details span {
    color: #374151;
    font-weight: 500;
}

.floor-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    padding-bottom: 20px;
}

.floor-tab {
    padding: 12px 20px;
    border: none;
    background: rgba(255, 255, 255, 0.1);
    color: #6b7280;
    border-radius: 25px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s ease;
}

.floor-tab:hover,
.floor-tab.active {
    background: #C8102E;
    color: white;
    transform: translateY(-2px);
}

.floor-panel {
    display: none;
}

.floor-panel.active {
    display: block;
}

.floor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.floor-header h3 {
    color: #6b46c1;
    margin: 0;
    font-size: 1.5rem;
}

.floor-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 500;
}

.floor-badge.executive {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.floor-badge.editorial {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.floor-badge.sales {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.floor-badge.production {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.floor-badge.admin {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.office-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.office-room {
    padding: 25px;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    transition: all 0.3s ease;
}

.office-room:hover {
    transform: translateY(-5px);
    background: rgba(255, 255, 255, 0.1);
}

.room-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.office-room h4 {
    color: #6b46c1;
    margin-bottom: 10px;
    font-size: 1.1rem;
}

.office-room p {
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.5;
}

.facilities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.facility-card {
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
}

.facility-header {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: rgba(107, 70, 193, 0.1);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.facility-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.facility-header h4 {
    color: #6b46c1;
    margin: 0;
}

.facility-details {
    padding: 20px;
}

.facility-details p {
    color: #374151;
    margin: 8px 0;
    line-height: 1.5;
}

.facility-details strong {
    color: #6b46c1;
}

.employee-seat {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin: 5px;
    background-color: #f9f9f9;
}

.seat-info {
    display: flex;
    flex-direction: column;
}

.employee-name {
    font-weight: bold;
    margin-bottom: 5px;
}

.extension-number {
    color: #666;
    font-size: 0.9em;
}

.floor-layout {
    position: relative;
    margin-top: 20px;
}

.office-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 15px;
    padding: 20px;
}

@media (max-width: 768px) {
    .floor-container {
        padding: 15px;
    }
    
    .glass-card {
        padding: 20px;
    }
    
    .building-stats {
        grid-template-columns: 1fr;
    }
    
    .floor-tabs {
        flex-direction: column;
    }
    
    .floor-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .office-grid {
        grid-template-columns: 1fr;
    }
    
    .facilities-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 樓層切換功能
    const floorTabs = document.querySelectorAll('.floor-tab');
    const floorPanels = document.querySelectorAll('.floor-panel');
    
    floorTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const floor = tab.dataset.floor;
            
            // 移除所有活動狀態
            floorTabs.forEach(t => t.classList.remove('active'));
            floorPanels.forEach(p => p.classList.remove('active'));
            
            // 設置當前活動狀態
            tab.classList.add('active');
            document.getElementById(`floor-${floor}`).classList.add('active');
        });
    });
});
</script> 