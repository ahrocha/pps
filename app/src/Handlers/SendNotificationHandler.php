<?php

namespace App\Handlers;

use App\Core\LoggerService;
use App\Services\NotificationQueueService;

class SendNotificationHandler extends AbstractTransferHandler
{
    private NotificationQueueService $queue;

    public function __construct(NotificationQueueService $queue)
    {
        $this->queue = $queue;
    }

    public function handle(array $payer, array $payee, float $value): void
    {
        $this->queue->publish([
            'user_id' => $payee['id'],
            'message' => "Você recebeu R$ {$value} de {$payer['name']}"
        ]);

        LoggerService::getLogger()->info("Notificação enfileirada para usuário #{$payee['id']}");

        $this->next($payer, $payee, $value);
    }
}