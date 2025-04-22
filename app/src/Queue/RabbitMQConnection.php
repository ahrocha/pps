<?php

namespace App\Queue;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQConnection
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private string $queue;

    public function __construct(string $queue = 'notifications', int $maxAttempts = 5, int $retryInterval = 2)
    {
        $this->queue = $queue;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $this->connection = new AMQPStreamConnection(
                    getenv('RABBITMQ_HOST') ?: 'pps_rabbitmq',
                    getenv('RABBITMQ_PORT') ?: 5672,
                    getenv('RABBITMQ_USER') ?: 'guest',
                    getenv('RABBITMQ_PASS') ?: 'guest'
                );

                $this->channel = $this->connection->channel();
                $this->channel->queue_declare($this->queue, false, true, false, false);
                return;
            } catch (AMQPIOException $e) {
                $attempt++;
                echo "[RabbitMQConnection] Tentativa {$attempt}/{$maxAttempts}. RabbitMQ falhou.  Tentando novamente...\n";
                sleep($retryInterval);
            }
        }

        throw new \Exception("Não foi possível conectar ao RabbitMQ após {$maxAttempts} tentativas.");
    }

    public function getChannel(): AMQPChannel
    {
        return $this->channel;
    }

    public function getConnection(): AMQPStreamConnection
    {
        return $this->connection;
    }

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function close(): void
    {
        $this->channel->close();
        $this->connection->close();
    }
}
