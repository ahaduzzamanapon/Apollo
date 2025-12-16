<?php

use App\Models\Doctor;
use App\Models\ReportCategory;
use App\Models\AccountsLedger;
use App\Models\Patient;
use App\Models\PatientReport;

// 1. Create a Doctor
$doctor = Doctor::create([
    'name' => 'Mr. Apon',
    'mobile' => '8801738245476',
    'email' => 'apon@gmail.com',
    'address' => 'Pirgang, Thakurgaon',
]);
echo "Doctor created: " . $doctor->name . "\n";

// 2. Create a Report Category
$category = ReportCategory::create([
    'category_name' => 'Haematology',
    'test_name' => 'ECHO 2D & M-MODE',
    'price' => 2000,
    'room_no' => 'AP-304'
]);
echo "Category created: " . $category->test_name . "\n";

// 3. Create Doctor Honorarium
$doctor->honorariums()->create([
    'report_category_id' => $category->id,
    'amount' => 500,
]);
echo "Honorarium assigned.\n";

// 4. Create Ledger
$ledger = AccountsLedger::create([
    'name' => 'Flat Fee',
    'type' => 'Expense'
]);
echo "Ledger created: " . $ledger->name . "\n";

// 5. Create Patient
$patient = Patient::create([
    'name' => 'Mr. Ahaduzzaman Apon',
    'mobile' => '8801738245476',
    'age' => 25,
    'gender' => 'Male'
]);
echo "Patient created: " . $patient->name . "\n";

// 6. Create Report
$report = PatientReport::create([
    'report_code' => 'ADDC_000001',
    'patient_id' => $patient->id,
    'reference_doctor_id' => $doctor->id,
    'report_date' => now(),
    'total_amount' => 2000,
    'due_amount' => 2000
]);
echo "Report created: " . $report->report_code . "\n";

// 7. Add Test to Report
$report->tests()->create([
    'report_category_id' => $category->id,
    'price' => 2000,
    'commission_amount' => 500
]);
echo "Test added to report.\n";

echo "\nVerification Successful!\n";
