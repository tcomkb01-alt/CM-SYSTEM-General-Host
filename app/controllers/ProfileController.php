<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Session;
use Core\Database;
use App\Models\User;

class ProfileController extends Controller
{
    protected User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Display profile page
     */
    public function index()
    {
        $userId = Session::get('user_id');
        $user = $this->userModel->find($userId);

        $this->view('admin.profile', [
            'title' => 'ข้อมูลส่วนตัว',
            'user' => $user
        ]);
    }

    /**
     * Update profile information (POST)
     */
    public function update()
    {
        $request = new Request();
        $userId = Session::get('user_id');

        $data = [
            'display_name' => $request->input('display_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone')
        ];

        // Validation
        if (empty($data['display_name'])) {
            $this->json(['success' => false, 'message' => 'กรุณากรอกชื่อที่ต้องการให้แสดง'], 400);
            return;
        }

        try {
            $this->userModel->update($userId, $data);
            
            // Update session data
            Session::set('user_name', $data['display_name']);

            $this->json(['success' => true, 'message' => 'อัปเดตข้อมูลส่วนตัวสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Change password (POST)
     */
    public function changePassword()
    {
        $request = new Request();
        $userId = Session::get('user_id');
        
        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');
        $confirmPassword = $request->input('confirm_password');

        if (empty($currentPassword) || empty($newPassword)) {
            $this->json(['success' => false, 'message' => 'กรุณากรอกรหัสผ่านให้ครบถ้วน'], 400);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            $this->json(['success' => false, 'message' => 'รหัสผ่านใหม่ไม่ตรงกัน'], 400);
            return;
        }

        $user = $this->userModel->find($userId);
        if (!password_verify($currentPassword, $user['password_hash'])) {
            $this->json(['success' => false, 'message' => 'รหัสผ่านปัจจุบันไม่ถูกต้อง'], 401);
            return;
        }

        try {
            $this->userModel->update($userId, [
                'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT)
            ]);

            $this->json(['success' => true, 'message' => 'เปลี่ยนรหัสผ่านสำเร็จ!']);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Upload profile avatar (POST)
     */
    public function uploadAvatar()
    {
        $userId = Session::get('user_id');
        
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'กรุณาเลือกไฟล์ภาพที่ถูกต้อง'], 400);
            return;
        }

        try {
            $uploadDir = ROOT . '/public/uploads/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $filePath = $uploadDir . $filename;
            $publicPath = '/CM_System/public/uploads/avatars/' . $filename;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filePath)) {
                // Get old avatar to delete it later if needed
                $oldUser = $this->userModel->find($userId);
                
                // Update database
                $this->userModel->update($userId, ['avatar' => $publicPath]);
                
                // Update session
                Session::set('user_avatar', $publicPath);

                // Try to delete old file if it exists
                if ($oldUser && $oldUser['avatar']) {
                    $oldFilePath = ROOT . str_replace('/CM_System/public', '/public', $oldUser['avatar']);
                    if (file_exists($oldFilePath) && strpos($oldUser['avatar'], '/uploads/avatars/') !== false) {
                        @unlink($oldFilePath);
                    }
                }

                $this->json(['success' => true, 'message' => 'อัปเดตภาพโปรไฟล์สำเร็จ!', 'avatar_url' => $publicPath]);
            } else {
                throw new \Exception("ไม่สามารถบันทึกไฟล์ได้");
            }
        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }
}

