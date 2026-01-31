<?php

namespace App\Services\Transfer\Rules;

class AccountsExistRule extends TransferRule
{
    protected function check(array $data): void
    {
        if (!$data['sourceAccount']) {
            throw new \Exception('Source account not found');
        }

        if (!$data['destinationAccount']) {
            throw new \Exception('Destination account not found');
        }
    }
}
