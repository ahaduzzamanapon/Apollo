<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\ReportCategory;
use App\Models\Doctor;
use App\Models\PatientReport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    public function index()
    {
        $reports = PatientReport::with(['patient', 'referenceDoctor'])->latest()->paginate(20);
        return view('admin.patients.index', compact('reports'));
    }

    public function due()
    {
        $reports = PatientReport::with(['patient', 'referenceDoctor'])
                    ->where('due_amount', '>', 0)
                    ->latest()
                    ->paginate(20);
        return view('admin.patients.due', compact('reports'));
    }

    public function create()
    {
        $doctors = Doctor::all();
        $categories = ReportCategory::orderBy('category_name')->get();
        return view('admin.patients.create', compact('doctors', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_name' => 'required|string',
            'mobile' => 'required|string',
            'age' => 'required|integer',
            'gender' => 'required|in:Male,Female,Other',
            'report_date' => 'required|date',
            'reference_doctor_id' => 'nullable|exists:doctors,id',
            'tests' => 'required|array',
            'tests.*' => 'exists:report_categories,id',
            'discount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
        ]);

        // 1. Create or Find Patient (Based on Mobile/NID logic if needed, but for now Create New)
        // Assuming every entry is a new functional patient visit, but let's try to reuse if mobile matches?
        // Requirement says "Patient Entry", implies creating a record.
        
        $patient = Patient::create([
            'name' => $request->patient_name,
            'mobile' => $request->mobile,
            'nid' => $request->nid,
            'age' => $request->age,
            'dob' => $request->dob, // Optional
            'gender' => $request->gender,
        ]);

        // 2. Create Report Header
        // Generate Unique ID: ADDC_000001
        $lastReport = PatientReport::latest('id')->first();
        $nextId = $lastReport ? $lastReport->id + 1 : 1;
        $reportCode = 'ADDC_' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $report = PatientReport::create([
            'report_code' => $reportCode,
            'patient_id' => $patient->id,
            'reference_doctor_id' => $request->reference_doctor_id,
            'report_date' => $request->report_date,
            'discount' => $request->discount ?? 0,
            'paid_amount' => $request->paid_amount ?? 0,
        ]);

        // 3. Add Tests & Calculate Totals
        $totalAmount = 0;
        
        // Fetch doctor honorariums
        $doctorHonorariums = [];
        if($request->reference_doctor_id) {
            $doctor = Doctor::with('honorariums')->find($request->reference_doctor_id);
            foreach($doctor->honorariums as $honorarium) {
                $doctorHonorariums[$honorarium->report_category_id] = $honorarium;
            }
        }

        foreach ($request->tests as $categoryId) {
            $category = ReportCategory::find($categoryId);
            $price = $category->price;
            $totalAmount += $price;

            // Calculate Commission
            $commission = 0;
            if (isset($doctorHonorariums[$categoryId])) {
                $hon = $doctorHonorariums[$categoryId];
                if ($hon->amount > 0) {
                    $commission = $hon->amount;
                } elseif ($hon->percentage > 0) {
                    $commission = ($price * $hon->percentage) / 100;
                }
            }

            $report->tests()->create([
                'report_category_id' => $categoryId,
                'price' => $price,
                'commission_amount' => $commission,
            ]);
        }

        // 4. Update Report Totals
        $finalAmount = $totalAmount - ($request->discount ?? 0);
        $dueAmount = $finalAmount - ($request->paid_amount ?? 0);

        $report->update([
            'total_amount' => $totalAmount,
            'final_amount' => $finalAmount,
            'due_amount' => max(0, $dueAmount),
        ]);

        return redirect()->route('patients.show', $report->id)->with('success', 'Report Entry Created Successfully');
    }

    public function show($id)
    {
        $report = PatientReport::with(['patient', 'tests.category', 'referenceDoctor'])->findOrFail($id);
        return view('admin.patients.show', compact('report'));
    }
}
