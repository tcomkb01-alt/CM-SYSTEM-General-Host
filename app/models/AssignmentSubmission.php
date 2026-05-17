<?php

namespace App\Models;

use Core\Model;

class AssignmentSubmission extends Model
{
    protected string $table = 'assignment_submissions';

    /**
     * ดึงรายการส่งงานของนักเรียนทุกคนในงานชิ้นนั้นๆ
     */
    public function getSubmissions(int $assignmentId): array
    {
        $sql = "SELECT s.id as student_id, s.prefix, s.first_name, s.last_name, s.student_number, s.student_code,
                sub.id as submission_id, sub.score, sub.status, sub.submitted_at
                FROM students s
                JOIN classroom_students cs ON s.id = cs.student_id
                JOIN assignments a ON cs.classroom_id = a.classroom_id
                LEFT JOIN {$this->table} sub ON (sub.assignment_id = a.id AND sub.student_id = s.id)
                WHERE a.id = :aid
                ORDER BY s.student_number ASC";
        return $this->db->query($sql, ['aid' => $assignmentId]);
    }

    /**
     * บันทึกคะแนน / สถานะการส่ง
     */
    public function updateScore(int $assignmentId, int $studentId, $score, string $status = 'graded'): bool
    {
        // ปรับค่าคะแนนว่างให้เป็น NULL สำหรับ DB
        $scoreValue = ($score === '' || $score === null) ? null : $score;

        // ตรวจสอบว่ามี record หรือยัง
        $sqlCheck = "SELECT id FROM {$this->table} WHERE assignment_id = :aid AND student_id = :sid";
        $existing = $this->db->queryOne($sqlCheck, ['aid' => $assignmentId, 'sid' => $studentId]);

        if ($existing) {
            $sql = "UPDATE {$this->table} SET score = :score, status = :status, submitted_at = NOW() WHERE id = :id";
            return $this->db->execute($sql, [
                'score' => $scoreValue, 
                'status' => $status, 
                'id' => $existing['id']
            ]);
        } else {
            $sql = "INSERT INTO {$this->table} (assignment_id, student_id, score, status, submitted_at) 
                    VALUES (:aid, :sid, :score, :status, NOW())";
            return $this->db->execute($sql, [
                'aid' => $assignmentId, 
                'sid' => $studentId, 
                'score' => $scoreValue, 
                'status' => $status
            ]);
        }
    }

    /**
     * ดึงสถิติรายบุคคล (สำหรับ Student Portal)
     */
    public function getStudentAssignments(int $classroomId, int $studentId): array
    {
        $sql = "SELECT a.*, sub.score, sub.status, sub.submitted_at
                FROM assignments a
                LEFT JOIN {$this->table} sub ON (sub.assignment_id = a.id AND sub.student_id = :sid)
                WHERE a.classroom_id = :cid
                ORDER BY a.due_date ASC";
        return $this->db->query($sql, ['cid' => $classroomId, 'sid' => $studentId]);
    }

    /**
     * ดึงสถิติการส่งงานของนักเรียนทุกคนในห้องเรียน
     */
    public function getClassroomAssignmentStats(int $classroomId): array
    {
        $sql = "SELECT s.id as student_id,
                COUNT(a.id) as total_assignments,
                COUNT(sub.id) as submitted_count,
                SUM(sub.score) as total_score,
                SUM(a.max_score) as max_possible_score
                FROM students s
                JOIN classroom_students cs ON s.id = cs.student_id
                JOIN assignments a ON cs.classroom_id = a.classroom_id
                LEFT JOIN {$this->table} sub ON (sub.assignment_id = a.id AND sub.student_id = s.id)
                WHERE cs.classroom_id = :cid
                GROUP BY s.id";
        $results = $this->db->query($sql, ['cid' => $classroomId]);
        
        $stats = [];
        foreach ($results as $row) {
            $stats[$row['student_id']] = $row;
        }
        return $stats;
    }
}
