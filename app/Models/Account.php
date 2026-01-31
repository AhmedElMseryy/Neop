<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'account_number',
        'currency',
        'balance',
        'status',
    ];

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


}
