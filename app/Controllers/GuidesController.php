<?php
// app/Controllers/GuidesController.php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class GuidesController extends Controller {
    
    public function __construct() {
        AuthMiddleware::requireLogin();
    }
    
    public function windows() {
        $this->setGlobalViewData();
        $this->view('guides/windows', [
            'title' => 'Windows 操作指引'
        ]);
    }
    
    public function printer() {
        $this->setGlobalViewData();
        $this->view('guides/printer', [
            'title' => '印表機操作指引'
        ]);
    }
    
    public function mac() {
        $this->setGlobalViewData();
        $this->view('guides/mac', [
            'title' => 'MAC 操作指引'
        ]);
    }
    
    public function nas() {
        $this->setGlobalViewData();
        $this->view('guides/nas', [
            'title' => 'NAS 操作指引'
        ]);
    }
    
    public function email() {
        $this->setGlobalViewData();
        $this->view('guides/email', [
            'title' => '電子郵件操作指引'
        ]);
    }
    
    public function taxExempt() {
        $this->setGlobalViewData();
        $this->view('guides/tax-exempt', [
            'title' => '文化部免稅操作指引'
        ]);
    }
} 