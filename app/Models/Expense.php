<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'ledger_id',
        'description',
        'amount',
    ];

    public function ledger()
    {
        return $this->belongsTo(AccountsLedger::class, 'ledger_id');
    }
}
