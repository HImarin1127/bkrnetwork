<?php
// app/Views/guides/printer-basic.php

$pageTitle = "印表機基本操作說明";
$pageType = "guides"; // For sidebar highlighting
?>

<div class="container mt-4">
    <h1 class="mb-4"><?php echo $pageTitle; ?></h1>

    <div class="card mb-4">
        <div class="card-header">
            <h3>檔案列印</h3>
        </div>
        <div class="card-body">
            <p>關於如何使用印表機進行文件列印，請參考以下操作手冊：</p>
            <a href="<?php echo BASE_URL; ?>assets/files/printer/檔案列印.pdf" target="_blank" class="btn btn-primary">
                <i class="fas fa-print"></i> 查看列印教學
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3>檔案掃描</h3>
        </div>
        <div class="card-body">
            <p>關於如何使用印表機將實體文件掃描為電子檔，請參考以下操作手冊：</p>
            <a href="<?php echo BASE_URL; ?>assets/files/printer/檔案掃描.pdf" target="_blank" class="btn btn-primary">
                <i class="fas fa-scanner"></i> 查看掃描教學
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3>檔案傳真</h3>
        </div>
        <div class="card-body">
            <p>關於如何使用印表機發送傳真，請參考以下操作手冊：</p>
            <a href="<?php echo BASE_URL; ?>assets/files/printer/檔案傳真.pdf" target="_blank" class="btn btn-primary">
                <i class="fas fa-fax"></i> 查看傳真教學
            </a>
        </div>
    </div>
</div> 