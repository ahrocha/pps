<?php
// src/Controllers/PayController.php
namespace App\Controllers;

use Exception;
use App\Core\LoggerService;
use App\Services\TransferService;
use App\Validators\TransferValidator;

class PayController
{
    private TransferService $transferService;
    private TransferValidator $transferValidator;

    public function __construct(TransferService $transferService, TransferValidator $transferValidator)
    {
        $this->transferService = $transferService;
        $this->transferValidator = $transferValidator;
    }

    public function pay(array $user): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        try {
            $input['payer'] = intval($user['user_id']);
            $this->transferValidator->validate($input);
            $value = floatval($input['value']);
            $payer = $user['user_id'];
            $payee = intval($input['payee']);
            $this->transferService->transfer($value, $payer, $payee);
            echo json_encode(['status' => 'Pagamento realizado com sucesso']);
        } catch (Exception $e) {
            http_response_code(400);
            LoggerService::getLogger()->error('Erro na execução do pagamento: ' . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
