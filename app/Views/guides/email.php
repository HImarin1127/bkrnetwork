<?php
// app/Views/guides/email.php

$pageTitle = "電子郵件完整設定指引";
$pageType = "guides"; // For sidebar highlighting
?>

<div class="container mt-4">
    <h1 class="mb-4"><?php echo $pageTitle; ?></h1>

    <div class="card mb-4">
        <div class="card-header">
            <h3>Webmail 入口</h3>
        </div>
        <div class="card-body">
            <p>您可以透過以下網址登入公司的網頁版電子郵件系統：</p>
            <a href="http://mail.bookrep.com.tw" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                <i class="fas fa-envelope"></i> 前往 mail.bookrep.com.tw
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3>變更密碼及自動回信教學</h3>
        </div>
        <div class="card-body">
            <p>如果您需要變更信箱密碼，或設定休假時的自動回覆，請參考以下教學文件：</p>
            <a href="<?php echo BASE_URL; ?>assets/files/email/10.公司信箱變更密碼及自動回信教學.pdf" target="_blank" class="btn btn-primary">
                <i class="fas fa-key"></i> 查看教學文件
            </a>
        </div>
    </div>
</div>