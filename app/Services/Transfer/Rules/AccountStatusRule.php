<?php

namespace App\Services\Transfer\Rules;

class AccountStatusRule extends TransferRule
{
    protected function check(array $data): void
    {
        if ($data['sourceAccount']->status !== 'active') {
            throw new \Exception('Source account is not active');
        }

        if ($data['destinationAccount']->status !== 'active') {
            throw new \Exception('Destination account is not active');
        }
    }
}
