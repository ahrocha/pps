<?php

namespace App\Controllers;

use App\Services\HealthService;

class HealthController
{
    private HealthService $healthService;

    public function __construct(HealthService $healthService)
    {
        $this->healthService = $healthService;
    }

    public function check(): void
    {
        header('Content-Type: application/json');

        $result = $this->healthService->check();

        http_response_code($result['status'] === 'ok' ? 200 : 500);
        echo json_encode($result);
    }
}
