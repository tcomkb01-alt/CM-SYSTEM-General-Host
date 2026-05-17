<?php

namespace Core;

class Request
{
    public function all(): array
    {
        $data = array_merge($_GET, $_POST);
        $json = json_decode(file_get_contents('php://input'), true);
        if (is_array($json)) {
            $data = array_merge($data, $json);
        }
        return $data;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        $all = $this->all();
        return $all[$key] ?? $default;
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
