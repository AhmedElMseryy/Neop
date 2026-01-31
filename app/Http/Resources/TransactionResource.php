<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_reference' => $this->transaction_reference,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'description' => $this->description,
            'source_account' => new AccountResource($this->sourceAccount),
            'destination_account' => new AccountResource($this->destinationAccount),
            'balance_before' => [
                'source' => $this->source_balance_before,
                'destination' => $this->destination_balance_before,
            ],
            'balance_after' => [
                'source' => $this->source_balance_after,
                'destination' => $this->destination_balance_after,
            ],
            'failure_reason' => $this->failure_reason,
            'created_at' => $this->created_at?->toDateTimeString(),
            'processed_at' => $this->processed_at?->toDateTimeString(),
        ];
    }
}
