<?php

namespace App\Repositories;

use App\Core\DatabaseService;
use Exception;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseService::getConnection();
    }

    public function find(int $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception("Usuário de ID $id não encontrado.");
        }

        $wallet = $this->getWalletBalance($id);
        $user['balance'] = $wallet;

        return $user;
    }

    private function getWalletBalance(int $userId): float
    {
        $stmt = $this->pdo->prepare("SELECT balance FROM wallets WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? floatval($result['balance']) : 0.0;
    }

    public function findByEmail(string $username): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
