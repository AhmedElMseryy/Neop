<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TransferService
{
    protected AccountRepositoryInterface $accountRepository;
    protected TransactionRepositoryInterface $transactionRepository;

    public function __construct(
        AccountRepositoryInterface $accountRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->transactionRepository = $transactionRepository;
    }

    #-------------------------------------------------------TRANSFER
    public function transfer(int $sourceAccountId, int $destinationAccountId, float $amount, ?string $description = null): Transaction
    {
        // Start Database Transaction
        return DB::transaction(function () use ($sourceAccountId, $destinationAccountId, $amount, $description) {

            // 1. (Pessimistic Locking)
            $sourceAccount = Account::where('id', $sourceAccountId)->lockForUpdate()->first();
            $destinationAccount = Account::where('id', $destinationAccountId)->lockForUpdate()->first();

            // 2. Validate Accounts Exist
            if (!$sourceAccount) {
                throw new \Exception('Source account not found');
            }

            if (!$destinationAccount) {
                throw new \Exception('Destination account not found');
            }

            // 3. Validate Same Account Transfer
            if ($sourceAccountId === $destinationAccountId) {
                throw new \Exception('Cannot transfer to the same account');
            }

            // 4. Validate Account Status
            if ($sourceAccount->status !== 'active') {
                throw new \Exception('Source account is not active');
            }

            if ($destinationAccount->status !== 'active') {
                throw new \Exception('Destination account is not active');
            }

            // 5. Validate Currency Match
            if ($sourceAccount->currency !== $destinationAccount->currency) {
                throw new \Exception('Currency mismatch. Both accounts must have the same currency');
            }

            // 6. Validate Amount
            if ($amount <= 0) {
                throw new \Exception('Transfer amount must be greater than zero');
            }

            // 7. Validate Sufficient Balance
            if ($sourceAccount->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            // 8. Create Transaction Record
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
                'source_balance_after' => 0, // Will update after transfer
                'destination_balance_after' => 0, // Will update after transfer
            ]);

            try {
                // 9. Perform Transfer
                $sourceAccount->balance -= $amount;
                $destinationAccount->balance += $amount;

                // 10. Save Updated Balances
                $sourceAccount->save();
                $destinationAccount->save();

                // 11. Update Transaction with new balances
                $transaction->update([
                    'source_balance_after' => $sourceAccount->balance,
                    'destination_balance_after' => $destinationAccount->balance,
                    'status' => 'success',
                    'processed_at' => now(),
                ]);

                // 12. Log Success
                Log::info('Transfer successful', [
                    'transaction_reference' => $transaction->transaction_reference,
                    'amount' => $amount,
                    'source_account' => $sourceAccountId,
                    'destination_account' => $destinationAccountId,
                ]);

                return $transaction->fresh(['sourceAccount', 'destinationAccount']);
            } catch (\Exception $e) {
                // Mark transaction as failed
                $transaction->update([
                    'status' => 'failed',
                    'failure_reason' => $e->getMessage(),
                    'processed_at' => now(),
                ]);

                // Log Error
                Log::error('Transfer failed', [
                    'transaction_reference' => $transaction->transaction_reference,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        });
    }

    /**
     * Get transaction by reference
     */
    public function getTransactionByReference(string $reference): ?Transaction
    {
        return $this->transactionRepository->findByReference($reference);
    }

    /**
     * Get account transaction history
     */
    public function getAccountTransactionHistory(int $accountId)
    {
        return $this->transactionRepository->getAccountTransactions($accountId)
            ->load(['sourceAccount', 'destinationAccount']);
    }

    /**
     * Generate unique transaction reference
     */
    protected function generateTransactionReference(): string
    {
        do {
            $reference = 'TXN' . date('Ymd') . strtoupper(Str::random(8));
        } while ($this->transactionRepository->findByReference($reference));

        return $reference;
    }

    /**
     * Validate transfer before execution
     */
    public function validateTransfer(int $sourceAccountId, int $destinationAccountId, float $amount): array
    {
        $errors = [];

        // Check source account
        $sourceAccount = $this->accountRepository->find($sourceAccountId);
        if (!$sourceAccount) {
            $errors[] = 'Source account not found';
        }

        // Check destination account
        $destinationAccount = $this->accountRepository->find($destinationAccountId);
        if (!$destinationAccount) {
            $errors[] = 'Destination account not found';
        }

        if (empty($errors)) {
            // Same account check
            if ($sourceAccountId === $destinationAccountId) {
                $errors[] = 'Cannot transfer to the same account';
            }

            // Status check
            if ($sourceAccount->status !== 'active') {
                $errors[] = 'Source account is not active';
            }

            if ($destinationAccount->status !== 'active') {
                $errors[] = 'Destination account is not active';
            }

            // Currency check
            if ($sourceAccount->currency !== $destinationAccount->currency) {
                $errors[] = 'Currency mismatch';
            }

            // Amount check
            if ($amount <= 0) {
                $errors[] = 'Amount must be greater than zero';
            }

            // Balance check
            if ($sourceAccount->balance < $amount) {
                $errors[] = 'Insufficient balance';
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
