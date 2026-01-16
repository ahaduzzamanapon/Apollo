<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientTestResult extends Model
{
    use SoftDeletes;

    protected $table = 'patient_test_results';

    protected $fillable = ['id', 'patien_id', 'test_id', 'resilt'];


}
