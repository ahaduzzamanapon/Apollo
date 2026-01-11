<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PatientPayment;
use App\Models\PatientReport;
use App\Models\ReportCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = PatientReport::with(['patient', 'referenceDoctor']);

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('report_code', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    })
                    ->orWhereHas('referenceDoctor', function ($d) use ($search) {
                        $d->where('name', 'like', "%{$search}%");
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
            $filename = 'patient_reports_'.date('Y-m-d_H-i-s').'.csv';

            return response()->streamDownload(function () use ($reports) {
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
                        $report->due_amount,
                    ]);
                }
                fclose($file);
            }, $filename);
        }

        // Export PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $reports = $query->latest()->get();
            $pdf = Pdf::loadView('admin.patients.pdf', compact('reports'));

            return $pdf->download('patient_reports_'.date('Y-m-d_H-i-s').'.pdf');
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
            $query->where(function ($q) use ($search) {
                $q->where('report_code', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($p) use ($search) {
                        $p->where('name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%");
                    })
                    ->orWhereHas('referenceDoctor', function ($d) use ($search) {
                        $d->where('name', 'like', "%{$search}%");
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
            $filename = 'due_list_'.date('Y-m-d_H-i-s').'.csv';

            return response()->streamDownload(function () use ($reports) {
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
                        $report->due_amount,
                    ]);
                }
                fclose($file);
            }, $filename);
        }

        // Export PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $reports = $query->latest()->get();
            $pdf = Pdf::loadView('admin.patients.pdf', compact('reports'));

            return $pdf->download('due_list_'.date('Y-m-d_H-i-s').'.pdf');
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
            'age_unit' => 'required|in:Years,Months,Days',
            'gender' => 'required|in:Male,Female,Other',
            'report_date' => 'required|date',
            'reference_doctor_id' => 'nullable|exists:doctors,id',
            'tests' => 'required|array',
            'tests.*' => 'exists:report_categories,id',
            'discount' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
        ]);

        // Calculate DOB based on Age & Unit
        $dob = null;
        if ($request->age_unit == 'Years') {
            $dob = Carbon::now()->subYears($request->age);
        } elseif ($request->age_unit == 'Months') {
            $dob = Carbon::now()->subMonths($request->age);
        } elseif ($request->age_unit == 'Days') {
            $dob = Carbon::now()->subDays($request->age);
        }

        // 1. Create or Find Patient
        $patient = Patient::create([
            'name' => $request->patient_name,
            'mobile' => $request->mobile,
            'nid' => $request->nid,
            'age' => $request->age,
            'age_unit' => $request->age_unit,
            'dob' => $dob,
            'gender' => $request->gender,
        ]);

        // 2. Create Report Header
        // Generate Unique ID: ADDC_000001
        $lastReport = PatientReport::latest('id')->first();
        $nextId = $lastReport ? $lastReport->id + 1 : 1;
        $reportCode = 'ADDC_'.str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $report = PatientReport::create([
            'report_code' => $reportCode,
            'patient_id' => $patient->id,
            'reference_doctor_id' => $request->reference_doctor_id,
            'ref_by_someone' => $request->has('ref_by_someone') ? 1 : 0,
            'report_date' => $request->report_date,
            'discount' => $request->discount ?? 0,
            'paid_amount' => $request->paid_amount ?? 0,
        ]);

        // 3. Add Tests & Calculate Totals
        $totalAmount = 0;
        $testData = [];
        $totalRawCommission = 0;

        // Fetch doctor honorariums
        $doctorHonorariums = [];
        if ($request->reference_doctor_id && !$request->has('ref_by_someone')) { // Check ref_by_someone logic too
            $doctor = Doctor::with('honorariums')->find($request->reference_doctor_id);
            if($doctor) {
                 foreach ($doctor->honorariums as $honorarium) {
                    $doctorHonorariums[$honorarium->report_category_id] = $honorarium;
                }
            }
        }

        // Pass 1: Calculate Totals and Raw Commissions
        foreach ($request->tests as $categoryId) {
            $category = ReportCategory::find($categoryId);
            if(!$category) continue;

            $price = $category->price;
            $totalAmount += $price;

            $commission = 0;
            // Only calculate commission if NOT ref_by_someone (though handled by empty array above, safety check)
            if (!$request->has('ref_by_someone')) {
                if (isset($doctorHonorariums[$categoryId])) {
                    $hon = $doctorHonorariums[$categoryId];
                    if ($hon->amount > 0) {
                        $commission = $hon->amount;
                    } elseif ($hon->percentage > 0) {
                        $commission = ($price * $hon->percentage) / 100;
                    }
                }
            }

            $totalRawCommission += $commission;

            $testData[] = [
                'category_id' => $categoryId,
                'price' => $price,
                'raw_commission' => $commission,
            ];
        }

        // Apply Discount Logic to Commission
        // User Logic: Doctor Commission = Total Commission - Patient Discount
        // If Discount > Commission, Doctor gets 0.
        $patientDiscount = $request->discount ?? 0;
        $netTotalCommission = max(0, $totalRawCommission - $patientDiscount);

        // Calculate adjustment factor to distribute net commission back to tests
        // Avoid division by zero
        $commissionFactor = $totalRawCommission > 0 ? ($netTotalCommission / $totalRawCommission) : 0;

        // Pass 2: Create PatientTest Records
        foreach ($testData as $data) {
            // Distribute the net commission proportionally
            $finalCommission = $data['raw_commission'] * $commissionFactor;

            $report->tests()->create([
                'report_category_id' => $data['category_id'],
                'price' => $data['price'],
                'commission_amount' => $finalCommission,
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
        if (($request->paid_amount ?? 0) > 0) {
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
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $report = PatientReport::findOrFail($request->patient_report_id);

        $paymentAmount = $request->amount;
        $discountAmount = $request->discount ?? 0;
        $totalReduction = $paymentAmount + $discountAmount;

        if ($totalReduction > $report->due_amount) {
            return back()->with('error', 'Payment amount + Discount cannot exceed due amount!');
        }

        // Create Payment Record (Store discount here)
        PatientPayment::create([
            'patient_report_id' => $report->id,
            'amount' => $paymentAmount,
            'discount' => $discountAmount,
            'payment_method' => $request->payment_method,
            'remarks' => $request->remarks,
            'collected_by' => Auth::guard('admin')->id(),
        ]);

        // Update Report Totals
        // 1. Update Paid Amount (Increment by actual money paid)
        $newPaidTotal = $report->paid_amount + $paymentAmount;

        // 2. Update Due Amount (Decrease by money paid + discount given)
        $totalReduction = $paymentAmount + $discountAmount;
        $newDueAmount = max(0, $report->due_amount - $totalReduction);

        // Note: We do NOT update report->discount or report->final_amount as per user request.
        // The discount on payment is considered a "waiver" or "adjustment" recorded in payment history.

        $report->update([
            'paid_amount' => $newPaidTotal,
            'due_amount' => $newDueAmount,
        ]);

        return back()->with('success', 'Payment Received Successfully!');
    }

    public function show($id)
    {
        $report = PatientReport::with(['patient', 'tests.category', 'referenceDoctor'])->findOrFail($id);

        return view('admin.patients.show', compact('report'));
    }

    public function downloadInvoice($id)
    {
        // $report = PatientReport::with(['patient', 'tests.category', 'referenceDoctor'])->findOrFail($id);
        // $pdf = Pdf::loadView('admin.patients.invoice_pdf', compact('report'))
        // ->setPaper('a5', 'portrait');
        // return $pdf->stream('invoice_'.$report->report_code.'.pdf');

        $report = PatientReport::with(['patient', 'tests.category', 'referenceDoctor'])
            ->findOrFail($id);

        // mPDF config
        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'default_font' => 'FreeSerif',
            'fontDir' => array_merge($fontDirs, [
                public_path('fonts/'),
            ]),
            'fontdata' => $fontData + [
                'kalpurush' => [
                    'R' => 'kalpurush.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],
            'format' => 'A5',
            'margin' => 15,
        ]);

        // Load Blade view (same as DOMPDF)
        $html = view('admin.patients.invoice_pdf', compact('report'))->render();

        $mpdf->WriteHTML($html);

        // Stream PDF (browser preview)
      return response(
        $mpdf->Output('invoice_'.$report->report_code.'.pdf', 'I'))
             ->header('Content-Type', 'application/pdf')
             ->header('Content-Disposition', 'attachment; filename="invoice_'.$report->report_code.'.pdf"');

    }
}
