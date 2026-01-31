<?php

namespace App\Services;

use App\Models\Account;
use App\Repositories\Contracts\AccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class AccountService
{

    public function __construct(protected AccountRepositoryInterface $accountRepository)
    {
    }

    public function getAllAccounts(): Collection
    {
        return $this->accountRepository->all();
    }

    public function getAccountById(int $id): ?Account
    {
        return $this->accountRepository->find($id);
    }

    public function getUserAccounts(int $userId): Collection
    {
        return $this->accountRepository->getUserAccounts($userId);
    }

    public function createAccount(array $data): Account
    {
        // Generate unique account number if not provided
        if (!isset($data['account_number'])) {
            $data['account_number'] = $this->generateAccountNumber();
        }

        return $this->accountRepository->create($data);
    }

    public function updateAccount(int $id, array $data): bool
    {
        return $this->accountRepository->update($id, $data);
    }

    public function activateAccount(int $id): bool
    {
        return $this->updateAccount($id, ['status' => 'active']);
    }

    protected function generateAccountNumber(): string
    {
        do {
            // Generate account number: ACC + timestamp + random
            $accountNumber = 'ACC' . time() . strtoupper(Str::random(6));
        } while ($this->accountRepository->findByAccountNumber($accountNumber));

        return $accountNumber;
    }

}
