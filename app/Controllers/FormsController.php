<?php
// app/Controllers/FormsController.php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class FormsController extends Controller {
    
    public function __construct() {
        // 確保使用者已登入
        AuthMiddleware::requireLogin();
    }
    
    public function bookRequest() {
        $this->setGlobalViewData();
        $this->view('forms/book-request', [
            'title' => '購書申請'
        ]);
    }
    
    public function personnelChange() {
        $this->setGlobalViewData();
        $this->view('forms/personnel-change', [
            'title' => '人員異動申請'
        ]);
    }
    
    public function leaveRequest() {
        $this->setGlobalViewData();
        $this->view('forms/leave-request', [
            'title' => '請假申請'
        ]);
    }
    
    public function overtimeRequest() {
        $this->setGlobalViewData();
        $this->view('forms/overtime-request', [
            'title' => '加班申請'
        ]);
    }
    
    public function businessTrip() {
        $this->setGlobalViewData();
        $this->view('forms/business-trip', [
            'title' => '出差申請'
        ]);
    }
    
    public function educationTraining() {
        $this->setGlobalViewData();
        $this->view('forms/education-training', [
            'title' => '教育訓練申請'
        ]);
    }
    
    public function equipmentPurchase() {
        $this->setGlobalViewData();
        $this->view('forms/equipment-purchase', [
            'title' => '設備採購申請'
        ]);
    }
} 