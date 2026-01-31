<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Repositories\Contracts\AccountRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\Transfer\TransferValidator;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class TransferService
{
    public function __construct(
        protected AccountRepositoryInterface     $accountRepository,
        protected TransactionRepositoryInterface $transactionRepository,
        protected TransferValidator              $transferValidator
    )
    {
    }

    #-------------------------------------------------------TRANSFER
    public function transfer(
        int     $sourceAccountId,
        int     $destinationAccountId,
        float   $amount,
        ?string $description = null
    ): Transaction
    {
        $transaction = $this->transactionRepository->create([
            'transaction_reference' => $this->generateTransactionReference(),
            'source_account_id' => $sourceAccountId,
            'destination_account_id' => $destinationAccountId,
            'amount' => $amount,
            'status' => 'pending',
            'description' => $description,
        ]);

        try {
            return DB::transaction(function () use (
                $sourceAccountId,
                $destinationAccountId,
                $amount,
                $transaction
            ) {


                $sourceAccount = Account::where('id', $sourceAccountId)
                    ->lockForUpdate()
                    ->first();

                $destinationAccount = Account::where('id', $destinationAccountId)
                    ->lockForUpdate()
                    ->first();


                $this->transferValidator->validate(
                    $sourceAccount,
                    $destinationAccount,
                    $amount
                );


                $transaction->update([
                    'currency' => $sourceAccount->currency,
                    'source_balance_before' => $sourceAccount->balance,
                    'destination_balance_before' => $destinationAccount->balance,
                ]);


                $sourceAccount->balance -= $amount;
                $destinationAccount->balance += $amount;

                $sourceAccount->save();
                $destinationAccount->save();


                $transaction->update([
                    'status' => 'success',
                    'source_balance_after' => $sourceAccount->balance,
                    'destination_balance_after' => $destinationAccount->balance,
                    'processed_at' => now(),
                ]);

                return $transaction->fresh([
                    'sourceAccount',
                    'destinationAccount'
                ]);
            });

        } catch (TransferException $e) {
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            throw $e;

        } catch (Throwable $e) {
            $transaction->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
                'processed_at' => now(),
            ]);

            throw $e;
        }
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
