<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'bank_name',
        'account_no',
        'type',
        'amount',
    ];
}
