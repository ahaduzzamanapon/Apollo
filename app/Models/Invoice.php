<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_report_id',
        'invoice_code',
        'barcode',
        'barcode_data',
        'amount',
        'discount',
        'total',
        'payment_method',
        'status',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function patientReport()
    {
        return $this->belongsTo(PatientReport::class, 'patient_report_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class)->through('patientReport');
    }

    public function payments()
    {
        return $this->hasMany(PatientPayment::class, 'patient_report_id', 'patient_report_id');
    }
}
