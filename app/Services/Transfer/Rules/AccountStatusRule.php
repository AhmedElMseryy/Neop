<?php

namespace App\Services\Transfer\Rules;

use App\Exceptions\TransferValidationException;

class AccountStatusRule extends TransferRule
{
    protected function check(array $data): void
    {
        if ($data['sourceAccount']->status !== 'active') {
            throw new TransferValidationException(
                'Source account is not active'
            );
        }

        if ($data['destinationAccount']->status !== 'active') {
            throw new TransferValidationException(
                'Destination account is not active'
            );
        }
    }
}
