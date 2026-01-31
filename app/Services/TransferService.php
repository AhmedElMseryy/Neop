<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\Transfer\TransferValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransferService
{
    protected AccountRepositoryInterface $accountRepository;
    protected TransactionRepositoryInterface $transactionRepository;
    protected TransferValidator $transferValidator;

    public function __construct(
        AccountRepositoryInterface $accountRepository,
        TransactionRepositoryInterface $transactionRepository,
        TransferValidator $transferValidator
    ) {
        $this->accountRepository = $accountRepository;
        $this->transactionRepository = $transactionRepository;
        $this->transferValidator = $transferValidator;
    }

    #-------------------------------------------------------TRANSFER
    public function transfer(
        int $sourceAccountId,
        int $destinationAccountId,
        float $amount,
        ?string $description = null
    ): Transaction {
        return DB::transaction(function () use (
            $sourceAccountId,
            $destinationAccountId,
            $amount,
            $description
        ) {

            // 1. Lock accounts (Pessimistic Locking)
            $sourceAccount = Account::where('id', $sourceAccountId)
                ->lockForUpdate()
                ->first();

            $destinationAccount = Account::where('id', $destinationAccountId)
                ->lockForUpdate()
                ->first();

            // 2. Validate transfer using Chain of Responsibility
            $this->transferValidator->validate(
                $sourceAccount,
                $destinationAccount,
                $amount
            );

            // 3. Create transaction (pending)
            $transaction = $this->transactionRepository->create([
                'transaction_reference' => $this->generateTransactionReference(),
                'source_account_id' => $sourceAccountId,
                'destination_account_id' => $destinationAccountId,
                'amount' => $amount,
                'currency' => $sourceAccount->currency,
                'status' => 'pending',
                'description' => $description,
                'source_balance_before' => $sourceAccount->balance,
                'destination_balance_before' => $destinationAccount->balance,
                'source_balance_after' => 0,
                'destination_balance_after' => 0,
            ]);

            try {
                // 4. Perform transfer
                $sourceAccount->balance -= $amount;
                $destinationAccount->balance += $amount;

                $sourceAccount->save();
                $destinationAccount->save();

                // 5. Mark transaction as success
                $transaction->update([
                    'status' => 'success',
                    'source_balance_after' => $sourceAccount->balance,
                    'destination_balance_after' => $destinationAccount->balance,
                    'processed_at' => now(),
                ]);

                Log::info('Transfer successful', [
                    'transaction_reference' => $transaction->transaction_reference,
                    'amount' => $amount,
                    'source_account' => $sourceAccountId,
                    'destination_account' => $destinationAccountId,
                ]);

                return $transaction->fresh([
                    'sourceAccount',
                    'destinationAccount'
                ]);

            } catch (\Exception $e) {

                // 6. Mark transaction as failed
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => $e->getMessage(),
                    'processed_at' => now(),
                ]);

                Log::error('Transfer failed', [
                    'transaction_reference' => $transaction->transaction_reference,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    #-------------------------------------------------------GET BY REFERENCE
    public function getTransactionByReference(string $reference): ?Transaction
    {
        return $this->transactionRepository->findByReference($reference);
    }

    #-------------------------------------------------------ACCOUNT HISTORY
    public function getAccountTransactionHistory(int $accountId)
    {
        return $this->transactionRepository
            ->getAccountTransactions($accountId);
    }

    #-------------------------------------------------------GENERATE REFERENCE
    protected function generateTransactionReference(): string
    {
        do {
            $reference = 'TXN' . now()->format('Ymd') . strtoupper(Str::random(8));
        } while ($this->transactionRepository->findByReference($reference));

        return $reference;
    }
}
