<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'name',
        'nid',
        'mobile',
        'age',
        'age_unit',
        'dob',
        'gender',
    ];

    public function reports()
    {
        return $this->hasMany(PatientReport::class);
    }
}
