<?php

namespace App\Controllers;

use Core\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $db = new \Core\Database();
        $adminId = \Core\Session::get('user_id');

        // Fetch real stats
        $totalStudents = $db->queryOne("SELECT COUNT(*) as total FROM students")['total'] ?? 0;
        $totalClasses = $db->queryOne("SELECT COUNT(*) as total FROM classrooms WHERE admin_id = :id", [':id' => $adminId])['total'] ?? 0;
        
        // สำหรับ Phase ถัดไป (Attendance & Assignments)
        $attendanceStats = $db->queryOne("
            SELECT 
                COUNT(*) as total_records,
                COUNT(CASE WHEN ar.status='present' THEN 1 END) as present_count
            FROM attendance_records ar
            JOIN attendance_sessions asess ON ar.session_id = asess.id
            WHERE asess.admin_id = :id
        ", [':id' => $adminId]);

        $attendancePercent = 0;
        if ($attendanceStats && $attendanceStats['total_records'] > 0) {
            $attendancePercent = round(($attendanceStats['present_count'] / $attendanceStats['total_records']) * 100);
        }

        $pendingAssignments = $db->queryOne("
            SELECT COUNT(*) as total 
            FROM assignments a
            JOIN classrooms c ON a.classroom_id = c.id
            WHERE c.admin_id = :id AND a.due_date >= CURDATE()
        ", [':id' => $adminId])['total'] ?? 0;

        $data = [
            'title' => 'แผงควบคุม (Dashboard)',
            'stats' => [
                'total_students' => $totalStudents,
                'total_classes' => $totalClasses,
                'attendance_today' => $attendancePercent . '%',
                'pending_assignments' => $pendingAssignments
            ]
        ];
        
        $this->view('admin.dashboard', $data);
    }

    public function appCenter()
    {
        $this->view('admin.app_center', ['title' => 'ศูนย์รวมแอปพลิเคชัน']);
    }
}
