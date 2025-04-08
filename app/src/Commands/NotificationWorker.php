<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Core\LoggerService;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;

$maxAttempts = 5;
$attempt = 0;

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
        LoggerService::getLogger()->error("RabbitMQ não está pronto. Tentativa {$attempt}/{$maxAttempts} falhou.");
        sleep(2);
    }
}

if (!isset($connection)) {
    throw new Exception("Não foi possível conectar ao RabbitMQ após {$maxAttempts} tentativas.");
}

LoggerService::getLogger()->info("Worker de notificação iniciado com sucesso.");

$channel = $connection->channel();
$channel->queue_declare('notifications', false, true, false, false);

$callback = function ($msg) use ($channel) {
    $data = json_decode($msg->body, true);
    $userId = $data['user_id'] ?? null;
    $message = $data['message'] ?? null;

    try {
        $url = 'https://util.devi.tools/api/v1/notify';
        $payload = json_encode([
            'user_id' => $userId,
            'message' => $message,
        ]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 2,
            ]
        ]);

        $result = @file_get_contents($url, false, $context);

        if ($result === false) {
            throw new Exception("Erro ao notificar usuário #$userId");
        }

        LoggerService::getLogger()->info("Notificação enviada com sucesso para usuário #$userId");
        $channel->basic_ack($msg->getDeliveryTag());
    } catch (Exception $e) {
        LoggerService::getLogger()->error("Erro no envio de notificação: " . $e->getMessage());
        $channel->basic_nack($msg->getDeliveryTag(), false, true);
    }
};

$channel->basic_consume('notifications', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
    sleep(5);
}
