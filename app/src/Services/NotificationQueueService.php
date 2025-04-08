<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Exception;

class NotificationQueueService
{
    private AMQPStreamConnection $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            getenv('RABBITMQ_HOST') ?: 'pps_rabbitmq',
            5672,
            getenv('RABBITMQ_USER') ?: 'guest',
            getenv('RABBITMQ_PASS') ?: 'guest'
        );
    }

    public function publish(array $data): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare('notifications', false, true, false, false);

        $message = new AMQPMessage(json_encode($data), [
            'content_type' => 'application/json',
            'delivery_mode' => 2
        ]);

        $channel->basic_publish($message, '', 'notifications');
        $channel->close();
        $this->connection->close();
    }
}
