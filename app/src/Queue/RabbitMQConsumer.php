<?php

namespace App\Queue;

use App\Core\LoggerService;
use App\Notification\NotificationContext;
use App\Notification\DeviToolsNotificationStrategy;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;

class RabbitMQConsumer implements QueueConsumerInterface
{
    private $connection;
    private $channel;
    private string $queue;

    public function __construct(string $queue = 'notifications')
    {
        $this->queue = $queue;

        $maxAttempts = 5;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $this->connection = new AMQPStreamConnection(
                    getenv('RABBITMQ_HOST') ?: 'pps_rabbitmq',
                    5672,
                    getenv('RABBITMQ_USER') ?: 'guest',
                    getenv('RABBITMQ_PASS') ?: 'guest'
                );

                $this->channel = $this->connection->channel();
                $this->channel->queue_declare($this->queue, false, true, false, false);
                break;
            } catch (AMQPIOException $e) {
                $attempt++;
                echo "[WORKER] Tentativa {$attempt}/{$maxAttempts}. RabbitMQ falhou.  Tentando novamente...\n";
                sleep(2);
            }
        }

        if (!$this->connection) {
            throw new \Exception("Não foi possível conectar ao RabbitMQ após {$maxAttempts} tentativas.");
        }

        LoggerService::getLogger()->info("Conexão com RabbitMQ estabelecida.");
    }

    public function consume(callable $handler): void
    {
        $this->channel->basic_consume($this->queue, '', false, false, false, false, function ($msg) use ($handler) {
            try {
                $handler(json_decode($msg->body, true));
                $this->channel->basic_ack($msg->getDeliveryTag());
            } catch (\Exception $e) {
                LoggerService::getLogger()->error("Erro no handler da fila: " . $e->getMessage());
                $this->channel->basic_nack($msg->getDeliveryTag(), false, true);
            }
        });

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }
}
