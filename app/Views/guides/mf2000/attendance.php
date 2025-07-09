<?php
// app/Views/guides/mf2000/attendance.php

?>

<div class="container">
    <div class="page-header">
        <h1>MF2000 出缺勤管理</h1>
        <p class="lead">此處提供 MF2000 系統中關於出缺勤管理的各項操作手冊。</p>
    </div>

    <div class="card-deck">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-user-clock"></i> 加班申請</h5>
                <p class="card-text">加班申請流程的操作說明。</p>
                <a href="https://drive.google.com/file/d/10dkgFitQWI6WPf_At4lZY-cUNRauGhqd/view?usp=sharing" class="btn btn-info" target="_blank"><i class="fas fa-book-open"></i> 閱讀文件</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-fingerprint"></i> 忘刷補登</h5>
                <p class="card-text">忘記刷卡時的補登申請流程。</p>
                <a href="https://drive.google.com/file/d/1_0HzSaIk-sQWtlXI2-uL34eclaQJPh4O/view?usp=sharing" class="btn btn-info" target="_blank"><i class="fas fa-book-open"></i> 閱讀文件</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-envelope-open-text"></i> 請假流程 (含寄信)</h5>
                <p class="card-text">完整的請假申請流程，包含系統操作與郵件通知。</p>
                <a href="https://drive.google.com/file/d/1CW_c3sBq5O2XUlHO7JNpUtntQDCsm6t5/view?usp=sharing" class="btn btn-info" target="_blank"><i class="fas fa-book-open"></i> 閱讀文件</a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-calendar-times"></i> 請假流程 (純操作)</h5>
                <p class="card-text">僅包含系統操作部分的請假申請流程說明。</p>
                <a href="https://drive.google.com/file/d/1iShvFKODW5mCzgshDhpXKKnS_hwlgjSV/view?usp=sharing" class="btn btn-info" target="_blank"><i class="fas fa-book-open"></i> 閱讀文件</a>
            </div>
        </div>
    </div>
</div>

<style>
.card-deck {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}
.card {
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    display: flex;
    flex-direction: column;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}
.card-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}
.card-title {
    margin-bottom: 0.75rem;
    font-size: 1.25rem;
}
.card-title .fas {
    margin-right: 0.5rem;
    color: #3182ce;
}
.card-text {
    color: #718096;
    margin-bottom: 1.5rem;
    flex-grow: 1;
}
.btn {
    margin-top: auto;
}
.btn .fas {
    margin-right: 0.3rem;
}
.lead {
    color: #4a5568;
}
</style> 