<?php
// app/Models/CompanyInfo.php

namespace App\Models;

/**
 * 公司資訊模型
 * 
 * 處理公司樓層配置和聯絡人資訊
 */
class CompanyInfo extends Model {
    /**
     * 取得樓層配置資訊
     * 
     * @return array 樓層配置資訊
     */
    // public function getFloorInfo() {
    //     return $this->db->query('SELECT * FROM floor_info');
    // }

    /**
     * 取得部門聯絡資訊
     * 
     * @return array 部門聯絡資訊
     */
    // public function getDepartmentContacts() {
    //     $sql = "SELECT * FROM department_contacts ORDER BY department_id DESC";
    //     return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    // }

    /**
     * 取得員工座位資訊
     * 
     * @return array 員工座位資訊
     */
    // public function getEmployeeSeats() {
    //     $sql = "SELECT * FROM employee_seats ORDER BY seat_id DESC";
    //     return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    // }

    /**
     * 取得分機資訊
     * 
     * @return array 分機資訊
     */
    // public function getExtensionNumbers() {
    //     $sql = "SELECT * FROM extension_numbers ORDER BY extension_id DESC";
    //     return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    // }
} 