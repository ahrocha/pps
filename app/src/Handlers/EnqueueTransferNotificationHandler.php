<?php

namespace App\Handlers;

use App\Services\NotificationQueueService;
use App\Core\LoggerService;

class EnqueueTransferNotificationHandler extends AbstractTransferHandler
{
    private NotificationQueueService $queue;

    public function __construct(NotificationQueueService $queue)
    {
        $this->queue = $queue;
    }

    public function handle(array $payer, array $payee, float $value): void
    {
        $this->queue->publish([
            'type' => 'transfer',
            'user_id' => $payee['id'],
            'message' => "Você recebeu R$ {$value} de {$payer['name']}"
        ]);

        LoggerService::getLogger()->info("Notificação de transferência enfileirada para usuário #{$payee['id']}");

        $this->next($payer, $payee, $value);
    }
}
