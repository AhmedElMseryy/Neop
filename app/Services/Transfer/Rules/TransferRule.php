<?php

namespace App\Services\Transfer\Rules;

abstract class TransferRule
{
    protected ?TransferRule $next = null;

    public function setNext(TransferRule $rule): TransferRule
    {
        $this->next = $rule;
        return $rule;
    }

    public function handle(array $data): void
    {
        $this->check($data);

        if ($this->next) {
            $this->next->handle($data);
        }
    }

    abstract protected function check(array $data): void;
}
