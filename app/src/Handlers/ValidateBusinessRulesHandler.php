<?php

namespace App\Handlers;

use App\Core\LoggerService;
use Exception;

class ValidateBusinessRulesHandler extends AbstractTransferHandler
{
    public function handle(array $payer, array $payee, float $value): void
    {
        if ($payer['id'] === $payee['id']) {
            LoggerService::getLogger()->warning('Transferência entre o mesmo usuário', [
                'payer_id' => $payer['id'],
                'payee_id' => $payee['id'],
                'value' => $value,
            ]);
            throw new Exception("Pagador e recebedor não podem ser o mesmo usuário.");
        }

        if ($payer['type'] === 'merchant') {
            LoggerService::getLogger()->warning('Lojistas não podem realizar transferências', [
                'payer_id' => $payer['id'],
                'payee_id' => $payee['id'],
                'value' => $value,
            ]);
            throw new Exception("Lojistas não podem realizar transferências. " . $payer['id'] . " " . $payer['name'] . " " . $payer['type'] . " " . $payer['balance'] . " " . $value);
        }

        if ($payer['balance'] < $value) {
            LoggerService::getLogger()->warning('Saldo insuficiente para transferência', [
                'balance' => $payer['balance'],
                'payer_id' => $payer['id'],
                'payee_id' => $payee['id'],
                'value' => $value,
            ]);
            throw new Exception("Saldo insuficiente para realizar a transferência.");
        }

        $this->next($payer, $payee, $value);
    }
}
