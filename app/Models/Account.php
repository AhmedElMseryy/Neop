<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    #-------------------------------------------------------CASTS
    protected $casts = [
        'balance' => 'decimal:2',
    ];

    #-------------------------------------------------------RELATIONSHIPS
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sentTransactions()
    {
        return $this->hasMany(Transaction::class, 'source_account_id');
    }

    public function receivedTransactions()
    {
        return $this->hasMany(Transaction::class, 'destination_account_id');
    }

    #-------------------------------------------------------HELPER METHODS
    public function transactions()
    {
        return Transaction::where('source_account_id', $this->id)
            ->orWhere('destination_account_id', $this->id)
            ->orderBy('created_at', 'desc');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasSufficientBalance(float $amount): bool
    {
        return $this->balance >= $amount;
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }
}
