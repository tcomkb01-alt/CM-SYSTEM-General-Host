<?php

namespace App\Models;

use Core\Model;

class ClassroomStudent extends Model
{
    protected string $table = 'classroom_students';

    /**
     * ดึงรายชื่อนักเรียนในห้องเรียน
     */
    public function getStudentsInClassroom(int $classroomId): array
    {
        $sql = "SELECT s.*, cs.joined_at 
                FROM students s
                JOIN {$this->table} cs ON s.id = cs.student_id
                WHERE cs.classroom_id = :id
                ORDER BY s.student_number ASC, s.student_code ASC";
        return $this->db->query($sql, ['id' => $classroomId]);
    }

    /**
     * เพิ่มนักเรียนเข้าห้อง
     */
    public function addStudent(int $classroomId, int $studentId): bool
    {
        $sql = "INSERT INTO {$this->table} (classroom_id, student_id) VALUES (:cid, :sid)";
        return $this->db->execute($sql, ['cid' => $classroomId, 'sid' => $studentId]);
    }

    /**
     * เพิ่มนักเรียนหลายคนเข้าห้องพร้อมกัน
     */
    public function addMultipleStudents(int $classroomId, array $studentIds): int
    {
        $added = 0;
        foreach ($studentIds as $sid) {
            if (!$this->isStudentInClassroom($classroomId, $sid)) {
                $this->addStudent($classroomId, (int)$sid);
                $added++;
            }
        }
        return $added;
    }

    /**
     * ลบนนักเรียนออกจากห้อง
     */
    public function removeStudent(int $classroomId, int $studentId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE classroom_id = :cid AND student_id = :sid";
        return $this->db->execute($sql, ['cid' => $classroomId, 'sid' => $studentId]);
    }
    
    /**
     * ตรวจสอบว่านักเรียนอยู่ในห้องหรือยัง
     */
    public function isStudentInClassroom(int $classroomId, int $studentId): bool
    {
        $sql = "SELECT id FROM {$this->table} WHERE classroom_id = :cid AND student_id = :sid LIMIT 1";
        return (bool) $this->db->queryOne($sql, ['cid' => $classroomId, 'sid' => $studentId]);
    }
}
