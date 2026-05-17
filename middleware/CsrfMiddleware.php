<?php

namespace Middleware;

use Core\Request;
use Core\Response;
use Core\Session;

class CsrfMiddleware
{
    public function handle(Request $request): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        // Only check CSRF for POST, PUT, DELETE requests
        if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
            $token = $request->input('csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

            if (!$token || !hash_equals(Session::get('csrf_token', ''), $token)) {
                http_response_code(403);
                die('CSRF token mismatch.');
            }
        }
    }
}
