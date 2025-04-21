<?php

namespace App\Services;

use App\Queue\MessagePublisherInterface;

class NotificationQueueService
{
    private MessagePublisherInterface $publisher;
    private string $queueName = 'notifications';

    public function __construct(MessagePublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    public function publish(array $data): void
    {
        $this->publisher->publish($this->queueName, $data);
    }
}