<?php

namespace App\Repositories\Contracts;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Transaction;
    public function findByReference(string $reference): ?Transaction;
    public function create(array $data): Transaction;
    public function update(int $id, array $data): bool;
    public function getAccountTransactions(int $accountId): Collection;
    public function getSuccessfulTransactions(int $accountId): Collection;
    public function getFailedTransactions(int $accountId): Collection;
}
