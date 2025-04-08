<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Handlers\EnqueueTransferNotificationHandler;
use App\Handlers\ValidateBusinessRulesHandler;
use App\Handlers\ExecuteTransactionHandler;
use App\Services\AuthorizationService;

class TransferService
{
    private UserRepository $userRepository;
    private AuthorizationService $authorizationService;

    public function __construct(
        ?UserRepository $userRepository = null,
        ?AuthorizationService $authorizationService = null
    ) {
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->authorizationService = $authorizationService ?? new AuthorizationService();
    }

    public function transfer(float $value, int $payerId, int $payeeId): void
    {
        $payer = $this->userRepository->find($payerId);
        $payee = $this->userRepository->find($payeeId);

        $chain = new ValidateBusinessRulesHandler();
        $chain->setNext(new ExecuteTransactionHandler($this->authorizationService))
              ->setNext(new EnqueueTransferNotificationHandler());

        $chain->handle($payer, $payee, $value);
    }
}
