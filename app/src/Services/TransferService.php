<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Handlers\EnqueueTransferNotificationHandler;
use App\Handlers\ValidateBusinessRulesHandler;
use App\Handlers\ExecuteTransactionHandler;

class TransferService
{
    private UserRepository $userRepository;
    private EnqueueTransferNotificationHandler $enqueueTransferNotificationHandler;
    private ValidateBusinessRulesHandler $validateBusinessRulesHandler;
    private ExecuteTransactionHandler $executeTransactionHandler;

    public function __construct(
        UserRepository $userRepository,
        EnqueueTransferNotificationHandler $enqueueTransferNotificationHandler,
        ValidateBusinessRulesHandler $validateBusinessRulesHandler,
        ExecuteTransactionHandler $executeTransactionHandler
    ) {
        $this->userRepository = $userRepository;
        $this->enqueueTransferNotificationHandler = $enqueueTransferNotificationHandler;
        $this->validateBusinessRulesHandler = $validateBusinessRulesHandler;
        $this->executeTransactionHandler = $executeTransactionHandler;
    }

    public function transfer(float $value, int $payerId, int $payeeId): void
    {
        $payer = $this->userRepository->find($payerId);
        $payee = $this->userRepository->find($payeeId);

        $chain = $this->validateBusinessRulesHandler;
        $chain->setNext($this->executeTransactionHandler)
              ->setNext($this->enqueueTransferNotificationHandler);

        $chain->handle($payer, $payee, $value);
    }
}
