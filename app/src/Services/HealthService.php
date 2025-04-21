<?php

namespace App\Services;

use App\Core\DatabaseService;
use Exception;
use PDO;

class HealthService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function check(): array
    {
        $status = 'ok';
        $dbOk = false;
        $rabbitOk = false;
        $errors = [];

        try {
            $this->pdo->query('SELECT 1');
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