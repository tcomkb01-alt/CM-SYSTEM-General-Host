<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Classroom;
use App\Models\ClassroomStudent;
use App\Models\Student;
use App\Models\Attendance;
use Core\Request;
use Core\Database;

class ClassroomController extends Controller
{
    protected Classroom $classroomModel;

    public function __construct()
    {
        $this->classroomModel = new Classroom();
    }

    /**
     * หน้า Public Portal สำหรับนักเรียน
     */
    public function portal($code)
    {
        $db = new \Core\Database();
        $classroom = $db->queryOne("SELECT * FROM classrooms WHERE room_code = :code", ['code' => $code]);
        
        if (!$classroom) {
            die("ไม่พบรหัสห้องเรียนนี้");
        }

        $studentId = \Core\Session::get("student_auth_{$classroom['id']}");
        $student = null;

        if ($studentId) {
            $student = $db->queryOne("SELECT * FROM students WHERE id = :id", ['id' => $studentId]);
            if ($student) {
                // Get Attendance Stats
                $attendanceModel = new \App\Models\Attendance();
                $stats = $attendanceModel->getStudentStats($classroom['id'], $student['id']);
                $student['attendance_percent'] = $stats['percent'] ?? 0;
                $student['attendance_present'] = $stats['present'] ?? 0;
                $student['attendance_total'] = $stats['total'] ?? 0;

                // Get Assignments (Phase 6)
                $submissionModel = new \App\Models\AssignmentSubmission();
                $student['assignments'] = $submissionModel->getStudentAssignments($classroom['id'], $student['id']);
            } else {
                \Core\Session::remove("student_auth_{$classroom['id']}");
            }
        }

        $this->view('public.classroom_portal', [
            'title' => 'Student Portal: ' . $classroom['subject_name'],
            'classroom' => $classroom,
            'student' => $student
        ]);
    }

    /**
     * เข้าสู่ระบบนักเรียน (AJAX)
     */
    public function loginStudent($code)
    {
        $request = new \Core\Request();
        $studentCode = $request->input('student_code');
        
        $db = new \Core\Database();
        $classroom = $db->queryOne("SELECT * FROM classrooms WHERE room_code = :code", ['code' => $code]);
        
        if (!$classroom) {
            $this->json(['success' => false, 'message' => 'ไม่พบห้องเรียน'], 404);
            return;
        }

        $sql = "SELECT s.* FROM students s
                JOIN classroom_students cs ON s.id = cs.student_id
                WHERE cs.classroom_id = :cid AND s.student_code = :scode";
        $student = $db->queryOne($sql, ['cid' => $classroom['id'], 'scode' => $studentCode]);

        if ($student) {
            \Core\Session::set("student_auth_{$classroom['id']}", $student['id']);
            $this->json(['success' => true]);
        } else {
            $this->json(['success' => false, 'message' => 'ไม่พบรหัสนักเรียนในห้องนี้']);
        }
    }

    /**
     * ออกจากระบบนักเรียน
     */
    public function logoutStudent($code)
    {
        $db = new \Core\Database();
        $classroom = $db->queryOne("SELECT id FROM classrooms WHERE room_code = :code", ['code' => $code]);
        if($classroom) {
            \Core\Session::remove("student_auth_{$classroom['id']}");
        }
        header("Location: " . ($_ENV['APP_URL'] ?? '') . "/portal/" . $code);
    }

    /**
     * แสดงรายการชั้นเรียนทั้งหมด
     */
    public function index()
    {
        $request = new Request();
        $search = $request->input('search') ?? '';
        
        if (!empty($search)) {
            $classrooms = $this->classroomModel->search($search);
        } else {
            $classrooms = $this->classroomModel->getAllWithAdmin();
        }

        $this->view('admin.classrooms.index', [
            'title' => 'จัดการชั้นเรียน',
            'classrooms' => $classrooms,
            'search' => $search
        ]);
    }

    /**
     * สร้างห้องเรียนใหม่ (POST)
     */
    public function store()
    {
        $request = new Request();
        $subjectName = $request->input('subject_name');
        $subjectCode = $request->input('subject_code');
        $totalPeriods = $request->input('total_periods') ?? 0;

        if (empty($subjectName)) {
            $this->json(['success' => false, 'message' => 'กรุณากรอกชื่อวิชา'], 400);
            return;
        }

        try {
            $adminId = \Core\Session::get('user_id');
            $roomCode = $this->generateRoomCode();

            $data = [
                'admin_id' => $adminId,
                'room_code' => $roomCode,
                'subject_name' => $subjectName,
                'subject_code' => $subjectCode,
                'total_periods' => $totalPeriods,
                'is_active' => 1
            ];

            $this->classroomModel->create($data);

            $this->json([
                'success' => true, 
                'message' => 'สร้างห้องเรียนสำเร็จ!',
                'room_code' => $roomCode
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ดึงข้อมูลชั้นเรียนสำหรับแก้ไข
     */
    public function edit($id)
    {
        $classroom = $this->classroomModel->find($id);
        if (!$classroom) {
            $this->json(['success' => false, 'message' => 'ไม่พบข้อมูลชั้นเรียน'], 404);
            return;
        }
        $this->json(['success' => true, 'data' => $classroom]);
    }

    /**
     * อัปเดตข้อมูลชั้นเรียน
     */
    public function update($id)
    {
        $request = new Request();
        $data = [
            'subject_name' => $request->input('subject_name'),
            'subject_code' => $request->input('subject_code'),
            'total_periods' => $request->input('total_periods') ?? 0
        ];

        if (empty($data['subject_name'])) {
            $this->json(['success' => false, 'message' => 'กรุณากรอกชื่อวิชา'], 400);
            return;
        }

        try {
            $this->classroomModel->update($id, $data);
            $this->json(['success' => true, 'message' => 'อัปเดตข้อมูลชั้นเรียนสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * อัปเดตการตั้งค่าชั้นเรียน (จำนวนคาบ, เกณฑ์ผ่าน)
     */
    public function updateSettings($id)
    {
        $request = new Request();
        $data = [
            'total_periods' => (int)($request->input('total_periods') ?? 0),
            'pass_criteria' => (float)($request->input('pass_criteria') ?? 80)
        ];

        try {
            $this->classroomModel->update($id, $data);
            $this->json(['success' => true, 'message' => 'อัปเดตการตั้งค่าสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ลบชั้นเรียน
     */
    public function delete($id)
    {
        try {
            $this->classroomModel->delete($id);
            $this->json(['success' => true, 'message' => 'ลบชั้นเรียนสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * แสดงรายละเอียดห้องเรียน (นักเรียน & สถิติ)
     */
    public function show($id)
    {
        $classroom = $this->classroomModel->find($id);
        if (!$classroom) {
            $this->redirect('/admin/classrooms');
            return;
        }

        $csModel = new ClassroomStudent();
        $students = $csModel->getStudentsInClassroom($id);

        $attendanceModel = new Attendance();
        $sessions = $attendanceModel->getSessions($id) ?? [];
        $attendanceStats = $attendanceModel->getAttendanceStats($id);

        $submissionModel = new \App\Models\AssignmentSubmission();
        $assignmentStats = $submissionModel->getClassroomAssignmentStats($id);

        foreach ($students as &$s) {
            // Attendance
            $s['attendance_percent'] = $attendanceStats[$s['id']]['percent'] ?? 0;
            $s['attendance_present'] = $attendanceStats[$s['id']]['present'] ?? 0;
            $s['attendance_total'] = $attendanceStats[$s['id']]['total'] ?? 0;

            // Assignments
            $s['assignment_submitted'] = $assignmentStats[$s['id']]['submitted_count'] ?? 0;
            $s['assignment_total'] = $assignmentStats[$s['id']]['total_assignments'] ?? 0;
            $s['assignment_score'] = $assignmentStats[$s['id']]['total_score'] ?? 0;
            $s['assignment_max_score'] = $assignmentStats[$s['id']]['max_possible_score'] ?? 0;
        }

        $this->view('admin.classrooms.show', [
            'title' => 'ห้องเรียน: ' . $classroom['subject_name'],
            'classroom' => $classroom,
            'sessions' => $sessions,
            'students' => $students
        ]);
    }

    /**
     * เพิ่มนักเรียนเข้าห้องเรียน (POST) - รองรับเพิ่มหลายคน
     */
    public function addStudent($id)
    {
        $request = new Request();
        $studentIds = $request->input('student_ids'); // Array of IDs
        $studentId = $request->input('student_id');   // Single ID (backward compat)

        try {
            $csModel = new ClassroomStudent();

            // กรณีเพิ่มหลายคน
            if (!empty($studentIds) && is_array($studentIds)) {
                $added = $csModel->addMultipleStudents($id, $studentIds);
                $this->json(['success' => true, 'message' => "เพิ่มนักเรียน {$added} คนเข้าห้องเรียนสำเร็จ!"]);
                return;
            }

            // กรณีเพิ่มทีละคน
            if (!$studentId) {
                $this->json(['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียน'], 400);
                return;
            }

            if ($csModel->isStudentInClassroom($id, $studentId)) {
                $this->json(['success' => false, 'message' => 'นักเรียนคนนี้อยู่ในห้องเรียนแล้ว'], 400);
                return;
            }

            $csModel->addStudent($id, $studentId);
            $this->json(['success' => true, 'message' => 'เพิ่มนักเรียนเข้าห้องเรียนสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ลบนนักเรียนออกจากห้องเรียน (POST)
     */
    public function removeStudent($id)
    {
        $request = new Request();
        $studentId = $request->input('student_id');

        try {
            $csModel = new ClassroomStudent();
            $csModel->removeStudent($id, $studentId);
            $this->json(['success' => true, 'message' => 'ลบนักเรียนออกจากห้องเรียนสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ค้นหานักเรียนเพื่อเพิ่มเข้าห้อง (AJAX)
     */
    public function searchStudents($id)
    {
        $request = new Request();
        $keyword = $request->input('query');
        $classLevel = $request->input('class_level');
        
        $studentModel = new Student();
        $students = $studentModel->filter($classLevel, $keyword);
        
        $this->json(['success' => true, 'data' => $students]);
    }

    /**
     * ดึงรายการชั้นเรียนทั้งหมด (AJAX)
     */
    public function getClassLevels()
    {
        $studentModel = new Student();
        $levels = $studentModel->getClassLevels();
        $this->json(['success' => true, 'data' => $levels]);
    }

    /**
     * ตรวจสอบข้อมูลนักเรียนเพื่อเข้าสู่ Portal (AJAX)
     */
    public function verifyStudent($code)
    {
        $request = new \Core\Request();
        $studentCode = $request->input('student_code');
        
        $db = new \Core\Database();
        $classroom = $db->queryOne("SELECT * FROM classrooms WHERE room_code = :code", ['code' => $code]);
        
        if (!$classroom) {
            $this->json(['success' => false, 'message' => 'ไม่พบห้องเรียน'], 404);
            return;
        }

        // ค้นหานักเรียนในห้องนี้
        $sql = "SELECT s.* FROM students s
                JOIN classroom_students cs ON s.id = cs.student_id
                WHERE cs.classroom_id = :cid AND s.student_code = :scode";
        $student = $db->queryOne($sql, ['cid' => $classroom['id'], 'scode' => $studentCode]);

        if (!$student) {
            $this->json(['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียนในห้องนี้'], 404);
            return;
        }

        // คำนวณสถิติ
        $attendanceModel = new \App\Models\Attendance();
        $stats = $attendanceModel->getStudentStats($classroom['id'], $student['id']);
        
        $student['attendance_percent'] = $stats['percent'] ?? 0;
        $student['attendance_present'] = $stats['present'] ?? 0;
        $student['attendance_total'] = $stats['total'] ?? 0;

        $this->json(['success' => true, 'student' => $student]);
    }

    /**
     * ดึงรายการงานและการส่งของนักเรียนรายบุคคล (AJAX)
     */
    public function getStudentAssignments($classroomId, $studentId)
    {
        try {
            $submissionModel = new \App\Models\AssignmentSubmission();
            $data = $submissionModel->getStudentAssignments((int)$classroomId, (int)$studentId);
            $this->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * สุ่มรหัสห้องเรียน 6 หลัก (0-9, A-Z)
     */
    private function generateRoomCode(): string
    {
        $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        
        // ตรวจสอบว่าซ้ำในฐานข้อมูลไหม
        $exists = $this->classroomModel->findByRoomCode($code);
        if ($exists) {
            return $this->generateRoomCode();
        }
        
        return $code;
    }
}
