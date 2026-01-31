<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_reference',
        'source_account_id',
        'destination_account_id',
        'amount',
        'currency',
        'status',
        'description',
    ];

    protected $guarded = [
        'source_balance_before',
        'source_balance_after',
        'destination_balance_before',
        'destination_balance_after',
    ];

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

}
