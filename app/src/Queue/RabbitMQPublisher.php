<?php

namespace App\Queue;

use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher extends RabbitMQConnection implements MessagePublisherInterface
{
    public function publish(string $queue, array $data): void
    {
        $channel = $this->getChannel();

        $message = new AMQPMessage(json_encode($data), [
            'content_type' => 'application/json',
            'delivery_mode' => 2
        ]);

        $channel->basic_publish($message, '', $queue);
        $this->close();
    }
}