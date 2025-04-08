<?php

namespace App\Handlers;

interface TransferHandlerInterface
{
    public function setNext(TransferHandlerInterface $handler): TransferHandlerInterface;
    public function handle(array $payer, array $payee, float $value): void;
}
