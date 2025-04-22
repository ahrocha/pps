<?php

namespace App\Queue;

use App\Core\LoggerService;
use App\Notification\NotificationDTO;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQConsumer extends RabbitMQConnection implements QueueConsumerInterface
{
    public function consume(string $queue, callable $handler): void
    {
        $channel = $this->getChannel();
        $channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,
            function (AMQPMessage $message) use ($handler, $channel) {
                try {
                    $data = NotificationDTO::fromJson($message->body);
                    $handler($data, true);
                    $channel->basic_ack($message->getDeliveryTag());
                } catch (\Exception $e) {
                    LoggerService::getLogger()->error("Erro no handler da fila: " . $e->getMessage());
                    $channel->basic_nack($message->getDeliveryTag(), false, true);
                }
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    public function acknowledge(mixed $message): void
    {
        $this->getChannel()->basic_ack($message->getDeliveryTag());
    }

    public function reject(mixed $message, bool $requeue): void
    {
        $this->getChannel()->basic_nack($message->getDeliveryTag(), false, $requeue);
    }
}
