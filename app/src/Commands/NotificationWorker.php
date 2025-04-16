<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\LoggerService;
use App\Queue\RabbitMQConsumer; // ou SqsConsumer futuramente
use App\Notification\NotificationContext;
use App\Notification\DeviToolsNotificationStrategy;

LoggerService::getLogger()->info("Iniciando worker de notificação...");

$consumer = new RabbitMQConsumer(); // Aqui poderia vir de uma fábrica ou config
$context = new NotificationContext(new DeviToolsNotificationStrategy());

$consumer->consume(function (array $data) use ($context) {
    $context->send($data);
});
