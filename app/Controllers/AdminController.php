<?php
// app/Controllers/AdminController.php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class AdminController extends Controller {
    
    public function __construct() {
        // 確保使用者已登入且為管理員
        AuthMiddleware::requireLogin();
        if (!$this->isAdmin()) {
            $this->redirect(BASE_URL . '?error=權限不足');
        }
    }
    
    public function dashboard() {
        $this->setGlobalViewData();
        $this->view('admin/dashboard', [
            'title' => '管理後台'
        ]);
    }
    
    public function users() {
        $this->setGlobalViewData();
        $this->view('admin/users', [
            'title' => '使用者管理'
        ]);
    }
    
    public function createUser() {
        $this->setGlobalViewData();
        $this->view('admin/create-user', [
            'title' => '新增使用者'
        ]);
    }
    
    public function editUser() {
        $this->setGlobalViewData();
        $this->view('admin/edit-user', [
            'title' => '編輯使用者'
        ]);
    }
    
    public function announcements() {
        $this->setGlobalViewData();
        $this->view('admin/announcements', [
            'title' => '公告管理'
        ]);
    }
    
    public function createAnnouncement() {
        $this->setGlobalViewData();
        $this->view('admin/create-announcement', [
            'title' => '新增公告'
        ]);
    }
    
    public function editAnnouncement() {
        $this->setGlobalViewData();
        $this->view('admin/edit-announcement', [
            'title' => '編輯公告'
        ]);
    }
} 