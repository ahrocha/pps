<?php

namespace App\Controllers;

use App\Services\AuthenticationService;
use Exception;

class AuthController
{
    private AuthenticationService $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['email'], $input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'email e password são obrigatórios.']);
            return;
        }

        try {
            $token = $this->authService->authenticate($input['email'], $input['password']);
            echo json_encode(['token' => $token]);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
