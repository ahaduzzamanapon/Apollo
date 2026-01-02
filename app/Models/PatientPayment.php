<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'patient_report_id',
        'amount',
        'discount',
        'payment_method',
        'transaction_id',
        'remarks',
        'collected_by',
    ];

    public function report()
    {
        return $this->belongsTo(PatientReport::class, 'patient_report_id');
    }

    public function collectedBy()
    {
        return $this->belongsTo(Admin::class, 'collected_by');
    }
}
