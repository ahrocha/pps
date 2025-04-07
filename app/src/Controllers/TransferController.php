<?php

namespace App\Controllers;

use App\Facades\TransferFacade;
use Exception;

class TransferController
{
    public function transfer(): void
    {
        // $input = json_decode(file_get_contents('php://input'), true);

        $input = [
            'value' => 100,
            'payer' => 1,
            'payee' => 2,
        ];

        try {
            (new TransferFacade())->execute($input);
        
            echo json_encode(['status' => 'Transferência realizada com sucesso']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
