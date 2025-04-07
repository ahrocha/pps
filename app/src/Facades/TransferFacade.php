<?php

namespace App\Facades;

use App\Validators\TransferValidator;
use App\Services\TransferService;

class TransferFacade
{
    public function execute(array $data): void
    {
        (new TransferValidator())->validate($data);

        $value = floatval($data['value']);
        $payer = intval($data['payer']);
        $payee = intval($data['payee']);

        (new TransferService())->transfer($value, $payer, $payee);
    }
}
