<?php

namespace App\Models;

use Core\Model;

class Attendance extends Model
{
    protected string $table = 'attendance_records';

    /**
     * ดึงรอบการเช็คชื่อทั้งหมดในห้องเรียน
     */
    public function getSessions(int $classroomId): array
    {
        $sql = "SELECT * FROM attendance_sessions 
                WHERE classroom_id = :id 
                ORDER BY session_date DESC, started_at DESC";
        return $this->db->query($sql, ['id' => $classroomId]);
    }

    /**
     * ดึงบันทึกการเข้าเรียนของรอบนั้นๆ
     */
    public function getRecords(int $sessionId): array
    {
        $sql = "SELECT ar.*, s.student_code, s.first_name, s.last_name, s.prefix, s.student_number
                FROM {$this->table} ar
                JOIN students s ON ar.student_id = s.id
                WHERE ar.session_id = :id
                ORDER BY s.student_number ASC";
        return $this->db->query($sql, ['id' => $sessionId]);
    }

    /**
     * สร้างรอบการเช็คชื่อใหม่
     */
    public function createSession(array $data): int
    {
        $sql = "INSERT INTO attendance_sessions (classroom_id, admin_id, session_date, period_number, status, started_at) 
                VALUES (:cid, :aid, :sdate, :period, 'active', NOW())";
        $this->db->execute($sql, $data);
        return (int) $this->db->lastInsertId();
    }

    /**
     * บันทึกการเข้าเรียน
     */
    public function upsertRecord(array $data): bool
    {
        $sql = "INSERT INTO {$this->table} (session_id, student_id, status, check_method, checked_at) 
                VALUES (:sid, :stid, :status, :method, NOW())
                ON DUPLICATE KEY UPDATE status = :status_up, check_method = :method_up, checked_at = NOW()";
        
        $data['status_up'] = $data['status'];
        $data['method_up'] = $data['method'];
        
        return $this->db->execute($sql, $data);
    }
    
    /**
     * ค้นหารอบการเช็คชื่อตามเงื่อนไข
     */
    public function findSession(int $classroomId, string $date, int $period): ?array
    {
        $sql = "SELECT * FROM attendance_sessions 
                WHERE classroom_id = :cid AND session_date = :sdate AND period_number = :period";
        return $this->db->queryOne($sql, ['cid' => $classroomId, 'sdate' => $date, 'period' => $period]);
    }

    /**
     * อัปเดตสถานะรอบการเช็คชื่อ
     */
    public function updateSessionStatus(int $sessionId, string $status): bool
    {
        $sql = "UPDATE attendance_sessions SET status = :status WHERE id = :id";
        return $this->db->execute($sql, ['status' => $status, 'id' => $sessionId]);
    }

    /**
     * ดึงรอบการเช็คชื่อที่ยังเปิดอยู่
     */
    public function getActiveSession(int $classroomId): ?array
    {
        $sql = "SELECT * FROM attendance_sessions WHERE classroom_id = :cid AND status = 'active' LIMIT 1";
        return $this->db->queryOne($sql, ['cid' => $classroomId]);
    }

    /**
     * ดึงสถิติการเข้าเรียนของนักเรียนรายคน
     */
    /**
     * ดึงสถิติการเข้าเรียนของนักเรียนรายคน
     */
    public function getStudentStats(int $classroomId, int $studentId): array
    {
        // ดึงจำนวนคาบทั้งหมดจากห้องเรียน
        $sqlClass = "SELECT total_periods FROM classrooms WHERE id = :id";
        $classroom = $this->db->queryOne($sqlClass, ['id' => $classroomId]);
        $totalPeriods = (int) ($classroom['total_periods'] ?? 0);

        // ถ้าไม่ได้ระบุจำนวนคาบไว้ ให้ใช้จำนวนรอบที่เช็คจริงแทน
        if ($totalPeriods <= 0) {
            $sqlSessions = "SELECT COUNT(*) as total FROM attendance_sessions WHERE classroom_id = :cid";
            $totalPeriods = (int) ($this->db->queryOne($sqlSessions, ['cid' => $classroomId])['total'] ?? 0);
        }

        if ($totalPeriods === 0) {
            return ['present' => 0, 'total' => 0, 'percent' => 0];
        }

        $sqlPresent = "SELECT COUNT(*) as present_count 
                       FROM {$this->table} 
                       WHERE student_id = :sid 
                       AND session_id IN (SELECT id FROM attendance_sessions WHERE classroom_id = :cid) 
                       AND (status = 'present' OR status = 'late')";
        
        $presentCount = (int) ($this->db->queryOne($sqlPresent, ['sid' => $studentId, 'cid' => $classroomId])['present_count'] ?? 0);

        return [
            'present' => $presentCount,
            'total' => $totalPeriods,
            'percent' => round(($presentCount / $totalPeriods) * 100, 1)
        ];
    }

    /**
     * ดึงสถิติการเข้าเรียนของนักเรียนทุกคนในห้อง
     */
    public function getAttendanceStats(int $classroomId): array
    {
        // ดึงจำนวนคาบทั้งหมดจากห้องเรียน
        $sqlClass = "SELECT total_periods FROM classrooms WHERE id = :id";
        $classroom = $this->db->queryOne($sqlClass, ['id' => $classroomId]);
        $totalPeriods = (int) ($classroom['total_periods'] ?? 0);

        // ถ้าไม่ได้ระบุจำนวนคาบไว้ ให้ใช้จำนวนรอบที่เช็คจริงแทน
        if ($totalPeriods <= 0) {
            $sqlSessions = "SELECT COUNT(*) as total FROM attendance_sessions WHERE classroom_id = :cid";
            $totalPeriods = (int) ($this->db->queryOne($sqlSessions, ['cid' => $classroomId])['total'] ?? 0);
        }

        if ($totalPeriods === 0) return [];

        // 2. นับจำนวนวันที่มาเรียน (present) ของแต่ละคน
        $sqlPresent = "SELECT student_id, COUNT(*) as present_count 
                       FROM {$this->table} 
                       WHERE session_id IN (SELECT id FROM attendance_sessions WHERE classroom_id = :cid) 
                       AND (status = 'present' OR status = 'late') 
                       GROUP BY student_id";
        $results = $this->db->query($sqlPresent, ['cid' => $classroomId]);

        $stats = [];
        foreach ($results as $row) {
            $stats[$row['student_id']] = [
                'present' => (int) $row['present_count'],
                'total' => $totalPeriods,
                'percent' => round(($row['present_count'] / $totalPeriods) * 100, 1)
            ];
        }

        return $stats;
    }
}
