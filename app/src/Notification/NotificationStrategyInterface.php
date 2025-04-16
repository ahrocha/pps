<?php

namespace App\Notification;

interface NotificationStrategyInterface
{
    public function send(NotificationDTO $notification): void;
}
