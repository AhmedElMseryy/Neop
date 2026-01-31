<?php

namespace App\Services\Transfer\Rules;

use App\Exceptions\TransferValidationException;

class SufficientBalanceRule extends TransferRule
{
    protected function check(array $data): void
    {
        if ($data['sourceAccount']->balance < $data['amount']) {
            throw new TransferValidationException('Insufficient balance');
        }
    }
}
