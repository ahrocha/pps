<?php

namespace App\Handlers;

abstract class AbstractTransferHandler implements TransferHandlerInterface
{
    protected ?TransferHandlerInterface $next = null;

    public function setNext(TransferHandlerInterface $handler): TransferHandlerInterface
    {
        $this->next = $handler;
        return $handler;
    }

    protected function next(array $payer, array $payee, float $value): void
    {
        if ($this->next) {
            $this->next->handle($payer, $payee, $value);
        }
    }
}
