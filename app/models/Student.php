<?php

namespace App\Models;

use Core\Model;

class Student extends Model
{
    protected string $table = 'students';

    /**
     * ค้นหานักเรียนตามเงื่อนไข (เช่น ชื่อ หรือ รหัส)
     */
    public function search(string $keyword): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE student_code LIKE :keyword 
                OR first_name LIKE :keyword 
                OR last_name LIKE :keyword 
                OR class_level LIKE :keyword 
                ORDER BY class_level ASC, student_number ASC";
        
        return $this->db->query($sql, ['keyword' => "%{$keyword}%"]);
    }

    /**
     * ดึงรายการชั้นเรียนทั้งหมดที่มีในระบบ
     */
    public function getClassLevels(): array
    {
        $sql = "SELECT DISTINCT class_level FROM {$this->table} ORDER BY class_level ASC";
        return $this->db->query($sql);
    }

    /**
     * กรองนักเรียนตามชั้นเรียนและคำค้นหา
     */
    public function filter(?string $classLevel = null, ?string $keyword = null): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($classLevel) {
            $sql .= " AND class_level = :class_level";
            $params['class_level'] = $classLevel;
        }

        if ($keyword) {
            $sql .= " AND (student_code LIKE :keyword OR first_name LIKE :keyword OR last_name LIKE :keyword)";
            $params['keyword'] = "%{$keyword}%";
        }

        $sql .= " ORDER BY class_level ASC, student_number ASC";
        return $this->db->query($sql, $params);
    }
}
