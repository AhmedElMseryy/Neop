<?php

namespace App\Services\Transfer;

use App\Models\Account;
use App\Services\Transfer\Rules\{
    AccountsExistRule,
    SameAccountRule,
    AccountStatusRule,
    CurrencyMatchRule,
    ValidAmountRule,
    SufficientBalanceRule
};

class TransferValidator
{
    public function validate(Account $source, Account $destination, float $amount): void
    {
        $data = [
            'sourceAccount' => $source,
            'destinationAccount' => $destination,
            'amount' => $amount,
        ];

        $chain = new AccountsExistRule();
        $chain
            ->setNext(new SameAccountRule())
            ->setNext(new AccountStatusRule())
            ->setNext(new CurrencyMatchRule())
            ->setNext(new ValidAmountRule())
            ->setNext(new SufficientBalanceRule());

        $chain->handle($data);
    }
}
