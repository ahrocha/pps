<?php

namespace Tests\Facades;

use App\Facades\TransferFacade;
use PHPUnit\Framework\TestCase;
use Exception;

class TransferFacadeTest extends TestCase
{
    public function testSuccessfulTransfer()
    {
        $facade = new TransferFacade();

        $input = [
            'value' => 100,
            'payer' => 1,
            'payee' => 2,
        ];

        $this->expectNotToPerformAssertions();

        $facade->execute($input);
    }

    public function testTransferFailsWhenPayerAndPayeeAreEqual()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Pagador e recebedor não podem ser o mesmo usuário.");

        $facade = new TransferFacade();

        $facade->execute([
            'value' => 50,
            'payer' => 1,
            'payee' => 1,
        ]);
    }

    public function testFailsWhenBalanceIsInsufficient()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Saldo insuficiente para realizar a transferência.");

        $facade = new TransferFacade();

        $facade->execute([
            'value' => 999999,
            'payer' => 3, // usuário com R$250,00
            'payee' => 2,
        ]);
    }

    public function testFailsWhenMerchantTriesToPay()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Lojistas não podem realizar transferências.");

        $facade = new TransferFacade();

        $facade->execute([
            'value' => 10,
            'payer' => 2, // lojista
            'payee' => 1,
        ]);
    }
}
