<?php

namespace App\Repositories;

use App\Models\Account;
use App\Repositories\Contracts\AccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AccountRepository implements AccountRepositoryInterface
{
    protected Account $model;

    public function __construct(Account $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('user')->get();
    }

    public function find(int $id): ?Account
    {
        return $this->model->with('user')->find($id);
    }

    public function findByAccountNumber(string $accountNumber): ?Account
    {
        return $this->model->with('user')->where('account_number', $accountNumber)->first();
    }

    public function create(array $data): Account
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $account = $this->find($id);

        if (!$account) {
            return false;
        }

        return $account->update($data);
    }

    public function delete(int $id): bool
    {
        $account = $this->find($id);

        if (!$account) {
            return false;
        }

        return $account->delete();
    }

    public function getUserAccounts(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function getActiveAccounts(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->get();
    }
}
