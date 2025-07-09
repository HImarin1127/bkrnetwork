<?php
// app/Views/guides/mf2000/workflow.php

?>

<div class="container">
    <div class="page-header">
        <h1>MF2000 公文</h1>
    </div>

    <div class="card-deck">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-file-alt"></i> MF2000公文簽核</h5>
                <p class="card-text">MF2000公文簽核流程與操作說明文件。</p>
                <a href="https://drive.google.com/file/d/1ag9s8c_zjk34i5MIUq0FLl_FpdEhuQDL/view?usp=drive_link" class="btn btn-primary" target="_blank"><i class="fas fa-book-open"></i> 閱讀文件</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-external-link-alt"></i> MF2000電子公文網址</h5>
                <p class="card-text">點擊此處直接前往 MF2000 電子公文系統登入頁面。</p>
                <a href="https://eflow.bookrep.com.tw/docm" class="btn btn-secondary" target="_blank"><i class="fas fa-link"></i> 前往系統</a>
            </div>
        </div>
    </div>
</div>

<style>
.card-deck {
    display: flex;
    flex-wrap: wrap;
    gap: 1.5rem;
}
.card {
    flex: 1 1 300px; /* Flex-grow, flex-shrink, flex-basis */
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}
.card-body {
    padding: 1.5rem;
}
.card-title {
    margin-bottom: 1rem;
    font-size: 1.25rem;
}
.card-title .fas {
    margin-right: 0.5rem;
    color: #4a5568;
}
.card-text {
    color: #718096;
    margin-bottom: 1.5rem;
}
.btn .fas {
    margin-right: 0.3rem;
}
</style> 