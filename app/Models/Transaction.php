<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $guarded = [];

    #-------------------------------------------------------CASTS
    protected $casts = [
        'amount' => 'decimal:2',
        'source_balance_before' => 'decimal:2',
        'source_balance_after' => 'decimal:2',
        'destination_balance_before' => 'decimal:2',
        'destination_balance_after' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    #-------------------------------------------------------RELATIONSHIPS
    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'source_account_id');
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }

    #-------------------------------------------------------SCOPES
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForAccount($query, int $accountId)
    {
        return $query->where(function ($q) use ($accountId) {
            $q->where('source_account_id', $accountId)
                ->orWhere('destination_account_id', $accountId);
        });
    }

    #-------------------------------------------------------HELPER METHODS
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function markAsSuccess(): void
    {
        $this->update([
            'status' => 'success',
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'processed_at' => now(),
        ]);
    }
}
