<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Doctor;
use App\Models\ReportCategory;
use App\Models\AccountsLedger;
use App\Models\Patient;
use App\Models\PatientReport;
use Carbon\Carbon;

class ApolloDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Doctors
        $doctor = Doctor::create([
            'name' => 'Mr. Apon',
            'mobile' => '8801738245476',
            'email' => 'apon@gmail.com',
            'address' => 'Pirgang, Thakurgaon',
        ]);

        // 2. Report Categories
        $cats = [
            ['cat' => 'Haematology', 'name' => 'ECHO 2D & M-MODE', 'price' => 2000, 'room' => 'AP-304'],
            ['cat' => 'Immunology', 'name' => 'ETT/TMT', 'price' => 3000, 'room' => 'AP-308'],
            ['cat' => 'Biochemistry', 'name' => 'X-Cervical Spine', 'price' => 500, 'room' => 'AP-303'],
            ['cat' => 'Urine', 'name' => 'Spine/Vertebra Both View', 'price' => 600, 'room' => 'AP-307'],
            ['cat' => 'Hormone Analysis', 'name' => 'L Spine/Vertebra', 'price' => 800, 'room' => 'AP-306'],
        ];

        foreach($cats as $c) {
            $cat = ReportCategory::create([
                'category_name' => $c['cat'],
                'test_name' => $c['name'],
                'price' => $c['price'],
                'room_no' => $c['room'],
            ]);

            // Assign Commission to Doctor Apon
            $amount = 0; $pct = 0;
            if($c['price'] == 2000 || $c['price'] == 3000) $amount = 500;
            elseif($c['price'] == 500) $amount = 200;
            elseif($c['price'] == 600) $pct = 15;
            elseif($c['price'] == 800) $pct = 20;

            if($amount > 0 || $pct > 0) {
                 $doctor->honorariums()->create([
                    'report_category_id' => $cat->id,
                    'amount' => $amount,
                    'percentage' => $pct,
                ]);
            }
        }

        // 3. Ledgers
        $ledgers = [
            ['name' => 'Flat Fee', 'type' => 'Expense'],
            ['name' => 'Logistic Fee', 'type' => 'Expense'],
            ['name' => 'Advance Fee', 'type' => 'Expense'],
            ['name' => 'Staff Salary', 'type' => 'Expense'],
        ];

        foreach($ledgers as $l) {
            AccountsLedger::create($l);
        }

        // 4. Sample Patient and Report
        $patient = Patient::create([
            'name' => 'Mr. Ahaduzzaman Apon',
            'nid' => '3285217690',
            'mobile' => '8801738245476',
            'age' => 25,
            'dob' => '1990-09-13',
            'gender' => 'Male',
        ]);

        // Bill
        $report = PatientReport::create([
            'report_code' => 'ADDC_000001',
            'patient_id' => $patient->id,
            'reference_doctor_id' => $doctor->id,
            'report_date' => '2025-12-12',
            'total_amount' => 2000,
            'discount' => 200, // 10%
            'final_amount' => 1800,
            'paid_amount' => 1000,
            'due_amount' => 800,
        ]);
        
        // Add Echo 2D
        $echo = ReportCategory::where('test_name', 'ECHO 2D & M-MODE')->first();
        if($echo) {
             $report->tests()->create([
                'report_category_id' => $echo->id,
                'price' => 2000,
                'commission_amount' => 500,
            ]);
        }
    }
}
