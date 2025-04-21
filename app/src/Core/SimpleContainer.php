<?php

namespace App\Core;

use App\Controllers\TransferController;
use App\Controllers\HealthController;
use App\Repositories\UserRepository;
use App\Services\AuthorizationService;
use App\Services\HealthService;
use App\Services\TransferService;
use App\Services\NotificationQueueService;
use App\Repositories\WalletRepository;
use App\Repositories\TransactionRepository;
use App\Handlers\EnqueueTransferNotificationHandler;
use App\Handlers\ValidateBusinessRulesHandler;
use App\Handlers\ExecuteTransactionHandler;
use App\Validators\TransferValidator;
use App\Queue\MessagePublisherInterface;
use App\Queue\RabbitMQPublisher;
use PDO;

class SimpleContainer implements ContainerInterface
{
    private array $services = [];

    public function __construct()
    {
        $this->services = [
            TransferController::class => function (SimpleContainer $container) {
                return new TransferController(
                    $container->get(TransferService::class),
                    $container->get(TransferValidator::class)
                );
            },
            TransferService::class => function (SimpleContainer $container) {
                return new TransferService(
                    $container->get(UserRepository::class),
                    $container->get(EnqueueTransferNotificationHandler::class),
                    $container->get(ValidateBusinessRulesHandler::class),
                    $container->get(ExecuteTransactionHandler::class),
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
                    $container->get(AuthorizationService::class),
                    $container->get(PDO::class),
                    $container->get(WalletRepository::class),
                    $container->get(TransactionRepository::class)
                );
            },
            WalletRepository::class => function (SimpleContainer $container) {
                return new WalletRepository();
            },
            TransactionRepository::class => function (SimpleContainer $container) {
                return new TransactionRepository();
            },
            HealthController::class => function (SimpleContainer $container) {
                return new HealthController(
                    $container->get(HealthService::class)
                );
            },
            EnqueueTransferNotificationHandler::class => function (SimpleContainer $container) {
                return new EnqueueTransferNotificationHandler(
                    $container->get(NotificationQueueService::class)
                );
            },
            ValidateBusinessRulesHandler::class => function (SimpleContainer $container) {
                return new ValidateBusinessRulesHandler();
            },
            TransferValidator::class => function (SimpleContainer $container) {
                return new TransferValidator();
            },
            NotificationQueueService::class => function (SimpleContainer $container) {
                return new NotificationQueueService(
                    $container->get(MessagePublisherInterface::class)
                );
            },
            MessagePublisherInterface::class => function (SimpleContainer $container) {
                return new RabbitMQPublisher();
            },
            HealthService::class => function ($container) {
                return new HealthService($container->get(PDO::class));
            },
            PDO::class => function () {
                return DatabaseService::getConnection();
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
