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
        $query = PatientTest::with(['report.referenceDoctor', 'category'])
            ->whereHas('report', function($q) {
                $q->whereNotNull('reference_doctor_id');
            });

        if ($request->has('doctor_id') && $request->doctor_id) {
            $query->whereHas('report', function($q) use ($request) {
                $q->where('reference_doctor_id', $request->doctor_id);
            });
        }
        
        // Filter by date range if needed
        if ($request->date) {
             $query->whereHas('report', function($q) use ($request) {
                $q->whereDate('report_date', $request->date);
            });
        }

        $commissions = $query->paginate(20);
        $doctors = Doctor::all();
        
        $totalCommission = $commissions->sum('commission_amount'); // Current page sum, better to do aggregate query for total

        return view('admin.reports.commission', compact('commissions', 'doctors', 'totalCommission'));
    }
}
