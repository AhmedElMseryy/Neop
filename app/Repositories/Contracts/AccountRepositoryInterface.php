<?php

namespace App\Repositories\Contracts;

use App\Models\Account;
use Illuminate\Database\Eloquent\Collection;

interface AccountRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Account;
    public function findByAccountNumber(string $accountNumber): ?Account;
    public function create(array $data): Account;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getUserAccounts(int $userId): Collection;
    public function getActiveAccounts(int $userId): Collection;
}
