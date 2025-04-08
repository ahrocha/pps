<?php

namespace App\Controllers;

use App\Services\HealthService;

class HealthController
{
    public function check(): void
    {
        header('Content-Type: application/json');

        $service = new HealthService();
        $result = $service->check();

        http_response_code($result['status'] === 'ok' ? 200 : 500);
        echo json_encode($result);
    }
}
