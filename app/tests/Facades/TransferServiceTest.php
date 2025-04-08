<?php

namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use App\Services\TransferService;
use App\Services\AuthorizationService;
use Exception;

class TransferServiceTest extends TestCase
{
    public function testSuccessfulTransfer()
    {
        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('find')->willReturnOnConsecutiveCalls(
            ['id' => 1, 'type' => 'usuario', 'balance' => 1000.0],
            ['id' => 2, 'type' => 'merchant']
        );

        $walletRepo = $this->createMock(WalletRepository::class);
        $walletRepo->method('getBalance')->willReturn(1000.0);
        $walletRepo->method('updateBalance');

        $transactionRepo = $this->createMock(TransactionRepository::class);
        $transactionRepo->method('create');

        $authorizationService = $this->createMock(AuthorizationService::class);
        $authorizationService->method('authorize')->willReturn(true);

        $service = new TransferService(
            $userRepo,
            $authorizationService,
            $walletRepo,
            $transactionRepo
        );

        $this->expectNotToPerformAssertions();
        $service->transfer(100.0, 1, 2);
    }

    public function testFailsWhenPayerAndPayeeAreEqual()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Pagador e recebedor não podem ser o mesmo usuário.");

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('find')->willReturnOnConsecutiveCalls(
            ['id' => 1, 'type' => 'usuario'],
            ['id' => 1, 'type' => 'usuario']
        );

        $service = new TransferService($userRepo);
        $service->transfer(50.0, 1, 1);
    }

    public function testFailsWhenMerchantTriesToPay()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Lojistas não podem realizar transferências.");

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('find')->willReturnOnConsecutiveCalls(
            ['id' => 2, 'type' => 'lojista', 'balance' => 1000.0],
            ['id' => 1, 'type' => 'usuario']
        );

        $service = new TransferService($userRepo);
        $service->transfer(10.0, 2, 1);
    }

    public function testFailsWhenBalanceIsInsufficient()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Saldo insuficiente para realizar a transferência.");

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('find')->willReturnOnConsecutiveCalls(
            ['id' => 3, 'type' => 'usuario', 'balance' => 1000.0],
            ['id' => 2, 'type' => 'merchant']
        );

        $walletRepo = $this->createMock(WalletRepository::class);
        $walletRepo->method('getBalance')->willReturn(50.0);

        $service = new TransferService(
            $userRepo,
            null,
            $walletRepo
        );

        $service->transfer(10000.0, 3, 2);
    }

    public function testFailsWhenAuthorizationServiceDenies()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Transação não autorizada pelo serviço externo.");

        $userRepo = $this->createMock(UserRepository::class);
        $userRepo->method('find')->willReturnOnConsecutiveCalls(
            ['id' => 1, 'type' => 'usuario', 'balance' => 1000.0],
            ['id' => 2, 'type' => 'merchant']
        );

        $walletRepo = $this->createMock(WalletRepository::class);
        $walletRepo->method('getBalance')->willReturn(1000.0);
        $walletRepo->method('updateBalance');

        $transactionRepo = $this->createMock(TransactionRepository::class);
        $transactionRepo->method('create');

        $authorizationService = $this->createMock(AuthorizationService::class);
        $authorizationService->method('authorize')->willReturn(false);

        $service = new TransferService(
            $userRepo,
            $authorizationService,
            $walletRepo,
            $transactionRepo
        );

        $service->transfer(100.0, 1, 2);
    }
}
