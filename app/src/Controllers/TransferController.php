<?php

namespace App\Controllers;

use App\Facades\TransferFacade;
use Exception;
use App\Core\LoggerService;

class TransferController
{
    public function transfer(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        try {
            (new TransferFacade())->execute($input);
            echo json_encode(['status' => 'Transferência realizada com sucesso']);
        } catch (Exception $e) {
            http_response_code(400);
            LoggerService::getLogger()->error('Erro na execução da transação: ' . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
