<?php

namespace App\Handlers;

use App\Core\LoggerService;
use App\Exceptions\InsufficientFundsException;
use App\Exceptions\InvalidTransferException;

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
            throw new InvalidTransferException("Pagador e recebedor não podem ser o mesmo usuário.");
        }

        if ($payer['type'] === 'lojista') {
            LoggerService::getLogger()->warning('Lojistas não podem realizar transferências', [
                'payer_id' => $payer['id'],
                'payee_id' => $payee['id'],
                'value' => $value,
            ]);
            throw new InvalidTransferException("Lojistas não podem realizar transferências.");
        }

        if ($payer['balance'] < $value) {
            LoggerService::getLogger()->warning('Saldo insuficiente para transferência', [
                'balance' => $payer['balance'],
                'payer_id' => $payer['id'],
                'payee_id' => $payee['id'],
                'value' => $value,
            ]);
            throw new InsufficientFundsException("Saldo insuficiente para realizar a transferência.");
        }

        $this->next($payer, $payee, $value);
    }
}
