<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpexExpense extends Model
{
    protected $fillable = [
        'voucher_no',
        'exp_date',
        'category',
        'vendor_name',
        'description',
        'amount',
        'bank_account',
        'approver',
        'status',
    ];

    protected $casts = [
        'exp_date' => 'date',
        'amount' => 'decimal:2',
    ];
}
