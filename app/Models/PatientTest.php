<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientTest extends Model
{
    protected $fillable = [
        'patient_report_id',
        'report_category_id',
        'price',
        'commission_amount',
    ];

    public function report()
    {
        return $this->belongsTo(PatientReport::class, 'patient_report_id');
    }

    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'report_category_id');
    }
}
