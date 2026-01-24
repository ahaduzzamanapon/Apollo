<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientTest extends Model
{
    protected $fillable = [
        'patient_report_id',
        'report_category_id',
        'price',
        'discount',
        'discount_percent',
        'commission_amount',
        'approval_status',
        'approved_amount',
        'approved_at',
    ];

    public function report()
    {
        return $this->belongsTo(PatientReport::class, 'patient_report_id');
    }

    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'report_category_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class,'id');
    }

    public function result()
    {
        return $this->hasOne(PatientTestResult::class, 'test_id');
    }
}
