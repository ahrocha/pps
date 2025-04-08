<?php

namespace App\Handlers;

use App\Core\DatabaseService;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use Exception;
use PDO;
use App\Core\LoggerService;
use App\Services\AuthorizationService;

class ExecuteTransactionHandler extends AbstractTransferHandler
{
    private PDO $pdo;
    private WalletRepository $walletRepository;
    private TransactionRepository $transactionRepository;
    private AuthorizationService $authorization;

    public function __construct()
    {
        $this->pdo = DatabaseService::getConnection();
        $this->walletRepository = new WalletRepository();
        $this->transactionRepository = new TransactionRepository();
        $this->authorization = new AuthorizationService();
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

            if (!$this->authorize()) {
                throw new Exception("Transação não autorizada pelo serviço externo.");
            }

            $this->pdo->commit();
            LoggerService::getLogger()->info('Transferência executada com sucesso');
        } catch (Exception $e) {
            $this->pdo->rollBack();
            LoggerService::getLogger()->error('Erro na execução da transação: ' . $e->getMessage());
            throw new Exception("Erro na execução da transação: " . $e->getMessage());
        }

        $this->next($payer, $payee, $value);
    }

    private function authorize(): bool
    {
        return $this->authorization->authorize();
    }
    
}
