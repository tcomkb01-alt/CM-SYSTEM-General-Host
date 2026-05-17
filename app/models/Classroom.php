<?php

namespace App\Models;

use Core\Model;

class Classroom extends Model
{
    protected string $table = 'classrooms';

    /**
     * ดึงข้อมูลชั้นเรียนทั้งหมดพร้อมชื่ออาจารย์ที่ดูแล (ถ้ามี)
     */
    public function getAllWithAdmin(): array
    {
        $sql = "SELECT c.*, u.display_name as admin_name 
                FROM {$this->table} c
                LEFT JOIN users u ON c.admin_id = u.id
                ORDER BY c.created_at DESC";
        return $this->db->query($sql);
    }

    /**
     * ค้นหาชั้นเรียน
     */
    public function search(string $keyword): array
    {
        $sql = "SELECT c.*, u.display_name as admin_name 
                FROM {$this->table} c
                LEFT JOIN users u ON c.admin_id = u.id
                WHERE c.subject_name LIKE :keyword 
                OR c.subject_code LIKE :keyword 
                OR c.room_code LIKE :keyword
                ORDER BY c.created_at DESC";
        return $this->db->query($sql, ['keyword' => "%{$keyword}%"]);
    }

    /**
     * ค้นหาชั้นเรียนด้วยรหัสห้อง
     */
    public function findByRoomCode(string $code): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE room_code = :code LIMIT 1";
        return $this->db->queryOne($sql, ['code' => $code]);
    }

    /**
     * ดึงชั้นเรียนที่อาจารย์คนนี้ดูแล
     */
    public function getByAdmin(int $adminId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE admin_id = :id ORDER BY created_at DESC";
        return $this->db->query($sql, ['id' => $adminId]);
    }
}
