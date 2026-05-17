<?php

use Core\Session;

/**
 * Generate a CSRF token and store it in session
 */
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken(): string
    {
        if (!Session::get('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }
}

/**
 * Generate a hidden CSRF input field
 */
if (!function_exists('csrfField')) {
    function csrfField(): string
    {
        $token = generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }
}

/**
 * Validate CSRF token
 */
if (!function_exists('validateCsrfToken')) {
    function validateCsrfToken(?string $token): bool
    {
        $storedToken = Session::get('csrf_token');
        return $token && $storedToken && hash_equals($storedToken, $token);
    }
}
