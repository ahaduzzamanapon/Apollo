<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\ReportCategory;
use App\Models\Doctor;
use App\Models\PatientReport;
use App\Models\PatientPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use Barryvdh\DomPDF\Facade\Pdf;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = PatientReport::with(['patient', 'referenceDoctor']);

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('report_code', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($p) use ($search) {
                      $p->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                  });
            });
        }

        // Date Filter
        if ($request->has('start_date') && $request->start_date != '' && $request->has('end_date') && $request->end_date != '') {
            $query->whereBetween('report_date', [$request->start_date, $request->end_date]);
        }

        // Export CSV
        if ($request->has('export') && $request->export == 'true') {
            $reports = $query->latest()->get();
            $filename = "patient_reports_" . date('Y-m-d_H-i-s') . ".csv";
            
            return response()->streamDownload(function() use ($reports) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Report Code', 'Date', 'Patient Name', 'Mobile', 'Doctor', 'Total Amount', 'Paid Amount', 'Due Amount']);
                
                foreach ($reports as $report) {
                    fputcsv($file, [
                        $report->report_code,
                        $report->report_date,
                        $report->patient->name,
                        $report->patient->mobile,
                        $report->referenceDoctor->name ?? 'Self',
                        $report->final_amount,
                        $report->paid_amount,
                        $report->due_amount
                    ]);
                }
                fclose($file);
            }, $filename);
        }

        // Export PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $reports = $query->latest()->get();
            $pdf = Pdf::loadView('admin.patients.pdf', compact('reports'));
            return $pdf->download('patient_reports_' . date('Y-m-d_H-i-s') . '.pdf');
        }

        // Calculate Totals (Clone query to avoid modifying the original builder for pagination)
        $total_final = (clone $query)->sum('final_amount');
        $total_paid = (clone $query)->sum('paid_amount');
        $total_due = (clone $query)->sum('due_amount');

        $reports = $query->latest()->paginate(20);

        if ($request->ajax()) {
            return view('admin.patients.table_body', compact('reports', 'total_final', 'total_paid', 'total_due'))->render();
        }

        return view('admin.patients.index', compact('reports', 'total_final', 'total_paid', 'total_due'));
    }

    public function due(Request $request)
    {
        $query = PatientReport::with(['patient', 'referenceDoctor'])
                    ->where('due_amount', '>', 0);

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('report_code', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($p) use ($search) {
                      $p->where('name', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                  });
            });
        }

        // Date Filter
        if ($request->has('start_date') && $request->start_date != '' && $request->has('end_date') && $request->end_date != '') {
            $query->whereBetween('report_date', [$request->start_date, $request->end_date]);
        }

         // Export CSV
         if ($request->has('export') && $request->export == 'true') {
            $reports = $query->latest()->get();
            $filename = "due_list_" . date('Y-m-d_H-i-s') . ".csv";
            
            return response()->streamDownload(function() use ($reports) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Report Code', 'Date', 'Patient Name', 'Mobile', 'Doctor', 'Total Amount', 'Paid Amount', 'Due Amount']);
                
                foreach ($reports as $report) {
                    fputcsv($file, [
                        $report->report_code,
                        $report->report_date,
                        $report->patient->name,
                        $report->patient->mobile,
                        $report->referenceDoctor->name ?? 'Self',
                        $report->final_amount,
                        $report->paid_amount,
                        $report->due_amount
                    ]);
                }
                fclose($file);
            }, $filename);
        }

        // Export PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $reports = $query->latest()->get();
            $pdf = Pdf::loadView('admin.patients.pdf', compact('reports'));
            return $pdf->download('due_list_' . date('Y-m-d_H-i-s') . '.pdf');
        }

        // Calculate Totals
        $total_final = (clone $query)->sum('final_amount');
        $total_paid = (clone $query)->sum('paid_amount');
        $total_due = (clone $query)->sum('due_amount');

        $reports = $query->latest()->paginate(20);

        if ($request->ajax()) {
            return view('admin.patients.table_body', compact('reports', 'total_final', 'total_paid', 'total_due'))->render();
        }

        return view('admin.patients.due', compact('reports', 'total_final', 'total_paid', 'total_due'));
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

        // 5. Create Payment Record if Paid Amount > 0
        if(($request->paid_amount ?? 0) > 0) {
            PatientPayment::create([
                'patient_report_id' => $report->id,
                'amount' => $request->paid_amount,
                'payment_method' => $request->payment_method ?? 'Cash',
                'remarks' => $request->remarks,
                'collected_by' => Auth::guard('admin')->id(),
            ]);
        }

        return redirect()->route('admin.patients.show', $report->id)->with('success', 'Report Entry Created Successfully');
    }

    public function addPayment(Request $request)
    {
        $request->validate([
            'patient_report_id' => 'required|exists:patient_reports,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $report = PatientReport::findOrFail($request->patient_report_id);

        if($request->amount > $report->due_amount) {
            return back()->with('error', 'Payment amount cannot exceed due amount!');
        }

        // Create Payment Record
        PatientPayment::create([
            'patient_report_id' => $report->id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'remarks' => $request->remarks,
            'collected_by' => Auth::guard('admin')->id(),
        ]);

        // Update Report Totals
        $report->increment('paid_amount', $request->amount);
        $report->decrement('due_amount', $request->amount);

        return back()->with('success', 'Payment Received Successfully!');
    }

    public function show($id)
    {
        $report = PatientReport::with(['patient', 'tests.category', 'referenceDoctor'])->findOrFail($id);
        return view('admin.patients.show', compact('report'));
    }

    public function downloadInvoice($id)
    {
        $report = PatientReport::with(['patient', 'tests.category', 'referenceDoctor'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.patients.invoice_pdf', compact('report'));
        $pdf->setPaper('a5', 'landscape');
        return $pdf->download('invoice_' . $report->report_code . '.pdf');
    }
}
