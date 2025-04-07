<?php

namespace App\Repositories;

use Exception;

class UserRepository
{
    private array $mockUsers = [
        1 => [
            'id' => 1,
            'name' => 'Andrey Usuário',
            'type' => 'user',
            'balance' => 1000.00,
        ],
        2 => [
            'id' => 2,
            'name' => 'Lojista XPTO',
            'type' => 'merchant',
            'balance' => 0.00,
        ],
        3 => [
            'id' => 3,
            'name' => 'João Cliente',
            'type' => 'user',
            'balance' => 250.00,
        ],
    ];

    public function find(int $id): array
    {
        if (!isset($this->mockUsers[$id])) {
            throw new Exception("Usuário $id não encontrado.");
        }

        return $this->mockUsers[$id];
    }
}
