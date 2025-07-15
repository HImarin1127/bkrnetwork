<div class="floor-container">
    <div class="page-header">
        <h1>樓層配置圖</h1>
        <p class="page-subtitle">讀書共和國辦公室樓層分布與空間配置</p>
    </div>

    <div class="floor-content">
        
        <div class="floor-selector-card glass-card">
            <h2>樓層導覽</h2>
            <div class="floor-tabs">
                <button class="floor-tab active" data-floor="9f">108-2 9F 出版單位+社長辦公室</button>
                <button class="floor-tab" data-floor="8f-a">108-4 8F 營業單位辦公室</button>
                <button class="floor-tab" data-floor="8f-b">108-3 8F 出版單位</button>
                <button class="floor-tab" data-floor="6f">108-3 6F 出版單位</button>
                <button class="floor-tab" data-floor="5f">108-4 5F 出版單位+營業單位</button>
                <button class="floor-tab" data-floor="3f">108-3 3F 出版單位</button>
                <button class="floor-tab" data-floor="logistics">南崁 物流中心</button>
            </div>

            <div class="floor-content-area">
                <!-- 9F Panel -->
                <div class="floor-panel active" id="floor-9f">
                    <div class="floor-plan-image">
                        <a href="<?php echo $baseUrl; ?>/assets/images/108-2-9F.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                            <img src="<?php echo $baseUrl; ?>/assets/images/108-2-9F.png" alt="108-2 9F 平面圖">
                        </a>
                    </div>
                </div>

                <!-- 108-4 8F Panel -->
                <div class="floor-panel" id="floor-8f-a">
                    <div class="floor-plan-image">
                        <a href="<?php echo $baseUrl; ?>/assets/images/108-4-8F.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                            <img src="<?php echo $baseUrl; ?>/assets/images/108-4-8F.png" alt="108-4 8F 平面圖">
                        </a>
                    </div>
                </div>

                <!-- 108-3 8F Panel -->
                <div class="floor-panel" id="floor-8f-b">
                    <div class="floor-plan-image">
                        <a href="<?php echo $baseUrl; ?>/assets/images/108-3-8F.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                            <img src="<?php echo $baseUrl; ?>/assets/images/108-3-8F.png" alt="108-3 8F 平面圖">
                        </a>
                    </div>
                </div>

                <!-- 6F Panel -->
                <div class="floor-panel" id="floor-6f">
                    <div class="floor-plan-image">
                        <a href="<?php echo $baseUrl; ?>/assets/images/108-3-6F.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                            <img src="<?php echo $baseUrl; ?>/assets/images/108-3-6F.png" alt="108-3 6F 平面圖">
                        </a>
                    </div>
                </div>

                <!-- 5F Panel -->
                <div class="floor-panel" id="floor-5f">
                    <div class="floor-plan-image">
                        <a href="<?php echo $baseUrl; ?>/assets/images/108-4-5F.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                            <img src="<?php echo $baseUrl; ?>/assets/images/108-4-5F.png" alt="108-4 5F 平面圖">
                        </a>
                    </div>
                </div>

                <!-- 3F Panel -->
                <div class="floor-panel" id="floor-3f">
                    <div class="floor-plan-image">
                        <a href="<?php echo $baseUrl; ?>/assets/images/108-3-3F.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                            <img src="<?php echo $baseUrl; ?>/assets/images/108-3-3F.png" alt="108-3 3F 平面圖">
                        </a>
                    </div>
                </div>

                <!-- Logistics Panel -->
                <div class="floor-panel" id="floor-logistics">
                    <div class="floor-plan-image">
                        <a href="<?php echo $baseUrl; ?>/assets/images/Logistics.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                            <img src="<?php echo $baseUrl; ?>/assets/images/Logistics.png" alt="物流中心-1" style="margin-bottom: 1rem;">
                        </a>
                        <a href="<?php echo $baseUrl; ?>/assets/images/Logistics-1.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                            <img src="<?php echo $baseUrl; ?>/assets/images/Logistics-1.png" alt="物流中心-2" style="margin-bottom: 1rem;">
                        </a>
                        <a href="<?php echo $baseUrl; ?>/assets/images/Logistics-2.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                            <img src="<?php echo $baseUrl; ?>/assets/images/Logistics-2.png" alt="物流中心-3">
                        </a>
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
    animation: fadeIn 0.5s;
}

.floor-panel.active {
    display: block;
}

.floor-plan-image {
    margin-top: 20px;
    text-align: center;
}

.floor-plan-image img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@media (max-width: 768px) {
    .floor-container {
        padding: 15px;
    }
    .glass-card {
        padding: 20px;
    }
    .floor-tabs {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.floor-tab');
    const panels = document.querySelectorAll('.floor-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', function () {
            // Deactivate all tabs and panels
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));

            // Activate the clicked tab
            this.classList.add('active');

            // Activate the corresponding panel
            const floorId = this.getAttribute('data-floor');
            const targetPanel = document.getElementById('floor-' + floorId);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        });
    });
});
</script>