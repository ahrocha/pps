<?php

namespace App\Notification;

interface NotificationStrategyInterface
{
    public function send(array $data): void;
}