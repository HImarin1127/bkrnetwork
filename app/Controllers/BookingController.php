<?php
// app/Controllers/BookingController.php

require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class BookingController extends Controller {
    
    public function __construct() {
        AuthMiddleware::requireLogin();
    }
    
    public function meetingRoom() {
        $this->setGlobalViewData();
        $this->view('booking/meeting-room', [
            'title' => '會議室預約'
        ]);
    }
    
    public function equipment() {
        $this->setGlobalViewData();
        $this->view('booking/equipment', [
            'title' => '設備借用'
        ]);
    }
    
    public function vehicle() {
        $this->setGlobalViewData();
        $this->view('booking/vehicle', [
            'title' => '車輛預約'
        ]);
    }
} 