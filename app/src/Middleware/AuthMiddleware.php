<?php

 namespace App\Middleware;

 use App\Services\AuthenticationService;
 use Exception;

class AuthMiddleware
{
    private AuthenticationService $authService;

    public function __construct(AuthenticationService $authService)
    {
        $this->authService = $authService;
    }

    public function authenticate(string $token): array
    {
        try {
            return $this->authService->validateToken($token);
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}
