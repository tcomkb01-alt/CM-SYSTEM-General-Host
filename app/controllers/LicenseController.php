<?php

namespace App\Controllers;

use Core\LicenseManager;
use Core\Controller;

class LicenseController extends Controller {
    /**
     * Show Activation Page
     */
    public function activate() {
        $reason = $_GET['reason'] ?? 'Enter your license key to continue.';
        $this->view('license.activate', ['reason' => $reason]);
    }

    /**
     * Handle Activation Submission (AJAX)
     */
    public function submit() {
        $key = $_POST['license_key'] ?? '';

        try {
            if (empty($key)) {
                throw new \Exception("Please enter a license key.");
            }

            LicenseManager::activate($key);

            echo json_encode(['status' => 'success', 'message' => 'License activated successfully!']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
