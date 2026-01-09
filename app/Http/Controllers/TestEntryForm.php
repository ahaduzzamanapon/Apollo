<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientReport;
use App\Models\Patient;
use App\Models\PatientTest;
use App\Models\Doctor;
use App\Models\Test;

class TestEntryForm extends Controller
{
    public function index()
    {
        $patients = PatientReport::with('tests','patient','referenceDoctor')->get();
        // dd($patients[1]->tests);
        return view('admin.patients.test_entry.patient_test_list', compact('patients'));
    }

    public function patientTestEntry($id)
    {
        $patient = Patient::find($id);
        // dd($patient);
        return view('admin.patients.test_entry.patient_test_entry', compact('patient'));
    }
}
