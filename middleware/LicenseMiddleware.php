<?php

namespace Middleware;

use Core\LicenseManager;

class LicenseMiddleware {
    public function handle() {
        // Exclude activation and submission paths to prevent redirect loops
        $currentPath = $_SERVER['REQUEST_URI'];
        if (strpos($currentPath, '/license/') !== false) {
            return;
        }

        $result = LicenseManager::check();

        if ($result['status'] !== 'valid') {
            // Redirect to license activation page if not valid
            $baseUrl = $_ENV['APP_URL'] ?? '';
            header('Location: ' . $baseUrl . '/license/activate?reason=' . urlencode($result['message']));
            exit;
        }
    }
}
