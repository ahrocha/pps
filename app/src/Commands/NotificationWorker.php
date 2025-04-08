<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\LoggerService;
use App\Notification\NotificationContext;
use App\Notification\DeviToolsNotificationStrategy;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;

$maxAttempts = 5;
$attempt = 0;

/** @var AMQPStreamConnection|null $connection */
$connection = null;

while ($attempt < $maxAttempts) {
    try {
        $connection = new AMQPStreamConnection(
            getenv('RABBITMQ_HOST') ?: 'pps_rabbitmq',
            5672,
            getenv('RABBITMQ_USER') ?: 'guest',
            getenv('RABBITMQ_PASS') ?: 'guest'
        );

        break;
    } catch (AMQPIOException $e) {
        $attempt++;
        echo "[WORKER] Tentativa {$attempt}/{$maxAttempts} falhou. RabbitMQ não está pronto. Tentando novamente...\n";
        sleep(2);
    }
}

if (!$connection instanceof AMQPStreamConnection) {
    throw new Exception("Não foi possível conectar ao RabbitMQ após {$maxAttempts} tentativas.");
}

LoggerService::getLogger()->info("Worker de notificação iniciado com sucesso.");

/** @var \PhpAmqpLib\Channel\AMQPChannel $channel */
$channel = $connection->channel();
$channel->queue_declare('notifications', false, true, false, false);

$channel->basic_consume('notifications', '', false, false, false, false, function ($msg) use ($channel) {
    $data = json_decode($msg->body, true);

    try {
        $context = new NotificationContext(new DeviToolsNotificationStrategy());
        $context->send($data);
        $channel->basic_ack($msg->getDeliveryTag());
    } catch (Exception $e) {
        LoggerService::getLogger()->error("Erro ao enviar notificação: " . $e->getMessage());
        $channel->basic_nack($msg->getDeliveryTag(), false, true);
    }
});

while ($channel->is_consuming()) {
    $channel->wait();
}
