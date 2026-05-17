<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\ClassroomStudent;
use Core\Request;
use Core\Session;

class AttendanceController extends Controller
{
    protected Attendance $attendanceModel;

    public function __construct()
    {
        $this->attendanceModel = new Attendance();
    }

    /**
     * เริ่มรอบการเช็คชื่อใหม่ (POST)
     */
    public function start($classroomId)
    {
        $request = new Request();
        $adminId = Session::get('user_id');
        $period = $request->input('period_number') ?? 1;
        $today = date('Y-m-d');

        try {
            // 1. ตรวจสอบว่ามีรอบที่เปิดอยู่แล้วหรือไม่
            $active = $this->attendanceModel->getActiveSession($classroomId);
            if ($active) {
                $this->json(['success' => true, 'message' => 'รอบเช็คชื่อเปิดอยู่แล้ว', 'session_id' => $active['id']]);
                return;
            }

            // 2. ตรวจสอบว่ามีรอบในวันและคาบนี้อยู่แล้วหรือไม่ (เพื่อ Re-use แทนการสร้างใหม่)
            $existing = $this->attendanceModel->findSession($classroomId, $today, $period);
            
            if ($existing) {
                $sessionId = $existing['id'];
                $this->attendanceModel->updateSessionStatus($sessionId, 'active');
            } else {
                // สร้างใหม่ถ้ายังไม่มี
                $sessionId = $this->attendanceModel->createSession([
                    'cid' => $classroomId,
                    'aid' => $adminId,
                    'sdate' => $today,
                    'period' => $period
                ]);

                // เตรียมบันทึก absent ให้ทุกคนในห้องก่อน (Default)
                $csModel = new ClassroomStudent();
                $students = $csModel->getStudentsInClassroom($classroomId);
                
                foreach ($students as $s) {
                    $this->attendanceModel->upsertRecord([
                        'sid' => $sessionId,
                        'stid' => $s['id'],
                        'status' => 'absent',
                        'method' => 'manual'
                    ]);
                }
            }

            $this->json(['success' => true, 'message' => 'เปิดรอบเช็คชื่อสำเร็จ!', 'session_id' => $sessionId]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ปิดรอบการเช็คชื่อ (POST)
     */
    public function stop($sessionId)
    {
        try {
            $this->attendanceModel->updateSessionStatus($sessionId, 'closed');
            $this->json(['success' => true, 'message' => 'ปิดรอบเช็คชื่อเรียบร้อยแล้ว']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * บันทึกการเข้าเรียน (POST)
     */
    public function check($sessionId)
    {
        $request = new Request();
        $studentId = $request->input('student_id');
        $status = $request->input('status') ?? 'present';
        $method = $request->input('method') ?? 'manual';

        try {
            $this->attendanceModel->upsertRecord([
                'sid' => $sessionId,
                'stid' => $studentId,
                'status' => $status,
                'method' => $method
            ]);
            $this->json(['success' => true, 'message' => 'บันทึกสำเร็จ']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * เช็คชื่อด้วยการสแกน Barcode/QR Code (POST)
     */
    public function scan($sessionId)
    {
        $request = new Request();
        $studentCode = $request->input('student_code');

        if (!$studentCode) {
            $this->json(['success' => false, 'message' => 'กรุณาระบุรหัสนักเรียน'], 400);
            return;
        }

        try {
            // ค้นหานักเรียนจากรหัส
            $db = new \Core\Database();
            $student = $db->queryOne("SELECT id, first_name, last_name FROM students WHERE student_code = :code", ['code' => $studentCode]);

            if (!$student) {
                $this->json(['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียนรหัส: ' . $studentCode], 404);
                return;
            }

            // ตรวจสอบว่านักเรียนคนนี้อยู่ในห้องเรียนนี้หรือไม่
            // ดึง cid จาก session
            $session = $db->queryOne("SELECT classroom_id FROM attendance_sessions WHERE id = :sid", ['sid' => $sessionId]);
            $csModel = new ClassroomStudent();
            if (!$csModel->isStudentInClassroom($session['classroom_id'], $student['id'])) {
                $this->json(['success' => false, 'message' => "นักเรียน {$student['first_name']} ไม่ได้อยู่ในห้องเรียนนี้"], 400);
                return;
            }

            // บันทึกเข้าเรียน
            $this->attendanceModel->upsertRecord([
                'sid' => $sessionId,
                'stid' => $student['id'],
                'status' => 'present',
                'method' => 'scan'
            ]);

            $this->json([
                'success' => true, 
                'message' => 'เช็คชื่อสำเร็จ: ' . $student['first_name'] . ' ' . $student['last_name'],
                'student_id' => $student['id']
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * แสดงหน้ารอบการเช็คชื่อ
     */
    public function session($id)
    {
        $records = $this->attendanceModel->getRecords($id);
        
        // ดึงข้อมูลห้องเรียนผ่าน session
        $db = new \Core\Database();
        $sessionInfo = $db->queryOne("SELECT asess.*, c.subject_name FROM attendance_sessions asess JOIN classrooms c ON asess.classroom_id = c.id WHERE asess.id = :id", ['id' => $id]);

        $this->view('admin.attendance.session', [
            'title' => 'บันทึกการเข้าเรียน',
            'session' => $sessionInfo,
            'records' => $records
        ]);
    }

    /**
     * หน้าเช็คชื่อสำหรับนักเรียน (Public - ไม่ต้อง Login)
     */
    public function checkinPage($sessionId)
    {
        $db = new \Core\Database();
        $session = $db->queryOne(
            "SELECT asess.*, c.subject_name, c.room_code 
             FROM attendance_sessions asess 
             JOIN classrooms c ON asess.classroom_id = c.id 
             WHERE asess.id = :id AND asess.status = 'active'",
            ['id' => $sessionId]
        );

        if (!$session) {
            die('<div style="text-align:center;padding:50px;font-family:sans-serif"><h2>ไม่พบรอบการเช็คชื่อ หรือรอบนี้ปิดไปแล้ว</h2></div>');
        }

        $this->view('public.attendance_checkin', [
            'title' => 'เช็คชื่อเข้าเรียน',
            'session' => $session
        ]);
    }

    /**
     * รับข้อมูลเช็คชื่อจากนักเรียน (Public POST)
     */
    public function checkinSubmit($sessionId)
    {
        $request = new Request();
        $studentCode = $request->input('student_code');

        if (!$studentCode) {
            $this->json(['success' => false, 'message' => 'กรุณาระบุเลขประจำตัว'], 400);
            return;
        }

        try {
            $db = new \Core\Database();

            // ตรวจสอบ session ยังเปิดอยู่
            $session = $db->queryOne("SELECT * FROM attendance_sessions WHERE id = :id AND status = 'active'", ['id' => $sessionId]);
            if (!$session) {
                $this->json(['success' => false, 'message' => 'รอบเช็คชื่อนี้ปิดแล้ว'], 400);
                return;
            }

            // ค้นหานักเรียน
            $student = $db->queryOne("SELECT id, first_name, last_name, prefix FROM students WHERE student_code = :code", ['code' => $studentCode]);
            if (!$student) {
                $this->json(['success' => false, 'message' => 'ไม่พบเลขประจำตัว: ' . $studentCode], 404);
                return;
            }

            // ตรวจว่าอยู่ในห้องนี้
            $csModel = new ClassroomStudent();
            if (!$csModel->isStudentInClassroom($session['classroom_id'], $student['id'])) {
                $this->json(['success' => false, 'message' => 'นักเรียนไม่ได้อยู่ในห้องเรียนนี้'], 400);
                return;
            }

            // บันทึก
            $this->attendanceModel->upsertRecord([
                'sid' => $sessionId,
                'stid' => $student['id'],
                'status' => 'present',
                'method' => 'qr_scan'
            ]);

            $this->json([
                'success' => true,
                'message' => 'เช็คชื่อสำเร็จ!',
                'student_name' => $student['prefix'] . $student['first_name'] . ' ' . $student['last_name']
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * ลบรอบการเช็คชื่อ (POST)
     */
    public function deleteSession($id)
    {
        try {
            $db = new \Core\Database();
            $db->execute("DELETE FROM attendance_sessions WHERE id = :id", ['id' => $id]);
            
            $this->json(['success' => true, 'message' => 'ลบบันทึกการเข้าเรียนเรียบร้อยแล้ว']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

