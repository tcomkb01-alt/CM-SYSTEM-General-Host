<?php

namespace Core;

class SecurityCore {
    /**
     * Generate a unique server fingerprint
     */
    public static function getFingerprint() {
        $data = [
            $_SERVER['SERVER_ADDR'] ?? '127.0.0.1',
            $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            php_uname(),
            gethostname(),
            DIRECTORY_SEPARATOR
        ];
        
        // Add CPU/Memory info if possible (platform dependent)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $data[] = getenv('COMPUTERNAME');
            $data[] = getenv('PROCESSOR_IDENTIFIER');
        } else {
            $data[] = shell_exec('cat /proc/cpuinfo | grep serial');
        }

        return hash('sha256', implode('|', $data));
    }

    /**
     * Get the current domain
     */
    public static function getDomain() {
        return $_SERVER['HTTP_HOST'] ?? 'localhost';
    }

    /**
     * Get the server IP
     */
    public static function getServerIP() {
        return $_SERVER['SERVER_ADDR'] ?? $_SERVER['LOCAL_ADDR'] ?? '127.0.0.1';
    }
}
