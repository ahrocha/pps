<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTService
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function encode(array $payload): string
    {
        $now = new \DateTimeImmutable();
        $payload = array_merge($payload, [
            'iat' => $now->getTimestamp(),
            'exp' => $now->modify('+1 hour')->getTimestamp(),
        ]);

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function decode(string $token): array
    {
        try {
            return (array) JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (Exception $e) {
            throw new Exception('Token inválido: ' . $e->getMessage());
        }
    }
}
