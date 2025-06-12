<?php
// public/onboarding.php
require_once __DIR__ . '/../src/auth.php';
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$pageTitle = '新人到職';
include __DIR__ . '/include/header.php';
?>
<div class="dashboard-layout">
  <div class="sidebar">
    <div class="sidebar-header"><h3>員工內部網頁</h3></div>
    <nav class="sidebar-nav">
      <a href="dashboard.php" class="nav-item"><i class="fas fa-history"></i> 匯入紀錄</a>
      <a href="import.php" class="nav-item"><i class="fas fa-upload"></i> CSV 匯入</a>
      <a href="settings.php" class="nav-item"><i class="fas fa-cog"></i> 系統設定</a>
      <a href="onboarding.php" class="nav-item active"><i class="fas fa-user-plus"></i> 新人到職</a>
      <a href="logout.php" class="nav-item">
        <i class="fas fa-sign-out-alt"></i> 登出
      </a>
    </nav>
  </div>
</div>
  <div class="main-content">
    <div class="screen-header">
      <div class="screen-title">新人到職</div>
      <div class="screen-subtitle">請選擇欲填寫之區塊</div>
    </div>
    <div class="screen-content">
      <div class="btn-group mb-4" role="group" style="gap:15px;">
        <button type="button" id="btnHr"     class="btn btn-outline-primary">人資相關</button>
        <button type="button" id="btnFinance" class="btn btn-outline-success">財務相關</button>
        <button type="button" id="btnIt"     class="btn btn-outline-info">資訊相關</button>
      </div>

      <!-- 人資相關 -->
      <div id="sectionHr" style="display:none;">
        <form>
          <div class="row gy-3">
            <div class="col-md-6">
              <label class="form-label">填寫日期</label>
              <input type="date" name="hr_fill_date" class="form-input form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">到(復)職日期</label>
              <input type="date" name="hr_onboard_date" class="form-input form-control">
            </div>
            <div class="col-12">
              <label class="form-label">報到時間</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="hr_report_time[]" value="09:00">
                <label class="form-check-label">09:00</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="hr_report_time[]" value="09:30">
                <label class="form-check-label">09:30</label>
              </div>
              <div class="form-check form-check-inline">
                其他：
                <input type="time" name="hr_report_time_other" class="form-input form-control d-inline-block" style="width:auto;">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">申請單位</label>
              <input type="text" name="hr_applicant_unit" class="form-input form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">到職單位</label>
              <input type="text" name="hr_onboard_unit" class="form-input form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">姓名</label>
              <input type="text" name="hr_name" class="form-input form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">職稱</label>
              <input type="text" name="hr_title" class="form-input form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">分機</label>
              <input type="text" name="hr_extension" class="form-input form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">個人連絡電話</label>
              <input type="tel" name="hr_mobile" class="form-input form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">個人聯絡 EMAIL</label>
              <input type="email" name="hr_email" class="form-input form-control">
            </div>
            <div class="col-md-12">
              <label class="form-label">身份類別</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="hr_category" value="正職員工">
                <label class="form-check-label">正職員工</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="hr_category" value="計時員工">
                <label class="form-check-label">計時員工</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="hr_category" value="契約員工">
                <label class="form-check-label">契約員工</label>
              </div>
            </div>
            <div class="col-md-12">
              <label class="form-label">薪資計算方式 (手寫)</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="hr_pay_type" value="月薪">
                <label class="form-check-label">月薪_____/月</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="hr_pay_type" value="時薪">
                <label class="form-check-label">時薪_____/小時</label>
              </div>
            </div>
            <div class="col-md-12">
              <label class="form-label">勞健保/加保</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="hr_insurance[]" value="勞保" checked>
                <label class="form-check-label">勞保</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="hr_insurance[]" value="健保" checked>
                <label class="form-check-label">健保</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="hr_insurance[]" value="眷屬健保">
                <label class="form-check-label">眷屬健保</label>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">眷屬健保資料</label>
              <textarea name="hr_family_info" class="form-input form-control" rows="5"
                placeholder="家屬身分證字號：&#10;家屬姓名：&#10;家屬稱謂：&#10;家屬生日：&#10;家屬國籍：&#10;是否扶養：(Y/N)&#10;是否加健保：Y(預設)"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">應徵管道</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="hr_source" value="人力銀行媒合">
                <label class="form-check-label">人力銀行媒合</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="hr_source" value="自薦或第三人推薦">
                <label class="form-check-label">自薦或第三人推薦</label>
              </div>
              <div class="form-check form-check-inline">
                其他：
                <input type="text" name="hr_source_other" class="form-input form-control d-inline-block" style="width:auto;">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">報到交辦人員</label>
              <input type="text" name="hr_assigned" class="form-input form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">填寫人員</label>
              <input type="text" name="hr_filled_by" class="form-input form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">資訊帳戶申請</label><br>
              <div class="input-group">
                <span class="input-group-text">公司EMAIL</span>
                <input type="email" name="hr_company_email" class="form-input form-control" placeholder="__@bookrep.com.tw">
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- 財務相關 (保留原 Finance) -->
      <!-- 財務相關表單 -->
<div id="sectionFinance" style="display:none;">
  <form>
    <div class="row gy-3">
      <div class="col-md-6">
        <label class="form-label">填寫日期</label>
        <input type="date" name="fin_fill_date" class="form-input form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">到(復)職日期</label>
        <input type="date" name="fin_onboard_date" class="form-input form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">到職單位 (費用申報單位)</label>
        <input type="text" name="fin_unit" class="form-input form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">姓名</label>
        <input type="text" name="fin_name" class="form-input form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">職稱</label>
        <input type="text" name="fin_title" class="form-input form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">分機</label>
        <input type="text" name="fin_extension" class="form-input form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">身份類別</label><br>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="fin_category" value="正職員工">
          <label class="form-check-label">正職員工</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="fin_category" value="計時員工">
          <label class="form-check-label">計時員工</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="fin_category" value="契約員工">
          <label class="form-check-label">契約員工</label>
        </div>
      </div>
      <div class="col-md-12">
        <label class="form-label">薪資計算方式 (請手寫)</label><br>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="fin_pay_type" value="月薪">
          <label class="form-check-label">月薪 ____ / 月</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="fin_pay_type" value="時薪">
          <label class="form-check-label">時薪 ____ / 小時</label>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">時薪上班時間</label>
        <input type="text" name="fin_hourly_time" class="form-input form-control" placeholder="例：09:00-17:00">
      </div>
      <div class="col-md-6">
        <label class="form-label">勞健保/加保</label><br>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="fin_insurance[]" value="勞保" checked>
          <label class="form-check-label">勞保</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="fin_insurance[]" value="健保" checked>
          <label class="form-check-label">健保</label>
        </div>
        <div class="form-check form-check-inline">
          <input class="form-check-input" type="checkbox" name="fin_insurance[]" value="眷屬健保">
          <label class="form-check-label">眷屬健保</label>
        </div>
      </div>
      <div class="col-12">
        <label class="form-label">眷屬健保資料</label>
        <textarea name="fin_family_info" class="form-input form-control" rows="5"
          placeholder="家屬身分證字號：&#10;家屬姓名：&#10;家屬稱謂：&#10;家屬生日：&#10;家屬國籍：&#10;是否扶養：(Y/N)&#10;是否加健保：Y(預設)"></textarea>
      </div>
      <div class="col-md-6">
        <label class="form-label">填寫人員</label>
        <input type="text" name="fin_filled_by" class="form-input form-control">
      </div>
      <div class="col-12">
        <button type="submit" class="btn btn-success">送出</button>
      </div>
    </div>
  </form>
</div>

      <!-- 資訊相關 -->
      <div id="sectionIt" style="display:none;">
        <form>
          <div class="row gy-3">
            <div class="col-md-6">
              <label class="form-label">填寫日期</label>
              <input type="date" name="it_fill_date" class="form-input form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">到(復)職日期</label>
              <input type="date" name="it_onboard_date" class="form-input form-control">
            </div>
            <div class="col-12">
              <label class="form-label">報到時間</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="it_report_time[]" value="09:00">
                <label class="form-check-label">09:00</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="it_report_time[]" value="09:30">
                <label class="form-check-label">09:30</label>
              </div>
              <div class="form-check form-check-inline">
                其他：
                <input type="time" name="it_report_time_other" class="form-input form-control d-inline-block" style="width:auto;">
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label">申請單位</label>
              <input type="text" name="it_applicant_unit" class="form-input form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">到職單位</label>
              <input type="text" name="it_onboard_unit" class="form-input form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">姓名</label>
              <input type="text" name="it_name" class="form-input form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">職稱</label>
              <input type="text" name="it_title" class="form-input form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">分機</label>
              <input type="text" name="it_extension" class="form-input form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">填寫人員</label>
              <input type="text" name="it_filled_by" class="form-input form-control">
            </div>
            <div class="col-12">
              <label class="form-label">軟／硬體申請</label><br>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="it_hw_choice" value="A" required>
                <label class="form-check-label">A. 採購新電腦設備一組，含作業系統及文書處理軟體</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="it_hw_choice" value="B">
                <label class="form-check-label">B. 指定承接前用者電腦 ___ (請填寫機號)</label>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">螢幕規格</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="it_monitor" value="24吋">
                <label class="form-check-label">24吋：6,000內</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="it_monitor" value="27吋">
                <label class="form-check-label">27吋：10,000內</label>
              </div>
              <div class="form-check form-check-inline">
                其他：
                <input type="text" name="it_monitor_other" class="form-input form-control d-inline-block" style="width:auto;">
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">PC規格 (螢幕另計)</label>
              <textarea name="it_pc_specs" class="form-input form-control" rows="3"
                placeholder="□ 一般文書：50,000內&#10;□ 影像剪輯：100,000內&#10;□ 其他："></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">電腦承接處理方式</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="it_service" value="重灌系統">
                <label class="form-check-label">重灌系統</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="it_service" value="保留原系統">
                <label class="form-check-label">保留原系統</label>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">額外軟體需求</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="it_extra_sw[]" value="無" checked>
                <label class="form-check-label">無</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="it_extra_sw[]" value="其他">
                <label class="form-check-label">其他：</label>
                <input type="text" name="it_extra_sw_other" class="form-input form-control d-inline-block" style="width:auto;">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">資訊帳戶申請 (EMAIL)</label>
              <input type="email" name="it_email" class="form-input form-control" placeholder="__@bookrep.com.tw">
            </div>
            <div class="col-md-6">
              <label class="form-label">開通公區目錄</label>
              <input type="text" name="it_share_folder" class="form-input form-control" placeholder="例：平台_資訊">
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="it_mf2000" value="1">
                <label class="form-check-label">MF2000 帳號申請</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="it_install_songyue" value="1">
                <label class="form-check-label">安裝崧月系統</label>
              </div>
            </div>
            <div class="col-12">
              <button type="submit" class="btn btn-success">送出申請</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/include/footer.php'; ?>

<script>
// 切換區段
document.getElementById('btnHr').addEventListener('click', () => {
  document.getElementById('sectionHr').style.display = 'block';
  document.getElementById('sectionFinance').style.display = 'none';
  document.getElementById('sectionIt').style.display = 'none';
});
document.getElementById('btnFinance').addEventListener('click', () => {
  document.getElementById('sectionHr').style.display = 'none';
  document.getElementById('sectionFinance').style.display = 'block';
  document.getElementById('sectionIt').style.display = 'none';
});
document.getElementById('btnIt').addEventListener('click', () => {
  document.getElementById('sectionHr').style.display = 'none';
  document.getElementById('sectionFinance').style.display = 'none';
  document.getElementById('sectionIt').style.display = 'block';
});
// 預設顯示 Finance
document.getElementById('btnFinance').click();
</script>