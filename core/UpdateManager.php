<?php

namespace Core;

use Exception;

class UpdateManager {
    private static $update_url = 'https://tcomkb.id/license-server/api/check-update';
    private static $version_file = __DIR__ . '/../config/version.php';

    /**
     * Check for new updates
     */
    public static function check() {
        $currentVersion = self::getCurrentVersion();
        $license = LicenseManager::getCache();

        if (!$license) return false;

        try {
            $ch = curl_init(self::$update_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                'license_key' => $license['license_key'],
                'current_version' => $currentVersion,
                'domain' => SecurityCore::getDomain()
            ]));
            
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if ($response['status'] === 'update_available') {
                return $response['update_info'];
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Download and Apply Patch (Stub)
     */
    public static function applyUpdate($download_url, $checksum) {
        // 1. Download ZIP to temporary folder
        // 2. Verify Checksum (hash_file('sha256', $file))
        // 3. Extract and Overwrite
        // 4. Run Migration Scripts if any
        // 5. Update version.php
        return true;
    }

    private static function getCurrentVersion() {
        if (file_exists(self::$version_file)) {
            $config = include self::$version_file;
            return $config['version'] ?? '1.0.0';
        }
        return '1.0.0';
    }
}
