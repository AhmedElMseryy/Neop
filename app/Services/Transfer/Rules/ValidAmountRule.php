<?php

namespace App\Services\Transfer\Rules;

class ValidAmountRule extends TransferRule
{
    protected function check(array $data): void
    {
        if ($data['amount'] <= 0) {
            throw new \Exception('Transfer amount must be greater than zero');
        }
    }
}
