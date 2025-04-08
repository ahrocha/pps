<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;
use App\Controllers\TransferController;
use App\Controllers\HealthController;

header('Content-Type: application/json');

$router = new Router();

$router->get('/', function () {
    echo json_encode(['status' => 'PPS online']);
});

$router->get('/health', [new HealthController(), 'check']);

$router->post('/transfer', [new TransferController(), 'transfer']);

$router->set404(function () {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint não encontrado']);
});

$router->run();
