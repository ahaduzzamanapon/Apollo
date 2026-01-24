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

    protected $casts = [
        'age' => 'array',
    ];

    public function reports()
    {
        return $this->hasMany(PatientReport::class);
    }

    public function tests()
    {
        return $this->hasMany(PatientTest::class, 'patient_report_id');
    }

    public function getFormattedAgeAttribute()
    {
        if (is_array($this->age)) {
            $parts = [];
            if (!empty($this->age['years'])) $parts[] = $this->age['years'] . 'Y';
            if (!empty($this->age['months'])) $parts[] = $this->age['months'] . 'M';
            if (!empty($this->age['days'])) $parts[] = $this->age['days'] . 'D';
            return implode(' ', $parts) ?: '0D';
        }
        return $this->age . ' ' . $this->age_unit;
    }

    public function getLongFormattedAgeAttribute()
    {
        if (is_array($this->age)) {
            $parts = [];
            if (!empty($this->age['years'])) $parts[] = $this->age['years'] . 'YRS';
            if (!empty($this->age['months'])) $parts[] = $this->age['months'] . 'MON';
            if (!empty($this->age['days'])) $parts[] = $this->age['days'] . 'DAYS';
            return implode(' ', $parts) ?: '0DAYS';
        }
        return $this->age . ' ' . $this->age_unit;
    }


}
