<?php

namespace App\Queue;

interface MessagePublisherInterface
{
    public function publish(string $queue, array $data): void;
}