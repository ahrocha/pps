<?php

namespace App\Handlers;

use App\Core\DatabaseService;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use Exception;
use PDO;

class ExecuteTransactionHandler extends AbstractTransferHandler
{
    private PDO $pdo;
    private WalletRepository $walletRepository;
    private TransactionRepository $transactionRepository;

    public function __construct()
    {
        $this->pdo = DatabaseService::getConnection();
        $this->walletRepository = new WalletRepository();
        $this->transactionRepository = new TransactionRepository();
    }

    public function handle(array $payer, array $payee, float $value): void
    {
        try {
            $this->pdo->beginTransaction();

            $payerBalance = $this->walletRepository->getBalance($payer['id']);
            $payeeBalance = $this->walletRepository->getBalance($payee['id']);

            $this->walletRepository->updateBalance($payer['id'], $payerBalance - $value);
            $this->walletRepository->updateBalance($payee['id'], $payeeBalance + $value);

            $this->transactionRepository->create($value, $payer['id'], $payee['id']);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erro na execução da transação: " . $e->getMessage());
        }

        $this->next($payer, $payee, $value);
    }
}
