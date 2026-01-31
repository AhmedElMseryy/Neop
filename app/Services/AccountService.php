<?php

namespace App\Services;

use App\Models\Account;
use App\Repositories\Contracts\AccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class AccountService
{
    protected AccountRepositoryInterface $accountRepository;

    public function __construct(AccountRepositoryInterface $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function getAllAccounts(): Collection
    {
        return $this->accountRepository->all();
    }

    public function getAccountById(int $id): ?Account
    {
        return $this->accountRepository->find($id);
    }

    public function getAccountByNumber(string $accountNumber): ?Account
    {
        return $this->accountRepository->findByAccountNumber($accountNumber);
    }

    public function getUserAccounts(int $userId): Collection
    {
        return $this->accountRepository->getUserAccounts($userId);
    }

    public function getActiveUserAccounts(int $userId): Collection
    {
        return $this->accountRepository->getActiveAccounts($userId);
    }

    public function createAccount(array $data): Account
    {
        // Generate unique account number if not provided
        if (!isset($data['account_number'])) {
            $data['account_number'] = $this->generateAccountNumber();
        }

        // Set default balance if not provided
        if (!isset($data['balance'])) {
            $data['balance'] = 0.00;
        }

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        return $this->accountRepository->create($data);
    }

    public function updateAccount(int $id, array $data): bool
    {
        return $this->accountRepository->update($id, $data);
    }

    public function deleteAccount(int $id): bool
    {
        return $this->accountRepository->delete($id);
    }

    public function activateAccount(int $id): bool
    {
        return $this->updateAccount($id, ['status' => 'active']);
    }

    public function closeAccount(int $id): bool
    {
        $account = $this->getAccountById($id);

        if (!$account) {
            return false;
        }

        // Can only close accounts with zero balance
        if ($account->balance != 0) {
            throw new \Exception('Cannot close account with non-zero balance');
        }

        return $this->updateAccount($id, ['status' => 'closed']);
    }

    protected function generateAccountNumber(): string
    {
        do {
            // Generate account number: ACC + timestamp + random
            $accountNumber = 'ACC' . time() . strtoupper(Str::random(6));
        } while ($this->accountRepository->findByAccountNumber($accountNumber));

        return $accountNumber;
    }

    public function accountNumberExists(string $accountNumber): bool
    {
        return $this->accountRepository->findByAccountNumber($accountNumber) !== null;
    }
}
