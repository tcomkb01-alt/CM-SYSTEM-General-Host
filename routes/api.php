<?php
// routes/api.php
/** @var Core\Router $router */

$router->get('/api/v1/health', function() {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'version' => '1.0.0']);
});
