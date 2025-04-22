<?php

 namespace App\Services;

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
            throw new Exception('Credenciais inválidas.');
        }

        $payload = [
            'user_id' => $user['id'],
            'user_type' => $user['type'],
        ];

        return $this->jwtService->encode($payload);
    }

    public function validateToken(string $token): array
    {
        return $this->jwtService->decode($token);
    }
}
