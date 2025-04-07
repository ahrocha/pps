<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;

class TransferService
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function transfer(float $value, int $payerId, int $payeeId): void
    {
        $payer = $this->users->find($payerId);
        $payee = $this->users->find($payeeId);

        if ($payer['type'] === 'merchant') {
            throw new Exception("Lojistas não podem realizar transferências.");
        }

        if ($payer['balance'] < $value) {
            throw new Exception("Saldo insuficiente para realizar a transferência.");
        }

        if (!$this->mockAuthorize()) {
            throw new Exception("Transação não autorizada pelo serviço externo.");
        }

        $payer['balance'] -= $value;
        $payee['balance'] += $value;

        $this->mockNotify($payee['id']);

        echo "[TRANSFERÊNCIA SIMULADA] R$ {$value} de {$payer['name']} (#{$payerId}) para {$payee['name']} (#{$payeeId})\n";
    }

    private function mockAuthorize(): bool
    {
        return true;
    }

    private function mockNotify(int $userId): void
    {
        echo "[NOTIFICAÇÃO SIMULADA] Enviada notificação ao usuário #{$userId}\n";
    }
}
