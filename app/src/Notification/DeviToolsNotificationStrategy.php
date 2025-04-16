<?php

namespace App\Notification;

use App\Core\LoggerService;
use Exception;

class DeviToolsNotificationStrategy implements NotificationStrategyInterface
{
    public function send(NotificationDTO $notification): void
    {
        $url = 'https://util.devi.tools/api/v1/notify';

        $payload = json_encode([
            'user_id' => $notification->userId,
            'message' => $notification->message
        ]);

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => $payload,
                'timeout' => 2,
            ]
        ]);

        $result = @file_get_contents($url, false, $context);

        if ($result === false) {
            throw new Exception("Falha ao enviar notificação para o usuário #{$notification->userId}");
        }

        LoggerService::getLogger()->info("Notificação enviada para usuário #{$notification->userId}");
    }
}
