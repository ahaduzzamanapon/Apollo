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

        // Dashboard Table Data (Today's mixed transactions)
        $dashboardTrans = collect();
        
        $dPayments = PatientPayment::whereDate('created_at', $today)->latest()->get();
        foreach($dPayments as $p) {
            $dashboardTrans->push([
                'time' => $p->created_at,
                'desc' => 'Payment Rcvd #' . $p->id,
                'type' => 'Income',
                'amount' => $p->amount,
                'class' => 'text-success'
            ]);
        }
        
        $dExpenses = Expense::whereDate('date', $today)->latest()->get();
        foreach($dExpenses as $e) {
            $dashboardTrans->push([
                'time' => $e->created_at ?? $e->date,
                'desc' => $e->description ?? 'Expense',
                'type' => 'Expense',
                'amount' => $e->amount,
                'class' => 'text-danger'
            ]);
        }

        $dBank = BankTransaction::whereDate('trans_date', $today)->latest()->get();
        foreach($dBank as $b) {
            $dashboardTrans->push([
                'time' => $b->created_at,
                'desc' => 'Bank ' . $b->trans_type,
                'type' => $b->trans_type,
                'amount' => $b->amount,
                'class' => $b->trans_type == 'Deposit' ? 'text-primary' : 'text-warning'
            ]);
        }

        $todayTransactions = $dashboardTrans->sortByDesc('time')->take(10); // Last 10

        // Chart Data (Last 7 Days)
        $chartDates = [];
        $chartIncome = [];
        $chartExpense = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartDates[] = $date->format('d M');
            
            $chartIncome[] = PatientPayment::whereDate('created_at', $date)->sum('amount');
            $chartExpense[] = Expense::whereDate('date', $date)->sum('amount');
        }

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
            'todayPatients',
            'todayTransactions',
            'chartDates',
            'chartIncome',
            'chartExpense'
        ));
    }
}
