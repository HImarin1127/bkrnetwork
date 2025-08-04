<?php
// 確保 $baseUrl 有值
if (!isset($baseUrl)) {
    $baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    if ($baseUrl === '' || $baseUrl === '/') $baseUrl = '/bkrnetwork';
}

// PDF 檔案清單
$pdfFiles = [
    [
        'name' => '交資訊作業單',
        'filename' => '員工到職-資訊作業單(250107).pdf',
        'description' => '資訊部門專用的員工到職作業單',
        'icon' => '💻',
        'preview_url' => 'http://qrcode.bookrep.com.tw/it_onboarding'
    ],
    [
        'name' => '交總務人資部',
        'filename' => '員工到職報告書-交總務人資部(250418).pdf',
        'description' => '總務及人資部門使用的到職報告書',
        'icon' => '👥',
        'preview_url' => 'http://qrcode.bookrep.com.tw/ga_onboarding'
    ],
    [
        'name' => '交財務部',
        'filename' => '員工到職報告書-交財務部(220214).pdf',
        'description' => '財務部門使用的到職報告書',
        'icon' => '📊',
        'preview_url' => 'http://qrcode.bookrep.com.tw/fd_onboarding'
    ]
];
?>

<div class="download-container">
    <div class="page-header">
        <h1>📋 員工到職表單下載</h1>
        <p>請根據您的部門需求下載相應的表單檔案</p>
    </div>

    <div class="pdf-grid">
        <?php foreach ($pdfFiles as $pdf): ?>
            <div class="pdf-card">
                <div class="pdf-icon"><?= $pdf['icon'] ?></div>
                <div class="pdf-content">
                    <h3><?= htmlspecialchars($pdf['name']) ?></h3>
                    <p><?= htmlspecialchars($pdf['description']) ?></p>
                    <div class="pdf-actions">
                        <a href="<?= htmlspecialchars($pdf['preview_url']) ?>" 
                           target="_blank" 
                           class="btn btn-view">
                            💾 下載
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
                    </div>
                    
    <div class="instruction-section">
        <h2>📝 填寫說明</h2>
        <div class="instruction-steps">
            <div class="step">
                <span class="step-number">1</span>
                <div class="step-content">
                    <h4>選擇對應表單</h4>
                    <p>根據您的部門職責選擇相應的表單檔案</p>
                </div>
                    </div>
            <div class="step">
                <span class="step-number">2</span>
                <div class="step-content">
                    <h4>下載並填寫</h4>
                    <p>下載 PDF 檔案後，使用 PDF 編輯器或列印後手寫填寫</p>
                </div>
            </div>
            <div class="step">
                <span class="step-number">3</span>
                <div class="step-content">
                    <h4>提交表單</h4>
                    <p>完成填寫後請將表單提交給相關部門主管</p>
                </div>
                </div>
        </div>
    </div>
</div>

<style>
.download-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    text-align: center;
    margin-bottom: 3rem;
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

.pdf-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.pdf-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid rgba(200,16,46,0.1);
    transition: all 0.3s ease;
}

.pdf-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.12);
    border-color: #C8102E;
}

.pdf-icon {
    font-size: 4rem;
    text-align: center;
    margin-bottom: 1.5rem;
}

.pdf-content h3 {
    color: #C8102E;
    font-size: 1.3rem;
    margin-bottom: 0.8rem;
    text-align: center;
}

.pdf-content p {
    color: #666;
    text-align: center;
    margin-bottom: 1.5rem;
    line-height: 1.5;
}

.pdf-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-view {
    background: #007bff;
    color: white;
    border: 2px solid #007bff;
}

.btn-view:hover {
    background: #0056b3;
    border-color: #0056b3;
    color: white;
    transform: translateY(-2px);
}

.btn-download {
    background: #28a745;
    color: white;
    border: 2px solid #28a745;
}

.btn-download:hover {
    background: #218838;
    border-color: #218838;
    color: white;
    transform: translateY(-2px);
}

.instruction-section {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    padding: 2.5rem;
    margin-top: 2rem;
    border: 1px solid rgba(200,16,46,0.1);
}

.instruction-section h2 {
    color: #C8102E;
    font-size: 1.8rem;
    margin-bottom: 2rem;
    text-align: center;
}

.instruction-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.step {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.step-number {
    width: 50px;
    height: 50px;
    background: #C8102E;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    flex-shrink: 0;
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

@media (max-width: 768px) {
    .download-container {
        padding: 1rem;
    }
    
    .pdf-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .pdf-actions {
        flex-direction: column;
    }
    
    .instruction-steps {
        grid-template-columns: 1fr;
    }
    
    .step {
        flex-direction: column;
        text-align: center;
    }
}
</style> 