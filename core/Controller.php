<?php

namespace Core;

abstract class Controller
{
    public function __construct()
    {
        // 🔐 Hidden Security Layer: Double-check license even if middleware is bypassed
        // Skip for license-related paths to avoid redirect loops
        $currentPath = $_SERVER['REQUEST_URI'];
        if (strpos($currentPath, '/license/') !== false) {
            return;
        }

        $result = \Core\LicenseManager::check();
        if ($result['status'] !== 'valid') {
            $baseUrl = $_ENV['APP_URL'] ?? '';
            header('Location: ' . $baseUrl . '/license/activate?reason=' . urlencode($result['message']));
            exit;
        }
    }

    protected function view(string $template, array $data = []): void
    {
        $view = new View();
        $view->render($template, $data);
    }

    protected function json(array $data, int $status = 200): void
    {
        $response = new Response();
        $response->json($data, $status);
    }

    protected function redirect(string $url): void
    {
        header("Location: " . $_ENV['APP_URL'] . $url);
        exit;
    }
}
