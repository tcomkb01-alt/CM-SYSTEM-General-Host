<?php
return [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'name' => $_ENV['DB_NAME'] ?? 'tcomkb_CM_System',
    'user' => $_ENV['DB_USER'] ?? 'tcomkb_CM_System',
    'pass' => $_ENV['DB_PASS'] ?? '%VN!gah4iLr13pon',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
];
