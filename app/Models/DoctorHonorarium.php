<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorHonorarium extends Model
{
    protected $table = 'doctor_honorariums';

    protected $fillable = [
        'doctor_id',
        'report_category_id',
        'amount',
        'percentage',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function reportCategory()
    {
        return $this->belongsTo(ReportCategory::class);
    }
}
