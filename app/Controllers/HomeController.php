<?php
// app/Controllers/HomeController.php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Models/Database.php';
require_once __DIR__ . '/../Models/Announcement.php';

class HomeController extends Controller {
    private $announcementModel;
    
    public function __construct() {
        $this->announcementModel = new Announcement();
        $this->setGlobalViewData();
    }
    
    public function index() {
        // 首頁顯示最新公告
        $announcements = $this->announcementModel->getPublicAnnouncements(5);
        
        $this->view('home/index', [
            'title' => '首頁',
            'announcements' => $announcements,
            'pageType' => 'home'
        ]);
    }
    
    public function announcements() {
        $announcements = $this->announcementModel->getPublicAnnouncements();
        
        $this->view('announcements/index', [
            'title' => '最新公告',
            'announcements' => $announcements,
            'pageType' => 'announcements'
        ]);
    }
    
    public function holidays() {
        // 直接載入假日公告頁面
        // 頁面內部會自動載入 HolidayCalendar 模型
        $this->view('announcements/holidays', [
            'title' => '假日資訊',
            'pageType' => 'announcements'
        ]);
    }
    
    public function handbook() {
        $handbook = $this->announcementModel->getHandbookContents();
        
        $this->view('announcements/handbook', [
            'title' => '員工手冊',
            'handbook' => $handbook,
            'pageType' => 'announcements'
        ]);
    }
    
    public function company() {
        $this->view('company/index', [
            'title' => '公司資訊',
            'pageType' => 'company'
        ]);
    }
    
    public function companyFloor() {
        $this->view('company/floor', [
            'title' => '樓層圖',
            'pageType' => 'company'
        ]);
    }
    
    public function companyContacts() {
        $this->view('company/contacts', [
            'title' => '聯絡資訊',
            'pageType' => 'company'
        ]);
    }
    
    public function companyNas() {
        $this->view('company/nas', [
            'title' => 'NAS介紹',
            'pageType' => 'company'
        ]);
    }
} 