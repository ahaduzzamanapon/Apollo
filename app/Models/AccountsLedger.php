<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountsLedger extends Model
{
    protected $table = 'accounts_ledgers';
    
    protected $fillable = [
        'name',
        'type',
    ];

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'ledger_id');
    }
}
