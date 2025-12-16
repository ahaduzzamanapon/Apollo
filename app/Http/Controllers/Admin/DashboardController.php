<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientReport;
use App\Models\PatientTest;
use App\Models\Expense;
use App\Models\AccountsLedger;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDue = PatientReport::sum('due_amount');
        $totalMonthlyReports = PatientReport::whereMonth('report_date', Carbon::now()->month)->count();
        $totalMonthlyIncome = PatientReport::whereMonth('report_date', Carbon::now()->month)->sum('paid_amount');
        
        $totalMonthlyExpenses = Expense::whereMonth('date', Carbon::now()->month)->sum('amount');
        
        // Profit = Income - Expenses (Basic logic)
        $totalProfit = $totalMonthlyIncome - $totalMonthlyExpenses;

        $totalPatients = Patient::count();
        
        // Daily Doctor Honorarium
        $totalDailyHonorarium = PatientTest::whereHas('report', function($q) {
            $q->whereDate('report_date', Carbon::today());
        })->sum('commission_amount');

        return view('admin.dashboard', compact(
            'totalDue',
            'totalMonthlyReports',
            'totalProfit',
            'totalDailyHonorarium',
            'totalMonthlyExpenses',
            'totalMonthlyIncome',
            'totalPatients'
        ));
    }
}
