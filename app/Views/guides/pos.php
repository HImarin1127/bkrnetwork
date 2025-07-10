<?php
// app/Views/guides/pos.php
?>

<div class="container-fluid">
    <div class="page-header">
        <h1>POS 收銀機操作手冊</h1>
    </div>

    <div class="pdf-container">
        <iframe src="<?php echo $baseUrl; ?>/assets/files/pos/收銀機操作手冊.pdf" frameborder="0" width="100%" height="100%"></iframe>
    </div>
</div>

<style>
.pdf-container {
    width: 100%;
    height: 75vh;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.pdf-container iframe {
    display: block;
    width: 100%;
    height: 100%;
}
</style> 