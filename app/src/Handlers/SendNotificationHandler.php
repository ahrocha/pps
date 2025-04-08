<?php

namespace App\Handlers;

use App\Core\LoggerService;

class SendNotificationHandler extends AbstractTransferHandler
{
    public function handle(array $payer, array $payee, float $value): void
    {
        LoggerService::getLogger()->info("[NOTIFICAÇÃO] Enviada notificação para usuário #{$payee['id']}");

        $this->next($payer, $payee, $value);
    }
}
