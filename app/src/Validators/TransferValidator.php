<?php

namespace App\Validators;

use Exception;

class TransferValidator
{
    public function validate(array $data): void
    {
        if (!isset($data['value'], $data['payer'], $data['payee'])) {
            throw new Exception("Campos obrigatórios ausentes: 'value', 'payer' e 'payee'.");
        }

        if (!is_numeric($data['value']) || $data['value'] <= 0) {
            throw new Exception("O valor da transferência deve ser um número maior que zero.");
        }

        if (!is_numeric($data['payer']) || !is_numeric($data['payee'])) {
            throw new Exception("IDs do pagador e recebedor devem ser numéricos.");
        }

        if ((int)$data['payer'] === (int)$data['payee']) {
            throw new Exception("Pagador e recebedor não podem ser o mesmo usuário.");
        }
    }
}
