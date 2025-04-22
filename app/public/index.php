<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;
use App\Controllers\AuthController;
use App\Controllers\HealthController;
use App\Controllers\PayController;
use App\Controllers\TransferController;
use App\Core\SimpleContainer;
use App\Middleware\AuthMiddleware;
use App\Services\AuthenticationService;

header('Content-Type: application/json');

$router = new Router();
$container = new SimpleContainer();

$router->get('/', function () {
    echo json_encode(['status' => 'PPS online']);
});

$router->post('/login', function () use ($container) {
    $controller = $container->get(AuthController::class);
    $controller->login();
});

$router->get('/health', function () use ($container) {
    $controller = $container->get(HealthController::class);
    $controller->check();
});

$router->post('/transfer', function () use ($container) {
    $controller = $container->get(TransferController::class);
    $controller->transfer();
});

$router->post('/pay', function () use ($container) {
    $authMiddleware = new AuthMiddleware($container->get(AuthenticationService::class));
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? null;

    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Token de autenticação não fornecido.']);
        return;
    }

    try {
        $user = $authMiddleware->authenticate(str_replace('Bearer ', '', $token));
        $controller = $container->get(PayController::class);
        $controller->pay($user);
    } catch (Exception $e) {
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint não encontrado']);
    }
});

$router->set404(function () {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint não encontrado']);
});

$router->run();
