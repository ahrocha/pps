<?php

namespace App\Services;

use App\Core\DatabaseService;
use Exception;

class HealthService
{
    public function check(): array
    {
        $status = 'ok';
        $dbOk = false;
        $rabbitOk = false;
        $errors = [];

        try {
            $pdo = DatabaseService::getConnection();
            $pdo->query('SELECT 1');
            $dbOk = true;
        } catch (Exception $e) {
            $status = 'degraded';
            $errors['db'] = $e->getMessage();
        }

        try {
            $host = getenv('RABBITMQ_HOST') ?: 'pps_rabbitmq';
            $port = 5672;
            $conn = @fsockopen($host, $port, $errno, $errstr, 1.5);

            if ($conn) {
                fclose($conn);
                $rabbitOk = true;
            } else {
                throw new Exception("Falha na conexão com RabbitMQ: $errstr");
            }
        } catch (Exception $e) {
            $status = 'degraded';
            $errors['rabbitmq'] = $e->getMessage();
        }

        $result = [
            'status' => $status,
            'db' => $dbOk,
            'rabbitmq' => $rabbitOk,
        ];

        if (!empty($errors)) {
            $result['errors'] = $errors;
        }

        return $result;
    }
}
