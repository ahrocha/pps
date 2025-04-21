<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\LoggerService;
use App\Queue\RabbitMQConsumer as MessageQueueConsumer;
use App\Notification\NotificationContext;
use App\Notification\DeviToolsNotificationStrategy;
use App\Notification\NotificationDTO;

LoggerService::getLogger()->info("Iniciando worker de notificação...");

$consumer = new MessageQueueConsumer();
$context = new NotificationContext(new DeviToolsNotificationStrategy());

$queueName = 'notifications';

$consumer->consume($queueName, function (NotificationDTO $notification) use ($context) {
    $context->send($notification);
});
