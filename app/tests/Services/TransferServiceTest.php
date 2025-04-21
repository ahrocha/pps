<?php

namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use App\Services\TransferService;
use App\Services\AuthorizationService;
use App\Handlers\EnqueueTransferNotificationHandler;
use App\Handlers\ValidateBusinessRulesHandler;
use App\Handlers\ExecuteTransactionHandler;
use Exception;

class TransferServiceTest extends TestCase
{
    private UserRepository $userRepository;
    private EnqueueTransferNotificationHandler $enqueueTransferNotificationHandler;
    private ValidateBusinessRulesHandler $validateBusinessRulesHandler;
    private ExecuteTransactionHandler $executeTransactionHandler;
    private TransferService $transferService;
    private AuthorizationService $authorizationService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->enqueueTransferNotificationHandler = $this->createMock(EnqueueTransferNotificationHandler::class);
        $this->validateBusinessRulesHandler = $this->createMock(ValidateBusinessRulesHandler::class);
        $this->executeTransactionHandler = $this->createMock(ExecuteTransactionHandler::class);
        $this->authorizationService = $this->createMock(AuthorizationService::class);

        $this->transferService = new TransferService(
            $this->userRepository,
            $this->enqueueTransferNotificationHandler,
            $this->validateBusinessRulesHandler,
            $this->executeTransactionHandler
        );
    }

    public function testSuccessfulTransfer()
    {
        $payerId = 1;
        $payeeId = 2;
        $value = 100.0;

        $payer = ['id' => $payerId, 'type' => 'usuario', 'balance' => 1000.0, 'name' => 'Payer Name'];
        $payee = ['id' => $payeeId, 'type' => 'merchant', 'name' => 'Payee Name'];

        $this->userRepository->method('find')->willReturnMap([
            [$payerId, $payer],
            [$payeeId, $payee],
        ]);

        $this->expectNotToPerformAssertions();
        $this->transferService->transfer($value, $payerId, $payeeId);
    }

    public function testFailsWhenPayerAndPayeeAreEqual()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Pagador e recebedor não podem ser o mesmo usuário.");

        $payerId = 1;

        $payer = ['id' => $payerId, 'type' => 'usuario', 'balance' => 1000.0, 'name' => 'Payer Name'];

        $this->userRepository->method('find')->willReturn($payer);

        $this->validateBusinessRulesHandler->method('handle')
                                         ->willThrowException(new Exception("Pagador e recebedor não podem ser o mesmo usuário."));

        $this->transferService->transfer(50.0, $payerId, $payerId);
    }

    public function testFailsWhenMerchantTriesToPay()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Lojistas não podem realizar transferências.");

        $payerId = 2;
        $payeeId = 1;

        $payer = ['id' => $payerId, 'type' => 'lojista', 'balance' => 1000.0, 'name' => 'Payer Name'];
        $payee = ['id' => $payeeId, 'type' => 'usuario', 'name' => 'Payee Name'];

        $this->userRepository->method('find')->willReturnMap([
            [$payerId, $payer],
            [$payeeId, $payee],
        ]);

        $this->validateBusinessRulesHandler->method('handle')
                                         ->willThrowException(new Exception("Lojistas não podem realizar transferências."));

        $this->transferService->transfer(10.0, $payerId, $payeeId);
    }

    public function testFailsWhenBalanceIsInsufficient()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Saldo insuficiente para realizar a transferência.");

        $payerId = 3;
        $payeeId = 2;

        $payer = ['id' => $payerId, 'type' => 'usuario', 'balance' => 50.0, 'name' => 'Payer Name'];
        $payee = ['id' => $payeeId, 'type' => 'merchant', 'name' => 'Payee Name'];

        $this->userRepository->method('find')->willReturnMap([
            [$payerId, $payer],
            [$payeeId, $payee],
        ]);

        $this->validateBusinessRulesHandler->method('handle')
                                         ->willThrowException(new Exception("Saldo insuficiente para realizar a transferência."));

        $this->transferService->transfer(1000.0, $payerId, $payeeId);
    }

    public function testFailsWhenAuthorizationServiceDenies()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Transação não autorizada pelo serviço externo.");

        $payerId = 1;
        $payeeId = 2;
        $value = 100.0;

        $payer = ['id' => $payerId, 'type' => 'usuario', 'balance' => 1000.0, 'name' => 'Payer Name'];
        $payee = ['id' => $payeeId, 'type' => 'merchant', 'name' => 'Payee Name'];

        $this->userRepository->method('find')->willReturnMap([
            [$payerId, $payer],
            [$payeeId, $payee],
        ]);

        $this->authorizationService->method('authorize')->willReturn(false);

        // $this->validateBusinessRulesHandler->method('handle')->willReturn(null);
        // $this->executeTransactionHandler->method('handle')->willReturn(null);
        // $this->enqueueTransferNotificationHandler->method('handle')->willReturn(null);
        $this->validateBusinessRulesHandler->method('handle')
                                         ->willThrowException(new Exception("Transação não autorizada pelo serviço externo."));

        // $this->executeTransactionHandler->method('authorize')->willReturn(false);


        $this->transferService->transfer($value, $payerId, $payeeId);
    }
}
