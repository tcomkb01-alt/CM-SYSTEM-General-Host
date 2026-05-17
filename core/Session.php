<?php

namespace Core;

class Session
{
    /**
     * Start the session with secure settings
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session cookie parameters
            $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            if (!$isSecure && isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                $isSecure = true;
            }

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
        }
    }

    /**
     * Set a session value
     */
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Remove a session value
     */
    public static function remove(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy the session
     */
    public static function destroy(): void
    {
        session_unset();
        session_destroy();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
    }

    /**
     * Regenerate session ID for security
     */
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Set a flash message
     */
    public static function setFlash(string $key, mixed $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    /**
     * Get and clear a flash message
     */
    public static function getFlash(string $key): mixed
    {
        $message = $_SESSION['_flash'][$key] ?? null;
        if ($message) {
            unset($_SESSION['_flash'][$key]);
        }
        return $message;
    }

    /**
     * Check if user is logged in
     */
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }
}
