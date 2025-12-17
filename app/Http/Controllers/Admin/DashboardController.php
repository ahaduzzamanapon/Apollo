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

use App\Models\PatientPayment;
use App\Models\BankTransaction; // Add missing imports

class DashboardController extends Controller
{
    public function index()
    {
        // Monthly Stats (Existing)
        $totalDue = PatientReport::sum('due_amount');
        $totalMonthlyReports = PatientReport::whereMonth('report_date', Carbon::now()->month)->count();
        $totalMonthlyIncome = PatientReport::whereMonth('report_date', Carbon::now()->month)->sum('paid_amount');
        $totalMonthlyExpenses = Expense::whereMonth('date', Carbon::now()->month)->sum('amount');
        $totalProfit = $totalMonthlyIncome - $totalMonthlyExpenses;
        $totalPatients = Patient::count();
        $totalDailyHonorarium = PatientTest::whereHas('report', function($q) {
            $q->whereDate('report_date', Carbon::today());
        })->sum('commission_amount');

        // Daily Stats (New)
        $today = Carbon::today();
        
        $todayIncome = PatientPayment::whereDate('created_at', $today)->sum('amount');
        $todayExpense = Expense::whereDate('date', $today)->sum('amount');
        
        $todayDeposit = BankTransaction::whereDate('trans_date', $today)
                        ->where('trans_type', 'Deposit')
                        ->sum('amount');
                        
        $todayWithdraw = BankTransaction::whereDate('trans_date', $today)
                        ->where('trans_type', 'Withdraw')
                        ->sum('amount');

        // Cash Balance logic: (Income + Withdraw) - (Expense + Deposit)
        $todayBalance = ($todayIncome + $todayWithdraw) - ($todayExpense + $todayDeposit);

        $todayPatients = PatientReport::whereDate('report_date', $today)->count();


        return view('admin.dashboard', compact(
            'totalDue',
            'totalMonthlyReports',
            'totalProfit',
            'totalDailyHonorarium',
            'totalMonthlyExpenses',
            'totalMonthlyIncome',
            'totalPatients',
            'todayIncome',
            'todayExpense',
            'todayDeposit',
            'todayWithdraw',
            'todayBalance',
            'todayPatients'
        ));
    }
}
