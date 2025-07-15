<?php
// app/Views/guides/tax-exempt-system.php

$pageTitle = "文化部免稅系統操作說明";
$pageType = "guides"; // For sidebar highlighting
?>

<style>
.container.mt-4 {
    font-size: 1.18rem;
}
.container.mt-4 h1,
.container.mt-4 h3,
.container.mt-4 .card-header {
    font-size: 1.45rem;
}
.container.mt-4 ul,
.container.mt-4 p,
.container.mt-4 li {
    font-size: 1.18rem;
}
.container.mt-4 ul {
    list-style-type: none;
    padding-left: 0;
}
</style>

<div class="container mt-4">
    <h1 class="mb-4"><?php echo $pageTitle; ?></h1>

    <div class="card mb-4">
        <div class="card-header">
            <strong>操作說明</strong>
        </div>
        <div class="card-body">
            <p>登入【文化部免稅平台】帳號管理方法如下說明：</p>
            <ul>
                <li>文化部免稅系統以出版品牌為單位，帳號可看見該出版品牌所有書目申請狀況。</li>
                <li>集團旗下每一個出版品牌，將發給一個帳號，預設將以出版單位最高主管之電子郵件為帳號ID，並由各出版單位自行保管帳號、密碼，隨時上線申請、查核圖書免稅狀態。</li>
            </ul>
            <p><strong>登入方法：</strong></p>
            <ul>
                <li>登入網址：<a href="https://tax.moc.gov.tw/book-apply/login.jsp" target="_blank">https://tax.moc.gov.tw/book-apply/login.jsp</a></li>
                <li>單位統一編號：53226822</li>
                <li>註冊Email：貴單位帳號管理員之信箱</li>
                <li>密碼：貴單位自行設定的密碼</li>
            </ul>
            <p>（忘記密碼需要聯繫資訊部，後續系統會寄送密碼變更的信給貴單位帳號管理員的信箱）</p>
            <div class="text-center mt-3">
                <a href="<?php echo BASE_URL; ?>assets/images/文化部登入.png" target="_blank" rel="noopener noreferrer" class="clickable-image">
                    <img src="<?php echo BASE_URL; ?>assets/images/文化部登入.png" alt="文化部免稅系統登入畫面" class="img-fluid" style="max-width:480px;">
                </a>
                <div style="color:#666; font-size:0.95rem; margin-top:0.5rem;">文化部免稅系統登入畫面範例</div>
            </div>
            <!-- 圖片可依需求插入 -->
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3>文化部圖書免稅系統操作說明</h3>
        </div>
        <div class="card-body">
            <p>此為免稅系統的整體操作總覽說明。</p>
            <a href="<?php echo BASE_URL; ?>assets/files/tax-exempt/文化部圖書免稅系統操作說明.pdf" target="_blank" class="btn btn-primary">
                <i class="fas fa-book-open"></i> 查看總說明
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3>單筆 EAN 指對教學</h3>
        </div>
        <div class="card-body">
            <!--<p>此文件說明如何在免稅系統中進行單筆 EAN 的指對操作。</p>-->
            <a href="<?php echo BASE_URL; ?>assets/files/tax-exempt/文化部免稅系統操作教學_單筆EAN指對.pdf" target="_blank" class="btn btn-primary">
                </i> 查看單筆操作教學
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3>批次 EAN 指對教學</h3>
        </div>
        <div class="card-body">
            <!--<p>此文件說明如何在免稅系統中進行批次的 EAN 指對操作。</p>-->
            <a href="<?php echo BASE_URL; ?>assets/files/tax-exempt/文化部免稅系統操作教學_批次EAN指對.pdf" target="_blank" class="btn btn-primary">
                </i> 查看批次操作教學
            </a>
        </div>
    </div>
</div> 