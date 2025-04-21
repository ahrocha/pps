<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;
use App\Controllers\TransferController;
use App\Controllers\HealthController;
use App\Core\SimpleContainer;

header('Content-Type: application/json');

$router = new Router();
$container = new SimpleContainer();

$router->get('/', function () {
    echo json_encode(['status' => 'PPS online']);
});

$router->get('/health', function () use ($container) {
    $controller = $container->get(HealthController::class);
    $controller->check();
});

$router->post('/transfer', function () use ($container) {
    $controller = $container->get(TransferController::class);
    $controller->transfer();
});

$router->set404(function () {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint não encontrado']);
});

$router->run();
