<?php
// 取得表單資料
$employee_name = $_POST['employee_name'] ?? '';
$department = $_POST['department'] ?? '';
$job_title = $_POST['job_title'] ?? '';
$extension = $_POST['extension'] ?? '';
$identity_type = $_POST['identity_type'] ?? '';
$salary_type = $_POST['salary_type'] ?? '';
$monthly_salary = $_POST['monthly_salary'] ?? '';
$hourly_salary = $_POST['hourly_salary'] ?? '';
$hourly_hours = $_POST['hourly_hours'] ?? '';
$labor_insurance = isset($_POST['labor_insurance']) ? '■' : '□';
$health_insurance = isset($_POST['health_insurance']) ? '■' : '□';
$dependent_insurance = isset($_POST['dependent_insurance']) ? '■' : '□';
$finance_preparer = $_POST['finance_preparer'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>人員到職表單預覽</title>
    <style>
        body { font-family: "微軟正黑體", Arial, sans-serif; margin: 40px; }
        .form-title { font-size: 24px; font-weight: bold; margin-bottom: 20px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 32px; }
        th, td { border: 1px solid #333; padding: 8px 12px; font-size: 16px; }
        th { background: #f5f5f5; text-align: left; }
        .sign-table th, .sign-table td { border: none; }
        .sign-line { display: inline-block; border-bottom: 1px solid #333; width: 180px; height: 28px; vertical-align: bottom; margin: 0 16px; }
        .sign-label { font-size: 18px; }
        .print-btn { position: fixed; top: 20px; right: 40px; padding: 10px 24px; font-size: 16px; background: #2d8cf0; color: #fff; border: none; border-radius: 4px; cursor: pointer; z-index: 1000; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">列印</button>
    <div class="form-title">人員到職表單（預覽）</div>
    <table>
        <tr>
            <th>姓名</th>
            <td><?= htmlspecialchars($employee_name) ?></td>
            <th>部門</th>
            <td><?= htmlspecialchars($department) ?></td>
        </tr>
        <tr>
            <th>職稱</th>
            <td><?= htmlspecialchars($job_title) ?></td>
            <th>分機</th>
            <td><?= htmlspecialchars($extension) ?></td>
        </tr>
        <tr>
            <th>身份類別</th>
            <td colspan="3">
                <span><?= $identity_type === 'fulltime' ? '■' : '□' ?>正職員工</span>
                <span style="margin-left:24px;"> <?= $identity_type === 'parttime' ? '■' : '□' ?>計時員工</span>
                <span style="margin-left:24px;"> <?= $identity_type === 'contract' ? '■' : '□' ?>契約員工</span>
            </td>
        </tr>
        <tr>
            <th>薪資計算方式</th>
            <td colspan="3">
                <span><?= $salary_type === 'monthly' ? '■' : '□' ?>月薪 <?= htmlspecialchars($monthly_salary) ?> 元/月</span>
                <span style="margin-left:24px;"> <?= $salary_type === 'hourly' ? '■' : '□' ?>時薪 <?= htmlspecialchars($hourly_salary) ?> 元/小時，上班時數：<?= htmlspecialchars($hourly_hours) ?></span>
            </td>
        </tr>
        <tr>
            <th>勞健保/加保</th>
            <td colspan="3">
                <span><?= $labor_insurance ?>勞保</span>
                <span style="margin-left:24px;"> <?= $health_insurance ?>健保</span>
                <span style="margin-left:24px;"> <?= $dependent_insurance ?>眷屬健保</span>
            </td>
        </tr>
        <tr>
            <th>填寫人員</th>
            <td colspan="3"><?= htmlspecialchars($finance_preparer) ?></td>
        </tr>
    </table>
    <table class="sign-table" style="width:100%; margin-top:60px;">
        <tr>
            <td class="sign-label">部門主管：</td>
            <td><span class="sign-line"></span></td>
            <td class="sign-label">總務人資部：</td>
            <td><span class="sign-line"></span></td>
            <td class="sign-label">單位主管：</td>
            <td><span class="sign-line"></span></td>
        </tr>
        <tr>
            <td colspan="6" style="font-size:12px;color:#888;">114/04 更新</td>
        </tr>
    </table>
</body>
</html> 