<?php

namespace App\Facades;

use App\Validators\TransferValidator;
use App\Services\TransferService;

class TransferFacade
{
    private TransferValidator $validator;
    private TransferService $transferService;

    public function __construct(TransferValidator $validator, TransferService $transferService)
    {
        $this->validator = $validator;
        $this->transferService = $transferService;
    }

    public function execute(array $data): void
    {
        $this->validator->validate($data);

        $value = floatval($data['value']);
        $payer = intval($data['payer']);
        $payee = intval($data['payee']);

        $this->transferService->transfer($value, $payer, $payee);
    }
}
