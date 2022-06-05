<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'bank_code',
        'bank_tran_no',
        'card_type',
        'order_info',
        'pay_date',
        'response_code',
        'transaction_no',
        'transaction_status',
        'txn_ref'
    ];
}
