<?php

namespace App\Services\Transfer\Rules;

use App\Exceptions\TransferValidationException;

class AccountsExistRule extends TransferRule
{
    protected function check(array $data): void
    {
        if (!$data['sourceAccount']) {
            throw new TransferValidationException('Source account not found');
        }

        if (!$data['destinationAccount']) {
            throw new TransferValidationException('Destination account not found');
        }
    }
}
