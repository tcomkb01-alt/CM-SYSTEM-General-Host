<?php

namespace Middleware;

use Core\Request;
use Core\Response;

class AuthMiddleware
{
    public function handle(Request $request): bool
    {
        if (!\Core\Session::has('user_id')) {
            if ($request->isAjax()) {
                (new Response())->json(['error' => 'Unauthorized'], 401);
            } else {
                header("Location: " . $_ENV['APP_URL'] . "/login");
            }
            exit;
        }
        return true;
    }
}
