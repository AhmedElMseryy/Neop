<?php

namespace App\Services\Transfer\Rules;

class SufficientBalanceRule extends TransferRule
{
    protected function check(array $data): void
    {
        if ($data['sourceAccount']->balance < $data['amount']) {
            throw new \Exception('Insufficient balance');
        }
    }
}
