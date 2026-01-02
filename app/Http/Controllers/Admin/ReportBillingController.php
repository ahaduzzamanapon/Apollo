<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientTest;
use App\Models\Doctor;
use Illuminate\Http\Request;

class ReportBillingController extends Controller
{
    public function commission(Request $request)
    {
        $status = $request->status ?? 'pending'; // pending, approved

        $query = PatientTest::with(['report.referenceDoctor', 'category', 'report.patient'])
            ->whereHas('report', function($q) {
                // Only show commissions for paid invoices
                $q->whereNotNull('reference_doctor_id')
                  ->where('due_amount', 0);
            })
            ->where('approval_status', $status);

        if ($request->has('doctor_id') && $request->doctor_id) {
            $query->whereHas('report', function($q) use ($request) {
                $q->where('reference_doctor_id', $request->doctor_id);
            });
        }
        
        // Filter by date range
        if ($request->start_date && $request->end_date) {
             $query->whereHas('report', function($q) use ($request) {
                $q->whereBetween('report_date', [$request->start_date, $request->end_date]);
            });
        }

        // Export CSV
        if ($request->has('export') && $request->export == 'csv') {
            $exportData = $query->latest()->get();
            $filename = 'commission_report_' . $status . '_' . date('Y-m-d_H-i-s') . '.csv';

            return response()->streamDownload(function () use ($exportData, $status) {
                $file = fopen('php://output', 'w');
                
                $headers = ['Date', 'Report ID', 'Doctor', 'Test', 'Price', 'Calculated Commission'];
                if ($status == 'approved') {
                    $headers[] = 'Approved Amount';
                    $headers[] = 'Approved Date';
                }
                fputcsv($file, $headers);

                foreach ($exportData as $data) {
                    $row = [
                        $data->report->report_date,
                        $data->report->report_code,
                        $data->report->referenceDoctor->name,
                        $data->category->test_name,
                        $data->price,
                        $data->commission_amount,
                    ];
                    if ($status == 'approved') {
                        $row[] = $data->approved_amount;
                        $row[] = $data->approved_at;
                    }
                    fputcsv($file, $row);
                }
                fclose($file);
            }, $filename);
        }

        $commissions = $query->latest()->paginate(20);
        $doctors = Doctor::all();
        
        // Calculate Totals for footer
        $totalCommission = $commissions->sum('commission_amount'); 
        $totalApproved = $commissions->sum('approved_amount');

        return view('admin.reports.commission', compact('commissions', 'doctors', 'totalCommission', 'totalApproved', 'status'));
    }

    public function approve(Request $request)
    {
        $request->validate([
            'selected_commissions' => 'required|array',
            'approved_amounts' => 'array',
        ]);

        foreach ($request->selected_commissions as $id) {
            $test = PatientTest::find($id);
            if ($test && $test->approval_status == 'pending') {
                $amount = $request->approved_amounts[$id] ?? $test->commission_amount;
                
                $test->update([
                    'approval_status' => 'approved',
                    'approved_amount' => $amount,
                    'approved_at' => now(),
                ]);
            }
        }

        return back()->with('success', 'Selected commissions approved successfully.');
    }
}
