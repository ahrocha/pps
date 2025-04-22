<?php

 namespace App\Services;

 use App\Core\LoggerService;
 use App\Repositories\UserRepository;
 use Exception;

class AuthenticationService
{
    private UserRepository $userRepository;
    private JWTService $jwtService;

    public function __construct(UserRepository $userRepository, JWTService $jwtService)
    {
        $this->userRepository = $userRepository;
        $this->jwtService = $jwtService;
    }

    public function authenticate(string $email, string $password): string
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            LoggerService::getLogger()->warning("[AUTENTICACAO] Falha para o email: {$email}");
            throw new Exception('Credenciais inválidas.');
        }

        $payload = [
            'user_id' => $user['id'],
            'user_type' => $user['type'],
        ];

        LoggerService::getLogger()->info("[AUTENTICACAO] Sucesso para o email: {$email}.");
        return $this->jwtService->encode($payload);
    }

    public function validateToken(string $token): array
    {
        return $this->jwtService->decode($token);
    }
}
