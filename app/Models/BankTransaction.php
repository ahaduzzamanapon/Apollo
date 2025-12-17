<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankTransaction extends Model
{
    protected $fillable = [
        'bank_id',
        'trans_type',
        'amount',
        'trans_date',
        'note',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
