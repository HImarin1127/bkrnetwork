<?php
// app/Models/Announcement.php

require_once __DIR__ . '/Model.php';

class Announcement extends Model {
    protected $table = 'announcements';
    
    public function getPublicAnnouncements($limit = 10) {
        return $this->where(
            ['status' => 'published', 'type' => 'general'], 
            'created_at DESC', 
            $limit
        );
    }
    
    public function getHolidayAnnouncements() {
        return $this->where(
            ['status' => 'published', 'type' => 'holiday'], 
            'date DESC'
        );
    }
    
    public function getHandbookContents() {
        return $this->where(
            ['status' => 'published', 'type' => 'handbook'], 
            'sort_order ASC'
        );
    }
    
    public function createAnnouncement($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->create($data);
    }
    
    public function updateAnnouncement($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->update($id, $data);
    }
    
    public function getAnnouncementsByType($type) {
        return $this->where(['type' => $type], 'created_at DESC');
    }
    
    public function toggleStatus($id) {
        $announcement = $this->find($id);
        if ($announcement) {
            $newStatus = $announcement['status'] === 'published' ? 'draft' : 'published';
            return $this->updateAnnouncement($id, ['status' => $newStatus]);
        }
        return false;
    }
} 