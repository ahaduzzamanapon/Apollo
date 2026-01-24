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
        // 1. Total Daily Due & List
        $todayDueQuery = PatientReport::whereDate('report_date', Carbon::today())->where('due_amount', '>', 0);
        $todayDue = $todayDueQuery->sum('due_amount');
        $todayDueList = $todayDueQuery->with('patient')->get();
        
        // 2. Total Test Report Daily & List
        $todayReportsQuery = PatientReport::whereDate('report_date', Carbon::today());
        $todayReports = $todayReportsQuery->count();
        $todayReportList = $todayReportsQuery->with(['patient', 'tests.category'])->get();
        
        // 5. Total Daily Expense & List
        $todayExpenseQuery = Expense::whereDate('date', Carbon::today());
        $todayExpense = $todayExpenseQuery->sum('amount');
        $todayExpenseList = $todayExpenseQuery->get();
        
        // 6. Total Daily Income & List
        $todayIncomeQuery = PatientPayment::whereDate('created_at', Carbon::today());
        $todayIncome = $todayIncomeQuery->sum('amount');
        $todayIncomeList = $todayIncomeQuery->with('report.patient')->get();
        
        // 3. Total Daily Profit
        $todayProfit = $todayIncome - $todayExpense;

        // 4. Total Doctor Honorarium Daily & List
        $dailyHonorariumQuery = PatientTest::whereHas('report', function($q) {
            $q->whereDate('report_date', Carbon::today())
              ->where('ref_by_someone', 0);
        })->where('commission_amount', '>', 0);
        
        $totalDailyHonorarium = $dailyHonorariumQuery->sum('commission_amount');
        $dailyHonorariumList = $dailyHonorariumQuery->with(['category', 'report.referenceDoctor', 'report.patient'])->get();

        // 7. Total Daily Patient & List
        $todayPatientsQuery = Patient::whereDate('created_at', Carbon::today()); // Registered today
        $todayPatients = $todayPatientsQuery->count();
        $todayPatientList = $todayPatientsQuery->get();

        // 8. Charts Data (Last 7 Days)
        $endDate = Carbon::today();
        $startDate = Carbon::today()->subDays(6);
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];
        $chartPatients = [];

        // Pre-fetch data for the range to avoid N+1 queries in loop
        $incomeData = PatientPayment::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $expenseData = Expense::whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->selectRaw('DATE(date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $patientData = Patient::whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d M');
            $chartIncome[] = $incomeData[$day] ?? 0;
            $chartExpense[] = $expenseData[$day] ?? 0;
            $chartPatients[] = $patientData[$day] ?? 0;
        }

        return view('admin.dashboard', compact(
            'todayDue', 'todayDueList',
            'todayReports', 'todayReportList',
            'todayProfit', 
            'totalDailyHonorarium', 'dailyHonorariumList',
            'todayExpense', 'todayExpenseList',
            'todayIncome', 'todayIncomeList',
            'todayPatients', 'todayPatientList',
            'chartLabels', 'chartIncome', 'chartExpense', 'chartPatients'
        ));
    }
}
