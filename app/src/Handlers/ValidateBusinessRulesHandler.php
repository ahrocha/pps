<?php

namespace App\Handlers;

use Exception;

class ValidateBusinessRulesHandler extends AbstractTransferHandler
{
    public function handle(array $payer, array $payee, float $value): void
    {
        if ($payer['type'] === 'merchant') {
            throw new Exception("Lojistas não podem realizar transferências.");
        }

        if ($payer['balance'] < $value) {
            throw new Exception("Saldo insuficiente para realizar a transferência.");
        }

        $this->next($payer, $payee, $value);
    }
}
