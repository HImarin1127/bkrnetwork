<div class="page-header">
    <h1 class="page-title">📦 郵資查詢</h1>
    <p class="page-subtitle">可依物流編號、收件人等條件查詢國揚匯入的郵資資料。</p>
</div>

<div class="content-card">
    <form method="POST" class="search-form">
        <div class="search-section">
            <div class="search-group">
                <div class="search-input-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="receiver_name" placeholder="收件人姓名" value="<?php echo htmlspecialchars($filters['receiver_name'] ?? ''); ?>" class="search-input">
                </div>
                <div class="search-input-wrapper">
                    <input type="text" name="tracking_number" placeholder="物流編號" value="<?php echo htmlspecialchars($filters['tracking_number'] ?? ''); ?>" class="search-input">
                </div>
                <div class="search-input-wrapper">
                    <input type="text" name="original_tracking" placeholder="原物流編號" value="<?php echo htmlspecialchars($filters['original_tracking'] ?? ''); ?>" class="search-input">
                </div>
                <div class="search-input-wrapper">
                    <input type="text" name="order_id" placeholder="訂單編號" value="<?php echo htmlspecialchars($filters['order_id'] ?? ''); ?>" class="search-input">
                </div>
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($filters['start_date'] ?? ''); ?>" class="search-input" style="width:140px;">
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($filters['end_date'] ?? ''); ?>" class="search-input" style="width:140px;">
                <button type="submit" class="btn btn-primary"><span>🔍</span> 查詢</button>
                <?php if (!empty($filters['receiver_name']) || !empty($filters['tracking_number']) || !empty($filters['original_tracking']) || !empty($filters['order_id']) || !empty($filters['start_date']) || !empty($filters['end_date'])): ?>
                    <a href="<?php echo $baseUrl; ?>/mail/postage" class="btn btn-outline"><span>🗑️</span> 清除</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<?php if (empty($records)): ?>
    <div class="content-card">
        <div class="empty-state">
            <div class="empty-illustration">
                <div class="empty-icon">📭</div>
                <div class="empty-pattern"></div>
            </div>
            <h3 class="empty-title">🔍 找不到符合條件的郵資資料</h3>
            <p class="empty-message">請嘗試調整搜尋條件或關鍵字</p>
        </div>
    </div>
<?php else: ?>
    <div class="content-card">
        <div class="table-header">
            <h3>📦 郵資資料總覽</h3>
            <div class="table-stats">
                <span class="stat-item">
                    <span class="stat-number"><?php echo count($records); ?></span>
                    <span class="stat-label">筆資料</span>
                </span>
            </div>
        </div>
        <div class="records-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>物流編號</th>
                        <th>原物流編號</th>
                        <th>批號</th>
                        <th>入件日</th>
                        <th>收件人</th>
                        <th>地址</th>
                        <th>電話</th>
                        <th>代收金額</th>
                        <th>訂單編號</th>
                        <th>商品名稱</th>
                        <th>數量</th>
                        <th>處理進度</th>
                        <th>尺寸/重量</th>
                        <th>郵資</th>
                        <th>支付狀態</th>
                        <th>代收入帳狀態</th>
                        <th>匯入時間</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['tracking_number']); ?></td>
                        <td><?php echo $row['original_tracking'] !== null && $row['original_tracking'] !== '' ? htmlspecialchars($row['original_tracking']) : '無'; ?></td>
                        <td><?php echo htmlspecialchars($row['batch_no']); ?></td>
                        <td><?php echo htmlspecialchars($row['received_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['receiver_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['cod_amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td><?php echo $row['product_name'] !== null && $row['product_name'] !== '' ? htmlspecialchars($row['product_name']) : '無'; ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['size_weight']); ?></td>
                        <td><?php echo htmlspecialchars($row['postage']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['cod_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<style>
.search-form { margin-bottom: 1.5rem; }
.search-group { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
.search-input-wrapper { position: relative; flex: 1; min-width: 180px; }
.search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-size: 1rem; color: #666; pointer-events: none; }
.search-input { width: 100%; padding: 0.8rem 1rem 0.8rem 2.2rem; border: 2px solid rgba(200,16,46,0.15); border-radius: 12px; font-size: 0.95rem; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); transition: all 0.3s ease; }
.search-input:focus { outline: none; border-color: #C8102E; box-shadow: 0 0 0 3px rgba(200,16,46,0.1); background: rgba(255,255,255,0.95); }
.btn-primary { background: linear-gradient(135deg, #7b61ff, #4caaff); color: white; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; }
.btn-primary:hover { background: #4caaff; }
.btn-outline { background: #fff; color: #7b61ff; border: 2px solid #7b61ff; border-radius: 8px; padding: 0.75rem 1.5rem; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; }
.btn-outline:hover { background: #f1f5f9; }
.content-card { background: rgba(255, 255, 255, 0.95); border-radius: 20px; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 15px 35px rgba(0,0,0,0.08); border: 1px solid rgba(200,16,46,0.1); transition: all 0.3s ease; }
.table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid rgba(200,16,46,0.1); }
.table-header h3 { font-size: 1.3rem; color: #C8102E; margin: 0; font-weight: 700; }
.table-stats { display: flex; gap: 1.5rem; }
.stat-item { text-align: center; }
.stat-number { display: block; font-size: 1.5rem; font-weight: 700; color: #C8102E; line-height: 1; }
.stat-label { font-size: 0.85rem; color: #666; margin-top: 0.25rem; }
.records-table-container {
    overflow-x: auto;
    border-radius: 16px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    background: rgba(255,255,255,0.95);
    margin-top: 2rem;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 1rem;
    background: #fff;
}
.data-table th {
    background: linear-gradient(90deg, #C8102E 0%, #a30d23 100%);
    color: #fff;
    font-weight: 700;
    padding: 1.1rem 0.7rem;
    border-bottom: 2px solid #e1e5e9;
    text-align: left;
    letter-spacing: 0.02em;
}
.data-table td {
    padding: 1rem 0.7rem;
    border-bottom: 1px solid #e1e5e9;
    vertical-align: top;
    color: #2d3748;
    background: #fff;
}
.data-table tr:hover {
    background: #f8f9fa;
}
.data-table th, .data-table td {
    min-width: 110px;
}
.data-table td {
    font-size: 0.98rem;
    line-height: 1.5;
}
.data-table td:last-child, .data-table th:last-child {
    min-width: 140px;
}
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
}
.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}
</style>
