<?php

namespace App\Notification;

class NotificationDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $message
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['user_id'], $data['message'])) {
            throw new \InvalidArgumentException('Campos obrigatórios ausentes: user_id e/ou message.');
        }

        if (!is_numeric($data['user_id'])) {
            throw new \InvalidArgumentException('O campo user_id deve ser numérico.');
        }

        $userId = (int) $data['user_id'];
        $message = (string) $data['message'];

        if (mb_strlen($message) > 180) {
            throw new \InvalidArgumentException('A mensagem da notificação não pode exceder 180 caracteres.');
        }

        return new self(
            userId: $userId,
            message: $message
        );
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Erro ao decodificar JSON: ' . json_last_error_msg());
        }

        return self::fromArray($data);
    }
}
