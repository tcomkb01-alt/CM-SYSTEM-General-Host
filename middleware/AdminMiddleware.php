<?php

namespace Middleware;

use Core\Request;
use Core\Session;

class AdminMiddleware
{
    public function handle(Request $request): void
    {
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit;
        }

        if (Session::get('user_role') !== 'admin') {
            http_response_code(403);
            die('Access denied. Admin only.');
        }
    }
}
