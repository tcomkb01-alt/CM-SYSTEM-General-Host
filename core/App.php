<?php

namespace Core;

use Core\Session;

class App
{
    public function run(): void
    {
        // Set Timezone
        date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Bangkok');

        // Initialize Session
        Session::start();

        // 🛡️ Global License Check
        $license = new \Middleware\LicenseMiddleware();
        $license->handle();

        // Load Routes
        $router = new Router();
        require dirname(__DIR__) . '/routes/web.php';
        require dirname(__DIR__) . '/routes/api.php';

        // Dispatch
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // ตัด base path ออกจาก URI
        // เช่น /CM_System/login → /login
        $scriptName = $_SERVER['SCRIPT_NAME']; // e.g. /CM_System/public/index.php
        $scriptDir = dirname(dirname($scriptName)); // e.g. /CM_System
        
        // ตัดเฉพาะส่วน base directory ออก
        if ($scriptDir !== '/' && $scriptDir !== '\\' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        if (empty($uri) || $uri === false) {
            $uri = '/';
        }

        $router->dispatch($method, $uri);
    }
}
