<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        // 🔐 Core Security: Prevent Database access if license is invalid
        $license = LicenseManager::check();
        if ($license['status'] !== 'valid') {
            // Redirect or fail silently to confuse crackers
            header('Location: ' . ($_ENV['APP_URL'] ?? '') . '/license/activate?reason=' . urlencode($license['message']));
            exit("Security Violation: Unauthorized System Access.");
        }

        if (self::$instance === null) {
            $config = require dirname(__DIR__) . '/config/database.php';
            
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}";
            
            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Database Connection Error: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function queryOne(string $sql, array $params = []): ?array
    {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = self::getInstance()->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }
}
