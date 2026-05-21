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
        
        // Add MAC address for stronger fingerprint
        $mac = self::getMACAddress();
        if ($mac) {
            $data[] = $mac;
        }
        
        // Add CPU/Memory info if possible (platform dependent)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $data[] = getenv('COMPUTERNAME');
            $data[] = getenv('PROCESSOR_IDENTIFIER');
        } else {
            $cpuinfo = shell_exec('cat /proc/cpuinfo | grep serial');
            if ($cpuinfo) {
                $data[] = trim($cpuinfo);
            }
        }

        return hash('sha256', implode('|', array_filter($data)));
    }

    /**
     * Get MAC address of the primary network interface
     */
    private static function getMACAddress() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            $mac = @shell_exec("ipconfig /all | findstr /I \"physical address\"");
            if ($mac) {
                preg_match('/([0-9A-F]{2}(-[0-9A-F]{2}){5})/', $mac, $matches);
                return $matches[1] ?? null;
            }
        } else {
            // Linux/Unix
            $mac = @shell_exec("ip link show | grep -A 1 'link/ether' | grep 'ether' | head -1 | awk '{print $2}'");
            if ($mac) {
                return trim($mac);
            }
        }
        return null;
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
