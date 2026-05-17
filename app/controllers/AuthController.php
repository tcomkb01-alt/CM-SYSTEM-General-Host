<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Session;
use Core\Validator;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin()
    {
        if (Session::has('user_id')) {
            $this->redirect(Session::get('user_role') === 'admin' ? '/admin/dashboard' : '/student/dashboard');
        }
        $this->view('auth.login');
    }

    public function login()
    {
        $request = new Request();
        $validator = new Validator();

        $data = $request->all();

        $rules = [
            'username' => 'required|min:3',
            'password' => 'required|min:6',
            'csrf_token' => 'required'
        ];

        if (!$validator->validate($data, $rules)) {
            return $this->json([
                'success' => false, 
                'message' => 'ข้อมูลไม่ถูกต้อง',
                'errors' => $validator->getErrors()
            ], 422);
        }

        // Validate CSRF
        if (!hash_equals(Session::get('csrf_token', ''), $data['csrf_token'] ?? '')) {
            return $this->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
        }

        $username = $data['username'];
        $password = $data['password'];

        $user = $this->userModel->findByUsername($username);

        if (!$user) {
            $this->logLogin(null, $username, 'failed');
            return $this->json(['success' => false, 'message' => 'ไม่พบผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'], 401);
        }

        // Check if account is locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            $this->logLogin($user['id'], $username, 'locked');
            return $this->json(['success' => false, 'message' => 'บัญชีถูกระงับชั่วคราวเนื่องจากเข้าระบบผิดพลาดหลายครั้ง'], 403);
        }

        if (password_verify($password, $user['password_hash'])) {
            // Success
            Session::regenerate();
            Session::set('user_id', $user['id']);
            Session::set('user_role', $user['role']);
            Session::set('user_name', $user['display_name']);
            Session::set('user_avatar', $user['avatar']);
            
            $this->userModel->resetFailedAttempts($user['id']);
            $this->userModel->updateLoginLog($user['id']);
            $this->logLogin($user['id'], $username, 'success');

            return $this->json(['success' => true, 'redirect' => $user['role'] === 'admin' ? '/admin/dashboard' : '/student/dashboard']);
        } else {
            // Failed
            $this->userModel->incrementFailedAttempts($username);
            $this->logLogin($user['id'], $username, 'failed');
            return $this->json(['success' => false, 'message' => 'รหัสผ่านไม่ถูกต้อง'], 401);
        }
    }

    public function logout()
    {
        Session::destroy();
        $this->redirect('/login');
    }

    private function logLogin(?int $userId, string $username, string $status): void
    {
        $db = new \Core\Database();
        $sql = "INSERT INTO login_logs (user_id, username, status, ip_address, user_agent) 
                VALUES (:user_id, :username, :status, :ip, :ua)";
        $db->execute($sql, [
            ':user_id' => $userId,
            ':username' => $username,
            ':status' => $status,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
