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

    public function isComplete()
    {
        if ($this->tests->isEmpty()) {
            return false;
        }

        foreach ($this->tests as $test) {
            $category = $test->category;
            // If no category (misconfiguration), we consider it incomplete
            if (!$category) {
                return false;
            }

            // REQUIRED: No result record = incomplete
            if (!$test->result) {
                return false;
            }

            $results = json_decode($test->result->resilt, true);
            if (!is_array($results) || empty($results)) {
                return false;
            }

            // Check if ALL entered results are empty strings/nulls
            $hasAnyActualData = false;
            foreach ($results as $val) {
                if (trim((string)$val) !== '') {
                    $hasAnyActualData = true;
                    break;
                }
            }
            if (!$hasAnyActualData) {
                return false;
            }

            $requiredFields = $category->fields;
            if (!$requiredFields->isEmpty()) {
                // Standard case: Check every defined field is filled
                foreach ($requiredFields as $field) {
                    if (!isset($results[$field->id]) || trim((string)$results[$field->id]) === '') {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
