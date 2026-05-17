<?php
/**
 * Application Configuration
 * โหลดค่าจากไฟล์ .env
 */

function loadEnv($path) {
    if (!file_exists($path)) return;
    
    // อ่านไฟล์ทั้งหมดแล้วแปลง CRLF → LF (แก้ปัญหา Windows → Linux)
    $content = file_get_contents($path);
    $content = str_replace("\r\n", "\n", $content);
    $lines = explode("\n", $content);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // ลบ quotes ถ้ามี
        $value = trim($value, '"\'');
        
        $_ENV[$name] = $value;
        putenv("{$name}={$value}");
    }
}

loadEnv(dirname(__DIR__) . '/.env');

return [
    'env'      => $_ENV['APP_ENV'] ?? 'production',
    'debug'    => ($_ENV['APP_DEBUG'] ?? 'false') === 'true',
    'url'      => $_ENV['APP_URL'] ?? 'https://tcomkb.comt',
    'key'      => $_ENV['APP_KEY'] ?? '',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Asia/Bangkok',
];
