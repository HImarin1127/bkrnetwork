<?php
// app/Views/guides/tax-exempt-system.php

$pageTitle = "文化部免稅系統操作說明";
$pageType = "guides"; // For sidebar highlighting
?>

<div class="container mt-4">
    <h1 class="mb-4"><?php echo $pageTitle; ?></h1>

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
            <p>此文件說明如何在免稅系統中進行單筆 EAN 的指對操作。</p>
            <a href="<?php echo BASE_URL; ?>assets/files/tax-exempt/文化部免稅系統操作教學_單筆EAN指對.pdf" target="_blank" class="btn btn-primary">
                <i class="fas fa-search"></i> 查看單筆操作教學
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3>批次 EAN 指對教學</h3>
        </div>
        <div class="card-body">
            <p>此文件說明如何在免稅系統中進行批次的 EAN 指對操作。</p>
            <a href="<?php echo BASE_URL; ?>assets/files/tax-exempt/文化部免稅系統操作教學_批次EAN指對.pdf" target="_blank" class="btn btn-primary">
                <i class="fas fa-tasks"></i> 查看批次操作教學
            </a>
        </div>
    </div>
</div> 