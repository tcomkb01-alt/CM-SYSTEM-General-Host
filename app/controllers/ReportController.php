<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Classroom;
use App\Models\Attendance;
use App\Models\AssignmentSubmission;

class ReportController extends Controller
{
    protected Classroom $classroomModel;

    public function __construct()
    {
        $this->classroomModel = new Classroom();
    }

    /**
     * หน้าแรกของเมนูรายงาน - เลือกชั้นเรียน
     */
    public function index()
    {
        $adminId = \Core\Session::get('user_id');
        $classrooms = $this->classroomModel->getByAdmin($adminId);

        $this->view('admin.reports.index', [
            'title' => 'รายงานสรุปผล',
            'classrooms' => $classrooms
        ]);
    }

    /**
     * แสดงรายงานของแต่ละชั้นเรียน
     */
    public function classroom($id)
    {
        $classroom = $this->classroomModel->find($id);
        if (!$classroom) {
            die("ไม่พบข้อมูลชั้นเรียน");
        }

        $db = new \Core\Database();
        
        // ดึงรายชื่อนักเรียนในห้อง
        $students = $db->query("
            SELECT s.* 
            FROM students s
            JOIN classroom_students cs ON s.id = cs.student_id
            WHERE cs.classroom_id = :cid
            ORDER BY s.student_number ASC
        ", ['cid' => $id]);

        // ดึงสถิติเข้าเรียนรายบุคคล
        $attendanceModel = new Attendance();
        foreach ($students as &$student) {
            $stats = $attendanceModel->getStudentStats($id, $student['id']);
            $student['attendance_present'] = $stats['present'] ?? 0;
            $student['attendance_total'] = $stats['total'] ?? 0;
            $student['attendance_percent'] = $stats['percent'] ?? 0;

            // ดึงสรุปงาน (ส่งแล้ว/ทั้งหมด)
            $taskStats = $db->queryOne("
                SELECT 
                    COUNT(a.id) as total_tasks,
                    COUNT(sub.id) as submitted_tasks,
                    SUM(sub.score) as total_score
                FROM assignments a
                LEFT JOIN assignment_submissions sub ON (sub.assignment_id = a.id AND sub.student_id = :sid)
                WHERE a.classroom_id = :cid
            ", ['sid' => $student['id'], 'cid' => $id]);

            $student['tasks_total'] = $taskStats['total_tasks'] ?? 0;
            $student['tasks_submitted'] = $taskStats['submitted_tasks'] ?? 0;
            $student['tasks_score'] = $taskStats['total_score'] ?? 0;
        }

        $this->view('admin.reports.classroom', [
            'title' => 'รายงาน: ' . $classroom['subject_name'],
            'classroom' => $classroom,
            'students' => $students
        ]);
    }
}
