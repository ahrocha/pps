<?php

namespace App\Repositories;

use App\Core\DatabaseService;
use PDO;

class TransactionRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DatabaseService::getConnection();
    }

    public function create(float $value, int $payerId, int $payeeId): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO transactions (value, payer_id, payee_id) VALUES (?, ?, ?)"
        );
        $stmt->execute([$value, $payerId, $payeeId]);
    }
}
