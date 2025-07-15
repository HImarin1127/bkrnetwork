<?php
// app/Views/guides/tax-exempt.php

$pageTitle = "出版品免稅申請流程";
?>

<div class="container mt-4">
    <h1 class="mb-4"><?php echo $pageTitle; ?></h1>

    <div class="card mb-4">
        <div class="card-header">
        <strong>申請說明:</strong>
        </div>
        <div class="card-body">
            <p>文化部免稅帳號管理權歸屬於集團旗下，必須在國圖申請完成出版成立後，文化部才會接收到國圖的資料，資訊部才能進行免稅帳號歸屬的動作。各出版單位帳號由集團統一開立配給。</p>
            <p><strong>請提供以下資料：</strong></p>
            <ul>
                【品牌免稅管理 email 帳號】
                    <br>【品牌帳號管理員姓名】
                    <br>【出版證明文件】（可以提供書籍版權頁或是與讀書共和國有關聯的證明資料）
            </ul>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            文化部出版品免稅申請流程圖
        </div>
        <div class="card-body text-center">
            <a href="<?php echo BASE_URL; ?>assets/images/tax_exemption_flowchart.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                <img src="<?php echo BASE_URL; ?>assets/images/tax_exemption_flowchart.png" class="img-fluid" alt="出版品免稅申請流程圖">
            </a>
        </div>
    </div>
</div> 