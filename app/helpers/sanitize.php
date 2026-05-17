<?php

/**
 * Sanitize output to prevent XSS
 */
if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Sanitize an array of data
 */
if (!function_exists('sanitizeArray')) {
    function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = sanitizeArray($value);
            } else {
                $sanitized[$key] = trim(htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'));
            }
        }
        return $sanitized;
    }
}
