<?php

namespace App\Models;

use Core\Model;

class Assignment extends Model
{
    protected string $table = 'assignments';

    /**
     * ดึงงานทั้งหมดในห้องเรียน
     */
    public function getByClassroom(int $classroomId): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE classroom_id = :cid ORDER BY created_at DESC";
        return $this->db->query($sql, ['cid' => $classroomId]);
    }

    /**
     * ดึงข้อมูลงานพร้อมสถิติการส่ง (จำนวนคนที่ส่งแล้ว / จำนวนคนทั้งหมด)
     */
    public function getWithStats(int $classroomId): array
    {
        $sql = "SELECT a.*, 
                (SELECT COUNT(*) FROM assignment_submissions WHERE assignment_id = a.id AND status != 'pending') as submitted_count,
                (SELECT COUNT(*) FROM classroom_students WHERE classroom_id = a.classroom_id) as total_students
                FROM {$this->table} a
                WHERE a.classroom_id = :cid
                ORDER BY a.created_at DESC";
        return $this->db->query($sql, ['cid' => $classroomId]);
    }
}
