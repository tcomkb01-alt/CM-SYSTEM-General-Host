<?php

namespace Core;

class View
{
    public function render(string $template, array $data = []): void
    {
        extract($data);
        $templatePath = str_replace('.', '/', $template);
        $viewFile = dirname(__DIR__) . "/views/{$templatePath}.php";
        
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            die("View not found: {$template}");
        }
    }
}
