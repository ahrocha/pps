<?php

namespace App\Services;

use App\Core\DatabaseService;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use App\Handlers\ValidateBusinessRulesHandler;
use App\Handlers\ExecuteTransactionHandler;
use App\Handlers\SendNotificationHandler;
use Exception;
use PDO;

class TransferService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function transfer(float $value, int $payerId, int $payeeId): void
    {
        $payer = $this->userRepository->find($payerId);
        $payee = $this->userRepository->find($payeeId);

        $chain = new ValidateBusinessRulesHandler();
        $chain->setNext(new ExecuteTransactionHandler())
              ->setNext(new SendNotificationHandler());
    
        $chain->handle($payer, $payee, $value);
    }
}
