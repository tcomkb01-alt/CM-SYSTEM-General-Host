<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Classroom;

class AssignmentController extends Controller
{
    protected Assignment $assignmentModel;
    protected AssignmentSubmission $submissionModel;
    protected Classroom $classroomModel;

    public function __construct()
    {
        $this->assignmentModel = new Assignment();
        $this->submissionModel = new AssignmentSubmission();
        $this->classroomModel = new Classroom();
    }

    /**
     * หน้าแรกศูนย์รวมงานมอบหมาย
     */
    public function index()
    {
        $adminId = \Core\Session::get('user_id');
        $classrooms = $this->classroomModel->getByAdmin($adminId);

        // ดึงจำนวนงานในแต่ละห้อง
        $db = new \Core\Database();
        foreach ($classrooms as &$c) {
            $c['assignment_count'] = $db->queryOne("SELECT COUNT(*) as total FROM assignments WHERE classroom_id = :cid", ['cid' => $c['id']])['total'] ?? 0;
        }

        $this->view('admin.assignments.index', [
            'title' => 'จัดการงานมอบหมาย',
            'classrooms' => $classrooms
        ]);
    }

    /**
     * รายการงานในห้องเรียน
     */
    public function classroom($id)
    {
        $classroom = $this->classroomModel->find($id);
        if (!$classroom) return $this->redirect('/admin/assignments');

        $assignments = $this->assignmentModel->getWithStats($id);

        $this->view('admin.assignments.classroom', [
            'title' => 'งานมอบหมาย: ' . $classroom['subject_name'],
            'classroom' => $classroom,
            'assignments' => $assignments
        ]);
    }

    /**
     * บันทึกงานใหม่ (AJAX)
     */
    public function store()
    {
        $request = new Request();
        $data = [
            'classroom_id' => $request->input('classroom_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'max_score' => $request->input('max_score') ?? 10,
            'due_date' => $request->input('due_date')
        ];

        if (empty($data['title'])) {
            return $this->json(['success' => false, 'message' => 'กรุณาระบุหัวข้อคำสั่งงาน']);
        }

        $id = $this->assignmentModel->create($data);
        if ($id) {
            return $this->json(['success' => true, 'message' => 'สร้างงานมอบหมายเรียบร้อย']);
        }

        return $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึก']);
    }

    /**
     * ลบงานมอบหมาย (AJAX)
     */
    public function delete($id)
    {
        if ($this->assignmentModel->delete($id)) {
            return $this->json(['success' => true]);
        }
        return $this->json(['success' => false]);
    }

    /**
     * หน้าจอให้คะแนน
     */
    public function grading($id)
    {
        $assignment = $this->assignmentModel->find($id);
        if (!$assignment) return $this->redirect('/admin/assignments');

        $classroom = $this->classroomModel->find($assignment['classroom_id']);
        $submissions = $this->submissionModel->getSubmissions($id);

        $this->view('admin.assignments.grading', [
            'title' => 'ให้คะแนน: ' . $assignment['title'],
            'assignment' => $assignment,
            'classroom' => $classroom,
            'submissions' => $submissions
        ]);
    }

    /**
     * บันทึกคะแนน (AJAX)
     */
    public function saveGrade()
    {
        $request = new Request();
        $assignmentId = (int)$request->input('assignment_id');
        $studentId = (int)$request->input('student_id');
        $score = $request->input('score');
        $status = $request->input('status') ?: 'graded';

        if ($this->submissionModel->updateScore($assignmentId, $studentId, $score, $status)) {
            return $this->json(['success' => true]);
        }
        return $this->json(['success' => false]);
    }
}
