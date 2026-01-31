<?php

namespace App\Services\Transfer\Rules;

class CurrencyMatchRule extends TransferRule
{
    protected function check(array $data): void
    {
        if ($data['sourceAccount']->currency !== $data['destinationAccount']->currency) {
            throw new \Exception('Currency mismatch. Both accounts must have the same currency');
        }
    }
}
