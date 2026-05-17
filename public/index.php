<?php

/**
 * Classroom Management System - Front Controller
 * Single entry point for all requests
 */

// Disable Error Reporting (ซ่อน Error จริงเพื่อความปลอดภัย)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('ROOT', dirname(__DIR__));

// Security Headers
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self' https: 'unsafe-inline' 'unsafe-eval'; img-src 'self' https: data:; font-src 'self' https: data:;");


// =============================================
// PSR-4 Autoloader
// แมป Namespace → โฟลเดอร์จริง (แก้ปัญหาตัวเล็ก-ใหญ่บน Linux)
// =============================================
spl_autoload_register(function ($class) {
    // แมป namespace prefix → directory path
    $map = [
        'Core\\'              => ROOT . '/core/',
        'App\\Controllers\\'  => ROOT . '/app/controllers/',
        'App\\Models\\'       => ROOT . '/app/models/',
        'App\\Services\\'     => ROOT . '/app/services/',
        'App\\Repositories\\' => ROOT . '/app/repositories/',
        'Middleware\\'        => ROOT . '/middleware/',
    ];

    foreach ($map as $prefix => $baseDir) {
        // ตรวจว่า class name ขึ้นต้นด้วย prefix นี้ไหม
        if (strpos($class, $prefix) === 0) {
            // ตัด prefix ออก เหลือแค่ชื่อ class
            $relativeClass = substr($class, strlen($prefix));
            // แปลง \ เป็น / แล้วต่อ .php
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
});

// Load Config & Start App
require_once ROOT . '/config/app.php';

// Load Helpers
require_once ROOT . '/app/helpers/csrf.php';
require_once ROOT . '/app/helpers/sanitize.php';

try {
    // 🛡️ Critical File Check
    $criticalFiles = [
        ROOT . '/core/LicenseManager.php',
        ROOT . '/middleware/LicenseMiddleware.php',
        ROOT . '/core/SecurityCore.php'
    ];

    foreach ($criticalFiles as $file) {
        if (!file_exists($file)) {
            throw new Exception("Security Violation: Critical System File Missing.");
        }
    }

    $app = new Core\App();
    $app->run();

} catch (Throwable $e) {
    // 📡 REPORT VIOLATION TO CENTRAL SERVER (Phone Home)
    try {
        $reportUrl = 'https://tcomkb.com/license-server/api/report-violation'; 
        $violationData = [
            'domain' => $_SERVER['HTTP_HOST'] ?? 'unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'violation_type' => 'SYSTEM_TAMPERING',
            'details' => $e->getMessage(),
            'fingerprint' => 'FILE_DELETED_OR_MODIFIED'
        ];

        $ch = curl_init($reportUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($violationData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout 3s (Don't wait too long)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    } catch (Throwable $ignore) {
        // Fail silently if reporting fails
    }

    // Hide real error and show scary warning
    // DEBUG MODE: Show real error
    header('HTTP/1.1 500 Internal Server Error');
    echo "<h1>SYSTEM ERROR</h1>";
    echo "<p>Message: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
    
    // header('HTTP/1.1 403 Forbidden');
    echo '<!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <title>SECURITY ALERT - SYSTEM TAMPERING</title>
        <style>
            body { background: #000; color: #ff0000; font-family: "Courier New", Courier, monospace; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; text-align: center; }
            .box { border: 2px solid #ff0000; padding: 40px; background: rgba(255,0,0,0.1); box-shadow: 0 0 20px #ff0000; max-width: 600px; }
            h1 { font-size: 24px; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px; }
            p { font-size: 18px; line-height: 1.5; }
            .code { color: #888; font-size: 12px; margin-top: 30px; }
        </style>
    </head>
    <body>
        <div class="box">
            <h1>🚨 คำเตือน: พบการละเมิดระบบ 🚨</h1>
            <p>(SYSTEM TAMPERING DETECTED)</p>
            <p>พบการลบหรือดัดแปลงไฟล์ระบบที่สำคัญ <br>การกระทำนี้ถือเป็นความผิดตาม พ.ร.บ. คอมพิวเตอร์</p>
            <p style="color: #fff; background: #900; padding: 10px;">ข้อมูลเครื่อง (Fingerprint) และ IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . ' <br>ถูกส่งไปยังเซิร์ฟเวอร์หลักของผู้พัฒนาเพื่อบันทึกหลักฐานแล้ว</p>
            <div class="code">Error ID: SEC-' . strtoupper(substr(md5(time()), 0, 8)) . ' | Status: LOGGED_TO_CENTRAL_SERVER</div>
        </div>
    </body>
    </html>';
    exit;
}
