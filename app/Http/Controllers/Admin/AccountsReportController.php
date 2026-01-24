<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PatientPayment;
use App\Models\Expense;
use App\Models\BankTransaction;
use App\Models\PatientReport;
use App\Models\PatientTest;

class AccountsReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();

        // 1. Fetch Data grouped by Date
        // Expenses
        $expenses = Expense::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy(function($item) { return Carbon::parse($item->date)->format('Y-m-d'); });

        // Commissions (Probable - based on Report Date)
        $commissions = PatientTest::with('report')
            ->whereHas('report', function($q) use ($startDate, $endDate) {
                $q->whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->where('ref_by_someone', 0);
            })
            ->get()
            ->groupBy(function($item) { return Carbon::parse($item->report->report_date)->format('Y-m-d'); });

        // Bank Deposits
        $bankDeposits = BankTransaction::where('trans_type', 'Deposit')
            ->whereBetween('trans_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy(function($item) { return Carbon::parse($item->trans_date)->format('Y-m-d'); });

        // Patient Reports (Income/Due)
        $reports = PatientReport::whereBetween('report_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->groupBy(function($item) { return Carbon::parse($item->report_date)->format('Y-m-d'); });


        // 2. Prepare Report Data
        $reportData = collect();
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        $grandTotalIncome = 0;
        $grandTotalDue = 0;
        $grandTotalExpense = 0;
        $grandTotalCommission = 0;
        $grandTotalDeposit = 0;

        foreach ($period as $date) {
            $day = $date->format('Y-m-d');
            
            // Calc Daily Totals
            $dayExpense = isset($expenses[$day]) ? $expenses[$day]->sum('amount') : 0;
            $dayCommission = isset($commissions[$day]) ? $commissions[$day]->sum('commission_amount') : 0;
            $dayDeposit = isset($bankDeposits[$day]) ? $bankDeposits[$day]->sum('amount') : 0;
            
            $dayIncome = isset($reports[$day]) ? $reports[$day]->sum('final_amount') : 0;
            $dayDue = isset($reports[$day]) ? $reports[$day]->sum('due_amount') : 0;

            // Add to Grand Totals
            $grandTotalIncome += $dayIncome;
            $grandTotalDue += $dayDue;
            $grandTotalExpense += $dayExpense;
            $grandTotalCommission += $dayCommission;
            $grandTotalDeposit += $dayDeposit;

            // Add rows for this day
            // Only add if there is data
            // Add rows for this day
            // If any activity exists for the day, show all 5 rows for consistent snapshot
            if ($dayExpense > 0 || $dayCommission > 0 || $dayDeposit > 0 || $dayIncome > 0 || $dayDue > 0) {
                 $reportData->push([
                    'date' => $day,
                    'description' => 'Daily Expenses Total (Actual Expenses)',
                    'amount' => $dayExpense,
                    'url' => route('expenses.index', ['start_date' => $day, 'end_date' => $day])
                ]);
                $reportData->push([
                    'date' => $day,
                    'description' => "Daily Doctor's Commission (Not Actual Expenses)",
                    'amount' => $dayCommission,
                    'url' => route('commission.index', ['start_date' => $day, 'end_date' => $day])
                ]);
                $reportData->push([
                    'date' => $day,
                    'description' => 'Daily Total Bank Deposit',
                    'amount' => $dayDeposit,
                    'url' => null
                ]);
                $reportData->push([
                    'date' => $day,
                    'description' => 'Daily Total Due',
                    'amount' => $dayDue,
                    'url' => route('admin.patients.due', ['start_date' => $day, 'end_date' => $day])
                ]);
                $reportData->push([
                    'date' => $day,
                    'description' => 'Daily Total Income',
                    'amount' => $dayIncome,
                    'url' => route('admin.patients.index', ['start_date' => $day, 'end_date' => $day])
                ]);
            }
        }

        // Calculations for Footer
        $allTotalExpenses = $grandTotalExpense + $grandTotalCommission + $grandTotalDeposit;
        $totalBalance = ($grandTotalIncome - $grandTotalDue) - $allTotalExpenses;

        // Export CSV
        if ($request->has('export') && $request->export == 'csv') {
            $filename = 'accounts_report_'.date('Y-m-d_H-i-s').'.csv';
            return response()->streamDownload(function () use ($reportData, $grandTotalIncome, $allTotalExpenses, $totalBalance) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Date', 'Note/Description', 'Amount']);
                
                foreach ($reportData as $row) {
                    fputcsv($file, [$row['date'], $row['description'], $row['amount']]);
                }
                
                // Summary in CSV
                fputcsv($file, ['', '', '']);
                fputcsv($file, ['', 'Total Income (All with paid or unpaid)', $grandTotalIncome]);
                fputcsv($file, ['', 'All Total Expenses', $allTotalExpenses]);
                fputcsv($file, ['', 'Total Balance Remain In Cash Now', $totalBalance]);

                fclose($file);
            }, $filename);
        }

        // Export PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.accounts.report_pdf', compact('reportData', 'grandTotalIncome', 'allTotalExpenses', 'totalBalance', 'startDate', 'endDate'));
            $pdf->setPaper('a4', 'portrait');

            return $pdf->download('accounts_report_' . date('Y-m-d_H-i-s') . '.pdf');
        }

        return view('admin.accounts.report', compact(
            'startDate', 'endDate', 'reportData', 
            'grandTotalIncome', 'allTotalExpenses', 'totalBalance'
        ));
    }
}
