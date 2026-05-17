<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Classroom;
use App\Models\Student;

class StudentCardController extends Controller
{
    protected Classroom $classroomModel;
    protected \App\Models\SchoolSetting $settingsModel;

    public function __construct()
    {
        $this->classroomModel = new Classroom();
        $this->settingsModel = new \App\Models\SchoolSetting();
    }

    /**
     * หน้าแรกของเมนูบัตรนักเรียน - เลือกชั้นเรียน
     */
    public function index()
    {
        $adminId = \Core\Session::get('user_id');
        $classrooms = $this->classroomModel->getByAdmin($adminId);
        $settings = $this->settingsModel->get();

        $this->view('admin.cards.index', [
            'title' => 'ระบบสร้างบัตรนักเรียน',
            'classrooms' => $classrooms,
            'settings' => $settings
        ]);
    }

    /**
     * หน้าตั้งค่าข้อมูลโรงเรียนสำหรับบัตร
     */
    public function settings()
    {
        $settings = $this->settingsModel->get();
        $this->view('admin.cards.settings', [
            'title' => 'ตั้งค่าข้อมูลบนบัตร',
            'settings' => $settings
        ]);
    }

    /**
     * บันทึกการตั้งค่า
     */
    public function updateSettings()
    {
        $data = [
            'school_name' => $_POST['school_name'],
            'school_name_en' => $_POST['school_name_en'],
            'academic_year' => $_POST['academic_year']
        ];

        // Handle Logo Upload
        if (isset($_FILES['school_logo']) && $_FILES['school_logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT . '/public/uploads/logos/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $filename = 'logo_' . time() . '.' . pathinfo($_FILES['school_logo']['name'], PATHINFO_EXTENSION);
            move_uploaded_file($_FILES['school_logo']['tmp_name'], $uploadDir . $filename);
            $data['school_logo'] = '/CM_System/public/uploads/logos/' . $filename;
        }

        $this->settingsModel->updateSettings($data);
        $this->redirect('/admin/cards/settings');
    }

    /**
     * เลือกนักเรียนในชั้นเรียน
     */
    public function select($id)
    {
        $classroom = $this->classroomModel->find($id);
        if (!$classroom) die("ไม่พบชั้นเรียน");

        $db = new \Core\Database();
        $students = $db->query("
            SELECT s.* 
            FROM students s
            JOIN classroom_students cs ON s.id = cs.student_id
            WHERE cs.classroom_id = :cid
            ORDER BY s.student_number ASC
        ", ['cid' => $id]);

        $this->view('admin.cards.select', [
            'title' => 'เลือกนักเรียน: ' . $classroom['subject_name'],
            'classroom' => $classroom,
            'students' => $students
        ]);
    }

    /**
     * เจนบัตรนักเรียน (Print View)
     */
    public function generate()
    {
        $studentIds = $_POST['student_ids'] ?? [];
        if (empty($studentIds)) {
            return $this->redirect('/admin/cards');
        }

        $idsString = implode(',', array_map('intval', $studentIds));
        $db = new \Core\Database();
        
        $students = $db->query("
            SELECT s.*, c.subject_name, c.room_code
            FROM students s
            JOIN classroom_students cs ON s.id = cs.student_id
            JOIN classrooms c ON cs.classroom_id = c.id
            WHERE s.id IN ($idsString)
            GROUP BY s.id
            ORDER BY s.student_number ASC
        ");

        $settings = $this->settingsModel->get();

        $this->view('admin.cards.print', [
            'title' => 'Student_Cards_' . date('Ymd'),
            'students' => $students,
            'settings' => $settings
        ]);
    }

    /**
     * AJAX Upload Student Photo
     */
    public function uploadPhoto()
    {
        header('Content-Type: application/json');
        
        $studentId = $_POST['student_id'] ?? null;
        if (!$studentId || !isset($_FILES['photo'])) {
            echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit;
        }

        try {
            if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception("Upload error: " . $_FILES['photo']['error']);
            }

            $uploadDir = ROOT . '/public/uploads/students/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            $filename = 'student_' . $studentId . '_' . time() . '.' . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $filePath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $filePath)) {
                $db = new \Core\Database();
                $photoUrl = '/CM_System/public/uploads/students/' . $filename;
                
                $db->execute("UPDATE students SET avatar = :avatar WHERE id = :id", [
                    'avatar' => $photoUrl,
                    'id' => $studentId
                ]);
                
                echo json_encode(['status' => 'success', 'url' => $photoUrl]);
            } else {
                throw new \Exception("ไม่สามารถบันทึกไฟล์ได้");
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}
