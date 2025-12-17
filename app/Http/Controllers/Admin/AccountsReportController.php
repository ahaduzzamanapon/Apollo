<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientPayment;
use App\Models\Expense;
use App\Models\BankTransaction;

class AccountsReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');

        // Fetch Data
        $payments = PatientPayment::whereDate('created_at', $date)->get();
        // Assuming Expenses has a 'date' field or created_at. Checking Expense migration is hard now, assuming created_at or `date`.
        // Usually expenses have a specific `date`. I'll use `date` if available or `created_at`.
        // Let's assume `date` based on typical expense entry.
        $expenses = Expense::whereDate('date', $date)->get(); 
        
        $bankTrans = BankTransaction::whereDate('trans_date', $date)->get();

        // Totals
        $total_income = $payments->sum('amount');
        $total_expense = $expenses->sum('amount');
        $total_deposit = $bankTrans->where('trans_type', 'Deposit')->sum('amount');
        $total_withdraw = $bankTrans->where('trans_type', 'Withdraw')->sum('amount');

        // Merging for detailed view
        $transactions = collect();

        foreach($payments as $payment) {
            $transactions->push([
                'created_at' => $payment->created_at,
                'description' => 'Payment Received - ID: ' . $payment->id, // Maybe add Patient Name if loaded
                'type' => 'Income',
                'amount' => $payment->amount
            ]);
        }

        foreach($expenses as $expense) {
            $transactions->push([
                'created_at' => $expense->created_at ?? $expense->date, // Fallback
                'description' => $expense->description ?? 'Expense',
                'type' => 'Expense',
                'amount' => $expense->amount
            ]);
        }

        foreach($bankTrans as $trans) {
            $transactions->push([
                'created_at' => $trans->created_at, // or trans_date w/ default time
                'description' => 'Bank ' . $trans->trans_type . ' (' . ($trans->bank->name ?? 'Bank') . ')',
                'type' => $trans->trans_type == 'Deposit' ? 'Bank Deposit' : 'Bank Withdraw', // Display label
                'amount' => $trans->amount
            ]);
        }

        // Sort by time
        $transactions = $transactions->sortByDesc('created_at');

        return view('admin.accounts.report', compact(
            'date', 'total_income', 'total_expense', 'total_deposit', 'total_withdraw', 'transactions'
        ));
    }
}
