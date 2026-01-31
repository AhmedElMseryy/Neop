<?php

namespace App\Repositories\Contracts;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function all(): Collection;
    public function findByReference(string $reference): ?Transaction;
    public function create(array $data): Transaction;
    public function getAccountTransactions(int $accountId): Collection;

}
