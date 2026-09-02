<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_no',
        'trans_date',
        'account_code',
        'description',
        'debit',
        'credit',
        'ref_type',
        'ref_id',
    ];

    protected $casts = [
        'trans_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(CoaAccount::class, 'account_code', 'code');
    }
}
