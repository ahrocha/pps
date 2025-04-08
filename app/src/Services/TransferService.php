<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Handlers\EnqueueTransferNotificationHandler;
use App\Handlers\ValidateBusinessRulesHandler;
use App\Handlers\ExecuteTransactionHandler;

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
              ->setNext(new EnqueueTransferNotificationHandler());
    
        $chain->handle($payer, $payee, $value);
    }
}
