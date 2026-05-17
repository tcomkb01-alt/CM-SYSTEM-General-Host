<?php

namespace Core;

use Exception;

class LicenseManager {
    private static function getConfig($key, $default = '') {
        return $_ENV[$key] ?? $default;
    }

    // These will now be fetched from .env
    private static function getServerUrl() {
        return self::getConfig('LICENSE_SERVER_URL', 'https://tcomkb.com/license-server/api');
    }

    private static function getHmacSecret() {
        return self::getConfig('LICENSE_HMAC_SECRET', '2b5e8f0a1b3c6d9e5f7d2e8a1b9c4d3e6f8a2b5c9d0e1f4a7b3c6d9e0f1a4b7c');
    }

    private static $cache_file = __DIR__ . '/../storage/license.cache';

    /**
     * Check if the license is valid (Local Cache + periodic online check)
     */
    public static function check() {
        $cache = self::getCache();

        // 1. If no cache, must activate
        if (!$cache) {
            return ['status' => 'unlicensed', 'message' => 'Please activate your license.'];
        }

        // 2. Hardware Fingerprint Check (Prevention of Redistribution)
        if (!isset($cache['fingerprint']) || $cache['fingerprint'] !== SecurityCore::getFingerprint()) {
            return ['status' => 'invalid_hardware', 'message' => 'This license is not registered for this server/hardware.'];
        }

        // 3. Basic Domain Check (Anti-Bypass)
        if ($cache['domain'] !== SecurityCore::getDomain()) {
            return ['status' => 'invalid_domain', 'message' => 'License domain mismatch.'];
        }

        // 4. Expiry Check
        if ($cache['expires_at'] && strtotime($cache['expires_at']) < time()) {
            return ['status' => 'expired', 'message' => 'License has expired.'];
        }

        // 5. Periodic Online Verification (Every 24 hours)
        if (time() > ($cache['last_verified'] + 86400)) {
            return self::verifyOnline($cache['license_key']);
        }

        return ['status' => 'valid', 'data' => $cache];
    }

    /**
     * Activate the license
     */
    public static function activate($license_key) {
        $params = [
            'license_key' => $license_key,
            'domain' => SecurityCore::getDomain(),
            'server_ip' => SecurityCore::getServerIP(),
            'fingerprint' => SecurityCore::getFingerprint(),
            'app_version' => '1.0.0'
        ];

        $response = self::callApi('activate', $params);

        if ($response['status'] === 'success') {
            // Verify Signature
            if (!self::verifySignature($response['data'], $response['signature'])) {
                throw new Exception("Security Alert: Invalid server signature detected.");
            }

            $cacheData = array_merge($response['data'], [
                'license_key' => $license_key,
                'fingerprint' => SecurityCore::getFingerprint(), // Lock to this hardware
                'last_verified' => time()
            ]);

            self::saveCache($cacheData);
            return true;
        }

        throw new Exception($response['message'] ?? 'Activation failed.');
    }

    /**
     * Online Verification
     */
    public static function verifyOnline($license_key) {
        $params = [
            'license_key' => $license_key,
            'domain' => SecurityCore::getDomain(),
            'fingerprint' => SecurityCore::getFingerprint()
        ];

        try {
            $response = self::callApi('verify', $params);

            if ($response['status'] === 'success') {
                if (!self::verifySignature($response['data'], $response['signature'])) {
                    return ['status' => 'tampered', 'message' => 'Signature verification failed.'];
                }

                $cache = self::getCache();
                $cache['last_verified'] = time();
                $cache['status'] = $response['data']['status'];
                self::saveCache($cache);

                return ['status' => 'valid', 'data' => $cache];
            }
        } catch (Exception $e) {
            // If server is down, allow 3 days of offline grace period
            $cache = self::getCache();
            if ($cache && time() < ($cache['last_verified'] + (86400 * 3))) {
                return ['status' => 'valid', 'message' => 'Offline mode active (Server unreachable).', 'data' => $cache];
            }
            return ['status' => 'error', 'message' => 'Unable to verify license online.'];
        }

        return ['status' => 'invalid', 'message' => 'License verification failed.'];
    }

    private static function callApi($endpoint, $params) {
        $url = self::getServerUrl() . '/' . $endpoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Important for some local environments
        
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception("Connection Error: " . curl_error($ch));
        }
        curl_close($ch);

        $data = json_decode($result, true);
        if ($data === null) {
            throw new Exception("Server Error: Invalid response from license server.");
        }

        return $data;
    }

    private static function verifySignature($data, $signature) {
        $expected = hash_hmac('sha256', json_encode($data), self::getHmacSecret());
        return hash_equals($expected, $signature);
    }

    private static function saveCache($data) {
        // Ensure storage directory exists
        $dir = dirname(self::$cache_file);
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        // In production, encrypt this file content
        file_put_contents(self::$cache_file, json_encode($data));
    }

    public static function getCache() {
        if (!file_exists(self::$cache_file)) return null;
        return json_decode(file_get_contents(self::$cache_file), true);
    }
}
