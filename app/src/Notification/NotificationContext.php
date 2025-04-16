<?php

namespace App\Notification;

class NotificationContext
{
    private NotificationStrategyInterface $strategy;

    public function __construct(NotificationStrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function send(NotificationDTO $notification): void
    {
        $this->strategy->send($notification);
    }
}
