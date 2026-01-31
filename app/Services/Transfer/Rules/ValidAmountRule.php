<?php

namespace App\Services\Transfer\Rules;

use App\Exceptions\TransferValidationException;

class ValidAmountRule extends TransferRule
{
    protected function check(array $data): void
    {
        if ($data['amount'] <= 0) {
            throw new TransferValidationException(
                'Transfer amount must be greater than zero'
            );
        }
    }
}
