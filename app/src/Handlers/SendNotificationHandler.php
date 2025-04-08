<?php

namespace App\Handlers;

class SendNotificationHandler extends AbstractTransferHandler
{
    public function handle(array $payer, array $payee, float $value): void
    {
        echo "[NOTIFICAÇÃO] Enviada notificação para usuário #{$payee['id']}\n";

        $this->next($payer, $payee, $value);
    }
}
