<?php

namespace App\Core;

use App\Controllers\TransferController;
use App\Controllers\HealthController;
use App\Facades\TransferFacade;
use App\Services\TransferService;
use App\Repositories\UserRepository;
use App\Services\AuthorizationService;
use App\Handlers\ExecuteTransactionHandler;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;

class SimpleContainer implements ContainerInterface
{
    private array $services = [];

    public function __construct()
    {
        $this->services = [
            TransferController::class => function (SimpleContainer $container) {
                return new TransferController($container->get(TransferFacade::class));
            },
            TransferFacade::class => function (SimpleContainer $container) {
                return new TransferFacade();
            },
            TransferService::class => function (SimpleContainer $container) {
                return new TransferService(
                    $container->get(UserRepository::class),
                    $container->get(AuthorizationService::class)
                );
            },
            UserRepository::class => function (SimpleContainer $container) {
                return new UserRepository();
            },
            AuthorizationService::class => function (SimpleContainer $container) {
                return new AuthorizationService();
            },
            ExecuteTransactionHandler::class => function (SimpleContainer $container) {
                return new ExecuteTransactionHandler(
                    $container->get(AuthorizationService::class)
                );
            },
            WalletRepository::class => function (SimpleContainer $container) {
                return new WalletRepository();
            },
            TransactionRepository::class => function (SimpleContainer $container) {
                return new TransactionRepository();
            },
            HealthController::class => function (SimpleContainer $container) {
                return new HealthController();
            },
        ];
    }

    public function get(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new \Exception("'{$id}' não encontrado no contêiner.");
        }

        $factory = $this->services[$id];
        return $factory($this);
    }
}