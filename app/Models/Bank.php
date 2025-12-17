<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = ['name', 'account_no'];

    public function transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }
}
