<?php

namespace App\Services\Transfer\Rules;

class SameAccountRule extends TransferRule
{
    protected function check(array $data): void
    {
        if ($data['sourceAccount']->id === $data['destinationAccount']->id) {
            throw new \Exception('Cannot transfer to the same account');
        }
    }
}
