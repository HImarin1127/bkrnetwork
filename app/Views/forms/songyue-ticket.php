<?php
// 確保 $baseUrl 有值
if (!isset($baseUrl)) {
    $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if ($baseUrl === '' || $baseUrl === '/') $baseUrl = '/bkrnetwork';
}
?>

<div class="form-container">
    <div class="page-header">
        <h1>🏢 崧月申請單</h1>
        <p>ERP 系統相關工單申請與處理</p>
    </div>

    <div class="form-content">
        <div class="access-section">
            <h3>打開工單</h3>
            <p>請點擊下方按鈕打開崧月工單，工單將在新視窗中開啟：</p>
            
            <div class="button-group">
                <a href="http://qrcode.bookrep.com.tw/erp_order" 
                   target="_blank" 
                   class="btn btn-primary btn-large">
                    🏢 打開崧月申請單
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.form-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid rgba(200, 16, 46, 0.1);
}

.page-header h1 {
    font-size: 2.5rem;
    color: #C8102E;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #666;
    font-size: 1.1rem;
}

.form-content {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.info-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(200,16,46,0.1);
    display: flex;
    gap: 1.5rem;
    align-items: flex-start;
}

.card-icon {
    font-size: 4rem;
    flex-shrink: 0;
}

.card-content h2 {
    color: #C8102E;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.card-content p {
    color: #666;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.feature-list {
    list-style: none;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
}

.feature-list li {
    color: #333;
    font-weight: 500;
    padding: 0.5rem 0;
}

.access-section {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(200,16,46,0.1);
}

.access-section h3 {
    color: #C8102E;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.access-section p {
    color: #666;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.button-group {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    padding: 1rem 2rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-large {
    padding: 1.2rem 2.5rem;
    font-size: 1.1rem;
}

.btn-primary {
    background: #C8102E;
    color: white;
    border: 2px solid #C8102E;
}

.btn-primary:hover {
    background: #a00d25;
    border-color: #a00d25;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(200, 16, 46, 0.3);
}

.btn-secondary {
    background: white;
    color: #C8102E;
    border: 2px solid #C8102E;
}

.btn-secondary:hover {
    background: #C8102E;
    color: white;
    transform: translateY(-2px);
}

.instruction-section {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(200,16,46,0.1);
}

.instruction-section h3 {
    color: #C8102E;
    font-size: 1.5rem;
    margin-bottom: 2rem;
    text-align: center;
}

.steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.step-number {
    width: 40px;
    height: 40px;
    background: #C8102E;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}
.step-number.info {
    background: #C8102E;
}


.step-content h4 {
    color: #333;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.step-content p {
    color: #666;
    line-height: 1.5;
    margin: 0;
}

.contact-section {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(200,16,46,0.1);
}

.contact-section h3 {
    color: #C8102E;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.contact-section p {
    color: #666;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.contact-info {
    display: flex;
    gap: 2rem;
    justify-content: center;
    flex-wrap: wrap;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: #333;
}

.contact-icon {
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .form-container {
        padding: 1rem;
    }
    
    .info-card {
        flex-direction: column;
        text-align: center;
    }
    
    .button-group {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 300px;
    }
    
    .steps {
        grid-template-columns: 1fr;
    }
    
    .contact-info {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<script>
function copyLink() {
    const link = 'http://qrcode.bookrep.com.tw/erp_order';
    navigator.clipboard.writeText(link).then(function() {
        alert('連結已複製到剪貼板！');
    }).catch(function() {
        // 備用方案
        const textArea = document.createElement('textarea');
        textArea.value = link;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('連結已複製到剪貼板！');
    });
}
</script>