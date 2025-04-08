<?php

namespace App\Repositories;

use App\Core\DatabaseService;
use Exception;
use PDO;

class WalletRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseService::getConnection();
    }

    public function getBalance(int $userId): float
    {
        $stmt = $this->pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception("Carteira não encontrada para o usuário ID $userId.");
        }

        return floatval($result['balance']);
    }

    public function updateBalance(int $userId, float $newBalance): void
    {
        $stmt = $this->pdo->prepare("UPDATE wallets SET balance = ? WHERE user_id = ?");
        $stmt->execute([$newBalance, $userId]);
    }
}
