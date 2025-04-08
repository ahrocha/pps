<?php

namespace App\Services;

use App\Core\DatabaseService;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use Exception;
use PDO;

class TransferService
{
    private PDO $pdo;
    private UserRepository $userRepository;
    private WalletRepository $walletRepository;
    private TransactionRepository $transactionRepository;

    public function __construct()
    {
        $this->pdo = DatabaseService::getConnection();
        $this->userRepository = new UserRepository();
        $this->walletRepository = new WalletRepository();
        $this->transactionRepository = new TransactionRepository();
    }

    public function transfer(float $value, int $payerId, int $payeeId): void
    {
        $payer = $this->userRepository->find($payerId);
        $payee = $this->userRepository->find($payeeId);

        if (!$this->mockAuthorize()) {
            throw new Exception("Transação não autorizada pelo serviço externo.");
        }

        try {
            $this->pdo->beginTransaction();

            $payerBalance = $this->walletRepository->getBalance($payerId);
            $payeeBalance = $this->walletRepository->getBalance($payeeId);

            $this->walletRepository->updateBalance($payerId, $payerBalance - $value);
            $this->walletRepository->updateBalance($payeeId, $payeeBalance + $value);

            $this->transactionRepository->create($value, $payerId, $payeeId);

            $this->pdo->commit();

            $this->mockNotify($payeeId);
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro durante a transferência: " . $e->getMessage());
        }
    }

    private function mockAuthorize(): bool
    {
        $random = random_int(1, 10);

        echo "[AUTORIZADOR] Valor sorteado: $random\n";

        return $random > 3;
}

    private function mockNotify(int $userId): void
    {
        echo "[NOTIFICAÇÃO] Notificação enviada ao usuário #$userId\n";
    }
}
