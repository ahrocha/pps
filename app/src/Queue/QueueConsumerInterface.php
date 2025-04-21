<?php

namespace App\Queue;

interface QueueConsumerInterface
{
    public function consume(string $queue, callable $handler): void;
    public function acknowledge(mixed $message): void;
    public function reject(mixed $message, bool $requeue): void;

}
