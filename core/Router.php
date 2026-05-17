<?php

namespace Core;

class Router
{
    protected array $routes = [];
    protected array $groupAttributes = [];

    public function add(string $method, string $uri, $action): void
    {
        $uri = $this->applyGroupPrefix($uri);
        $middleware = $this->groupAttributes['middleware'] ?? [];
        
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $this->formatUri($uri),
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function get(string $uri, $action): void { $this->add('GET', $uri, $action); }
    public function post(string $uri, $action): void { $this->add('POST', $uri, $action); }
    public function put(string $uri, $action): void { $this->add('PUT', $uri, $action); }
    public function delete(string $uri, $action): void { $this->add('DELETE', $uri, $action); }

    public function group(array $attributes, callable $callback): void
    {
        $previousGroupAttributes = $this->groupAttributes;
        $this->groupAttributes = array_merge_recursive($this->groupAttributes, $attributes);
        
        $callback($this);
        
        $this->groupAttributes = $previousGroupAttributes;
    }

    protected function applyGroupPrefix(string $uri): string
    {
        $prefix = $this->groupAttributes['prefix'] ?? '';
        return rtrim($prefix, '/') . '/' . ltrim($uri, '/');
    }

    protected function formatUri(string $uri): string
    {
        return '/' . trim($uri, '/');
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = $this->formatUri($uri);

        // === DEBUG: ลบออกตอน Production ===
        // echo "DEBUG: Method={$method}, URI={$uri}<br>";
        // ===================================

        foreach ($this->routes as $route) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9-]+)', $route['uri']);
            $pattern = "#^" . $pattern . "$#";

            if ($route['method'] === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match
                
                $this->handleAction($route['action'], $matches, $route['middleware']);
                return;
            }
        }

        $this->abort(404);
    }

    protected function handleAction($action, array $params, array $middleware): void
    {
        // Execute Middleware
        foreach ($middleware as $mwName) {
            $mwClass = "Middleware\\" . $mwName;
            if (class_exists($mwClass)) {
                $mw = new $mwClass();
                if (method_exists($mw, 'handle')) {
                    $mw->handle(new Request());
                }
            }
        }
        
        if (is_callable($action)) {
            call_user_func_array($action, $params);
            return;
        }

        if (is_string($action)) {
            list($controllerName, $method) = explode('@', $action);
            $controllerClass = "App\\Controllers\\" . $controllerName;
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $params);
                    return;
                }
            }
        }

        $this->abort(500);
    }

    protected function abort(int $code): void
    {
        http_response_code($code);
        $viewPath = dirname(__DIR__) . "/views/errors/{$code}.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "Error {$code}";
        }
        exit;
    }
}
