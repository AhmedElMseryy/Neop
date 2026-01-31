<?php

namespace App\Services\Transfer\Rules;

use App\Exceptions\TransferValidationException;

class SameAccountRule extends TransferRule
{
    protected function check(array $data): void
    {
        if (
            $data['sourceAccount']->id ===
            $data['destinationAccount']->id
        ) {
            throw new TransferValidationException(
                'Cannot transfer to the same account'
            );
        }
    }
}
