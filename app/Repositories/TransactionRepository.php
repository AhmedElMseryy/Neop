<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    protected Transaction $model;

    public function __construct(Transaction $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['sourceAccount', 'destinationAccount'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find(int $id): ?Transaction
    {
        return $this->model->with(['sourceAccount', 'destinationAccount'])->find($id);
    }

    public function findByReference(string $reference): ?Transaction
    {
        return $this->model->with(['sourceAccount', 'destinationAccount'])
            ->where('transaction_reference', $reference)
            ->first();
    }

    public function create(array $data): Transaction
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $transaction = $this->find($id);

        if (!$transaction) {
            return false;
        }

        return $transaction->update($data);
    }

    public function getAccountTransactions(int $accountId): Collection
    {
        return $this->model->forAccount($accountId)
            ->with(['sourceAccount', 'destinationAccount'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getSuccessfulTransactions(int $accountId): Collection
    {
        return $this->model->forAccount($accountId)
            ->successful()
            ->with(['sourceAccount', 'destinationAccount'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFailedTransactions(int $accountId): Collection
    {
        return $this->model->forAccount($accountId)
            ->failed()
            ->with(['sourceAccount', 'destinationAccount'])
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
