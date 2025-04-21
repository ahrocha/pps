<?php

namespace App\Controllers;

use Exception;
use App\Core\LoggerService;
use App\Services\TransferService;
use App\Validators\TransferValidator;

class TransferController
{
    private TransferService $transferService;
    private TransferValidator $transferValidator;

    public function __construct(TransferService $transferService, TransferValidator $transferValidator)
    {
        $this->transferService = $transferService;
        $this->transferValidator = $transferValidator;
    }
    public function transfer(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        try {
            $this->transferValidator->validate($input);
            $value = floatval($input['value']);
            $payer = intval($input['payer']);
            $payee = intval($input['payee']);
            $this->transferService->transfer($value, $payer, $payee);
            echo json_encode(['status' => 'Transferência realizada com sucesso']);
        } catch (Exception $e) {
            http_response_code(400);
            LoggerService::getLogger()->error('Erro na execução da transação: ' . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
