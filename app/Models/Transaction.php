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
