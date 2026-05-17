<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Student;
use Core\Request;
use Core\Database;

class StudentController extends Controller
{
    protected Student $studentModel;

    public function __construct()
    {
        $this->studentModel = new Student();
    }

    /**
     * แสดงรายการนักเรียนทั้งหมด
     */
    public function index()
    {
        $request = new Request();
        $search = $request->input('search') ?? '';

        if (!empty($search)) {
            $students = $this->studentModel->search($search);
        } else {
            $students = $this->studentModel->all();
        }

        $this->view('admin.students.index', [
            'title' => 'จัดการข้อมูลนักเรียน',
            'students' => $students,
            'search' => $search
        ]);
    }

    /**
     * ดาวน์โหลดเทมเพลต CSV (UTF-8 BOM รองรับภาษาไทย)
     */
    public function downloadTemplate()
    {
        $filename = 'student_template.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $output = fopen('php://output', 'w');

        // เพิ่ม UTF-8 BOM เพื่อให้ Excel เปิดอ่านภาษาไทยได้ถูกต้อง
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        fputcsv($output, [
            'รหัสนักเรียน',
            'เลขบัตรประชาชน',
            'คำนำหน้า',
            'ชื่อ',
            'นามสกุล',
            'ชั้นเรียน',
            'เลขที่'
        ]);

        // ตัวอย่างข้อมูล 2 แถว
        fputcsv($output, ['65001', '1234567890123', 'นาย', 'สมชาย', 'ใจดี', 'ม.1/1', '1']);
        fputcsv($output, ['65002', '1234567890124', 'นางสาว', 'สมหญิง', 'รักเรียน', 'ม.1/1', '2']);

        fclose($output);
        exit;
    }

    /**
     * Import นักเรียนจากไฟล์ CSV
     */
    public function importCSV()
    {
        $request = new Request();

        // ตรวจสอบว่ามีไฟล์ถูกอัปโหลดหรือไม่
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'ไม่พบไฟล์ CSV หรือไฟล์มีปัญหา'], 400);
            return;
        }

        $file = $_FILES['csv_file'];

        // ตรวจสอบ extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            $this->json(['success' => false, 'message' => 'รองรับเฉพาะไฟล์ .csv เท่านั้น'], 400);
            return;
        }

        // ตรวจสอบขนาดไฟล์ (สูงสุด 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->json(['success' => false, 'message' => 'ไฟล์มีขนาดเกิน 5MB'], 400);
            return;
        }

        $handle = fopen($file['tmp_name'], 'r');
        if ($handle === false) {
            $this->json(['success' => false, 'message' => 'ไม่สามารถอ่านไฟล์ได้'], 500);
            return;
        }

        // อ่านเนื้อหาทั้งหมดแล้วลบ BOM ถ้ามี
        $content = file_get_contents($file['tmp_name']);
        $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
        if (strpos($content, $bom) === 0) {
            $content = substr($content, 3);
        }

        // แปลง encoding ถ้าจำเป็น (TIS-620 → UTF-8)
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'TIS-620,Windows-874');
        }

        // เขียนกลับไปไฟล์ temp
        $tempFile = tempnam(sys_get_temp_dir(), 'csv_');
        file_put_contents($tempFile, $content);

        $handle = fopen($tempFile, 'r');

        // ข้ามแถวแรก (Header)
        $header = fgetcsv($handle);

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $lineNumber = 1;

        $db = Database::getInstance();

        while (($row = fgetcsv($handle)) !== false) {
            $lineNumber++;

            // ข้ามแถวว่าง
            if (empty(array_filter($row))) continue;

            // ตรวจสอบจำนวนคอลัมน์ (ต้องมี 7 คอลัมน์)
            if (count($row) < 7) {
                $errors[] = "แถวที่ {$lineNumber}: ข้อมูลไม่ครบ (ต้องมี 7 คอลัมน์)";
                $skipped++;
                continue;
            }

            list($studentCode, $nationalId, $prefix, $firstName, $lastName, $classLevel, $studentNumber) = $row;

            // Trim ข้อมูล
            $studentCode = trim($studentCode);
            $nationalId = trim($nationalId);
            $prefix = trim($prefix);
            $firstName = trim($firstName);
            $lastName = trim($lastName);
            $classLevel = trim($classLevel);
            $studentNumber = intval(trim($studentNumber));

            // Validate ข้อมูลจำเป็น
            if (empty($studentCode) || empty($firstName) || empty($lastName) || empty($classLevel)) {
                $errors[] = "แถวที่ {$lineNumber}: ข้อมูลไม่ครบ (รหัส, ชื่อ, นามสกุล, ชั้นเรียน จำเป็นต้องกรอก)";
                $skipped++;
                continue;
            }

            try {
                // ตรวจสอบว่ารหัสนักเรียนซ้ำหรือไม่
                $stmt = $db->prepare("SELECT id FROM students WHERE student_code = :code");
                $stmt->execute(['code' => $studentCode]);

                if ($stmt->fetch()) {
                    // อัปเดตข้อมูลถ้ามีอยู่แล้ว
                    $stmt = $db->prepare("UPDATE students SET national_id = :nid, prefix = :prefix, first_name = :fname, last_name = :lname, class_level = :class, student_number = :num, updated_at = NOW() WHERE student_code = :code");
                    $stmt->execute([
                        'nid' => $nationalId ?: null,
                        'prefix' => $prefix,
                        'fname' => $firstName,
                        'lname' => $lastName,
                        'class' => $classLevel,
                        'num' => $studentNumber,
                        'code' => $studentCode
                    ]);
                    $imported++;
                } else {
                    // เพิ่มข้อมูลใหม่
                    $stmt = $db->prepare("INSERT INTO students (student_code, national_id, prefix, first_name, last_name, class_level, student_number, created_at) VALUES (:code, :nid, :prefix, :fname, :lname, :class, :num, NOW())");
                    $stmt->execute([
                        'code' => $studentCode,
                        'nid' => $nationalId ?: null,
                        'prefix' => $prefix,
                        'fname' => $firstName,
                        'lname' => $lastName,
                        'class' => $classLevel,
                        'num' => $studentNumber
                    ]);
                    $imported++;
                }
            } catch (\PDOException $e) {
                $errors[] = "แถวที่ {$lineNumber}: " . $e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);
        unlink($tempFile);

        $this->json([
            'success' => true,
            'message' => "นำเข้าสำเร็จ {$imported} รายการ" . ($skipped > 0 ? ", ข้าม {$skipped} รายการ" : ''),
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors
        ]);
    }
    /**
     * เพิ่มนักเรียนใหม่ (Single)
     */
    public function store()
    {
        $request = new Request();
        $data = [
            'student_code' => $request->input('student_code'),
            'national_id' => $request->input('national_id'),
            'prefix' => $request->input('prefix'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'class_level' => $request->input('class_level'),
            'student_number' => $request->input('student_number'),
            'is_active' => 1
        ];

        if (empty($data['student_code']) || empty($data['first_name'])) {
            $this->json(['success' => false, 'message' => 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน'], 400);
            return;
        }

        try {
            $this->studentModel->create($data);
            $this->json(['success' => true, 'message' => 'เพิ่มข้อมูลนักเรียนสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ดึงข้อมูลนักเรียนสำหรับแก้ไข
     */
    public function edit($id)
    {
        $student = $this->studentModel->find($id);
        if (!$student) {
            $this->json(['success' => false, 'message' => 'ไม่พบข้อมูลนักเรียน'], 404);
            return;
        }
        $this->json(['success' => true, 'data' => $student]);
    }

    /**
     * อัปเดตข้อมูลนักเรียน
     */
    public function update($id)
    {
        $request = new Request();
        $data = [
            'student_code' => $request->input('student_code'),
            'national_id' => $request->input('national_id'),
            'prefix' => $request->input('prefix'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'class_level' => $request->input('class_level'),
            'student_number' => $request->input('student_number')
        ];

        try {
            $this->studentModel->update($id, $data);
            $this->json(['success' => true, 'message' => 'อัปเดตข้อมูลนักเรียนสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ลบข้อมูลนักเรียน
     */
    public function delete($id)
    {
        try {
            $this->studentModel->delete($id);
            $this->json(['success' => true, 'message' => 'ลบข้อมูลนักเรียนสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * ส่งออกข้อมูลนักเรียนทั้งหมดเป็น CSV
     */
    public function export()
    {
        $students = $this->studentModel->all();
        $filename = 'students_export_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM

        fputcsv($output, ['ID', 'รหัสนักเรียน', 'เลขบัตรประชาชน', 'คำนำหน้า', 'ชื่อ', 'นามสกุล', 'ชั้นเรียน', 'เลขที่']);

        foreach ($students as $s) {
            fputcsv($output, [
                $s['id'],
                $s['student_code'],
                $s['national_id'],
                $s['prefix'],
                $s['first_name'],
                $s['last_name'],
                $s['class_level'],
                $s['student_number']
            ]);
        }

        fclose($output);
        exit;
    }
}
