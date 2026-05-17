<?php
/**
 * CM_System Standalone Installer v1.3
 * This file will self-destruct after successful installation.
 */

$error = '';
$success = false;

// 1. New SQL Schema (from tcomkb_CM_System.sql)
$sql_content = "
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET time_zone = \"+00:00\";

-- Activity Logs
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(10) UNSIGNED DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Assignments & Scores
CREATE TABLE IF NOT EXISTS `assignments` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `max_score` decimal(5,2) DEFAULT 10.00,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `assignment_scores` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `score` decimal(8,2) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_assignment_student` (`assignment_id`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `assignment_submissions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `assignment_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `status` enum('pending','submitted','graded') DEFAULT 'pending',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_submission` (`assignment_id`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attendance
CREATE TABLE IF NOT EXISTS `attendance_records` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `session_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `status` enum('present','absent','late','leave') NOT NULL DEFAULT 'absent',
  `check_method` enum('manual','barcode','qr') DEFAULT NULL,
  `checked_at` datetime DEFAULT NULL,
  `created_at?` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at?` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_session_student` (`session_id`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `attendance_sessions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `session_date` date NOT NULL,
  `period_number` smallint(5) UNSIGNED DEFAULT NULL,
  `status` enum('active','closed','expired') NOT NULL DEFAULT 'active',
  `timer_minutes` smallint(5) UNSIGNED NOT NULL DEFAULT 15,
  `started_at` datetime NOT NULL DEFAULT current_timestamp(),
  `closed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Classrooms
CREATE TABLE IF NOT EXISTS `classrooms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `room_code` char(6) NOT NULL,
  `subject_name` varchar(150) NOT NULL,
  `subject_code` varchar(30) DEFAULT NULL,
  `total_hours` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `total_periods` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `pass_criteria` decimal(5,2) NOT NULL DEFAULT 80.00,
  `qr_code_path` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_code` (`room_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `classroom_students` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `joined_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_classroom_student` (`classroom_id`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Logs & Misc
CREATE TABLE IF NOT EXISTS `consent_logs` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `consent_type` enum('cookie','privacy','terms') NOT NULL,
  `consent_given` tinyint(1) NOT NULL DEFAULT 0,
  `consent_text?` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `status` enum('success','failed','locked') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `qr_codes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entity_type` enum('classroom','student') NOT NULL,
  `entity_id` int(10) UNSIGNED NOT NULL,
  `qr_data` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_qr_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings
CREATE TABLE IF NOT EXISTS `school_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_name` varchar(255) DEFAULT 'โรงเรียนตัวอย่าง',
  `school_name_en` varchar(255) DEFAULT 'Sample School',
  `school_logo` varchar(255) DEFAULT NULL,
  `academic_year` varchar(10) DEFAULT '2567',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admin_setting` (`admin_id`,`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Students
CREATE TABLE IF NOT EXISTS `students` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_code` varchar(20) NOT NULL,
  `national_id` varchar(13) DEFAULT NULL,
  `prefix` varchar(20) NOT NULL DEFAULT '',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `class_level` varchar(20) NOT NULL,
  `student_number` smallint(5) UNSIGNED NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `barcode_data` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_code` (`student_code`),
  UNIQUE KEY `national_id` (`national_id`),
  UNIQUE KEY `barcode_data` (`barcode_data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `student_cards` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `student_ids` longtext NOT NULL,
  `school_name` varchar(200) NOT NULL,
  `school_logo` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `display_name` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `theme` enum('light','dark') NOT NULL DEFAULT 'light',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `failed_attempts` tinyint(4) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial Settings Data
INSERT INTO `school_settings` (id, school_name, school_name_en, academic_year) VALUES (1, 'โรงเรียนตัวอย่าง', 'Sample School', '2567') ON DUPLICATE KEY UPDATE school_name=school_name;
";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? 'localhost';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';
    $admin_user = $_POST['admin_user'] ?? 'admin';
    $admin_pass = $_POST['admin_pass'] ?? '';
    $app_url = rtrim($_POST['app_url'] ?? '', '/');

    try {
        // 1. Connection
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // 2. Execute SQL
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        $queries = array_filter(array_map('trim', explode(';', $sql_content)));
        foreach ($queries as $query) {
            if (!empty($query)) { $pdo->exec($query); }
        }
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

        // 3. Create Master Admin (Targeting 'users' table)
        $hashedPass = password_hash($admin_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role, display_name) VALUES (?, ?, 'admin', ?)");
        $stmt->execute([$admin_user, $hashedPass, 'System Administrator']);

        // 4. Generate .env
        $app_key = bin2hex(random_bytes(32));
        $env_content = "# Application\n"
                     . "APP_ENV=production\n"
                     . "APP_DEBUG=false\n"
                     . "APP_URL=" . $app_url . "\n"
                     . "APP_KEY=" . $app_key . "\n"
                     . "APP_TIMEZONE=Asia/Bangkok\n\n"
                     . "# Database\n"
                     . "DB_HOST=" . $db_host . "\n"
                     . "DB_PORT=3306\n"
                     . "DB_NAME=" . $db_name . "\n"
                     . "DB_USER=" . $db_user . "\n"
                     . "DB_PASS=" . $db_pass . "\n"
                     . "DB_CHARSET=utf8mb4\n\n"
                     . "# Session\n"
                     . "SESSION_LIFETIME=7200\n"
                     . "SESSION_NAME=CM_SESSION\n\n"
                     . "# Paths\n"
                     . "UPLOAD_PATH=public/uploads\n"
                     . "STORAGE_PATH=storage\n";

        file_put_contents(__DIR__ . '/.env', $env_content);

        $success = true;
        @unlink(__FILE__);
        header("Refresh: 3; url=" . $app_url . "/public");

    } catch (Exception $e) {
        $error = "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}

// Auto-detect URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$detected_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . str_replace('/install.php', '', $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Installer - CM_System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0a0b1e; color: #e2e8f0; }
        .cyber-card { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(34, 211, 238, 0.1); }
        .cyber-input { background: rgba(0, 0, 0, 0.4); border: 1px solid rgba(34, 211, 238, 0.2); color: white; transition: all 0.3s; }
        .cyber-input:focus { border-color: #22d3ee; box-shadow: 0 0 15px rgba(34, 211, 238, 0.2); outline: none; }
        .cyber-btn { background: linear-gradient(45deg, #6366f1, #4f46e5); box-shadow: 0 0 20px rgba(99, 102, 241, 0.4); }
        .glow-text { text-shadow: 0 0 10px rgba(34, 211, 238, 0.5); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-slate-950">
    <div class="max-w-xl w-full">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-black text-white glow-text tracking-tighter uppercase">SYSTEM <span class="text-cyan-400">DEPLOY</span></h1>
            <p class="text-[10px] text-cyan-500/50 font-mono tracking-[0.4em] uppercase mt-2">v1.3.0 Standard Protocol</p>
        </div>

        <div class="cyber-card rounded-[2.5rem] p-10 shadow-2xl relative">
            <?php if ($success): ?>
                <div class="text-center py-10">
                    <div class="w-20 h-20 bg-emerald-500/20 text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-500/30">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">ติดตั้งเสร็จสมบูรณ์!</h3>
                    <p class="text-cyan-500 text-[10px] italic">Database Updated. กำลังไปที่ระบบ...</p>
                </div>
            <?php else: ?>
                <form action="" method="POST" class="space-y-6">
                    <?php if ($error): ?>
                        <div class="p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-400 text-xs font-bold"><?= $error ?></div>
                    <?php endif; ?>

                    <div class="space-y-4">
                        <label class="block text-[10px] text-slate-500 uppercase font-bold mb-2 ml-1">Application URL</label>
                        <input type="text" name="app_url" value="<?= $detected_url ?>" required class="w-full cyber-input rounded-xl px-5 py-4 text-sm font-mono text-cyan-400">
                    </div>

                    <div class="space-y-4 pt-4 border-t border-white/5">
                        <h4 class="text-cyan-500 text-[10px] font-black uppercase tracking-widest mb-2">Database Connection</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-full">
                                <label class="block text-[10px] text-slate-500 uppercase font-bold mb-1 ml-1">Host</label>
                                <input type="text" name="db_host" value="localhost" required class="w-full cyber-input rounded-xl px-5 py-3 text-sm font-mono">
                            </div>
                            <input type="text" name="db_name" placeholder="DB Name" required class="w-full cyber-input rounded-xl px-5 py-3 text-sm font-mono">
                            <input type="text" name="db_user" placeholder="DB User" required class="w-full cyber-input rounded-xl px-5 py-3 text-sm font-mono">
                            <div class="col-span-full"><input type="password" name="db_pass" placeholder="DB Password" class="w-full cyber-input rounded-xl px-5 py-3 text-sm font-mono"></div>
                        </div>
                    </div>

                    <div class="space-y-4 pt-4 border-t border-white/5">
                        <h4 class="text-indigo-500 text-[10px] font-black uppercase tracking-widest mb-2">Master Administrator</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="admin_user" value="admin" required class="w-full cyber-input rounded-xl px-5 py-3 text-sm font-mono">
                            <input type="password" name="admin_pass" placeholder="Admin Password" required class="w-full cyber-input rounded-xl px-5 py-3 text-sm font-mono">
                        </div>
                    </div>

                    <button type="submit" class="w-full cyber-btn text-white font-black py-4 rounded-xl transition-all uppercase tracking-widest text-xs">
                        EXECUTE DEPLOYMENT
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
