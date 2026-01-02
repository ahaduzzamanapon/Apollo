<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_code',
        'patient_id',
        'reference_doctor_id',
        'ref_by_someone',
        'report_date',
        'total_amount',
        'discount',
        'final_amount',
        'paid_amount',
        'due_amount',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function referenceDoctor()
    {
        return $this->belongsTo(Doctor::class, 'reference_doctor_id');
    }

    public function tests()
    {
        return $this->hasMany(PatientTest::class);
    }

    public function payments()
    {
        return $this->hasMany(PatientPayment::class);
    }
}
