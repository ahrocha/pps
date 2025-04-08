<?php

namespace App\Services;

use App\Core\LoggerService;

class AuthorizationService
{
    public function authorize(): bool
    {
        $url = 'https://util.devi.tools/api/v2/authorize';

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 3,
            ]
        ]);

        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            LoggerService::getLogger()->warning("[AUTORIZADOR] Falha na comunicação com o serviço.");
            return false;
        }

        $result = json_decode($response, true);
        LoggerService::getLogger()->debug("[AUTORIZADOR] Resposta do serviço: " . json_encode($result));
        $autorizado = isset($result['data']) &&
                        isset($result['data']['authorization'])
                        && $result['data']['authorization'] === true;

        LoggerService::getLogger()->info("[AUTORIZADOR] Resposta: " . json_encode($result));

        return $autorizado;
    }
}
