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
    public function getFloorInfo() {
        $sql = "SELECT * FROM floor_info ORDER BY floor_number DESC";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 取得部門聯絡資訊
     * 
     * @return array 部門聯絡資訊
     */
    public function getDepartmentContacts() {
        $sql = "SELECT * FROM department_contacts ORDER BY floor_number DESC, department_name";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 取得員工座位資訊
     * 
     * @return array 員工座位資訊
     */
    public function getEmployeeSeats() {
        $sql = "SELECT * FROM employee_seats ORDER BY floor_number DESC, seat_number";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * 取得分機資訊
     * 
     * @return array 分機資訊
     */
    public function getExtensionNumbers() {
        $sql = "SELECT * FROM extension_numbers ORDER BY extension_number";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }
} 