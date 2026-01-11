<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankTransaction;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $banks = Bank::all();
        $activeBank = null;
        $total_deposit = 0;
        $total_withdraw = 0;
        $balance = 0;
        $transactions = collect();

        // Filters
        $startDate = $request->start_date ?? date('Y-m-d');
        $endDate = $request->end_date ?? date('Y-m-d');
        $type = $request->trans_type ?? 'All';

        if ($request->has('bank_id')) {
            $activeBank = $banks->firstWhere('id', $request->bank_id);
        } else {
            $activeBank = $banks->first();
        }

        if ($activeBank) {
            // 1. Calculate Overall Balance (Life Time) - Not affected by Date Filters
            // using a fresh query to avoid loading all records if possible, but load() is already used on $activeBank model instance from collection.
            // Since we are iterating all banks, they are already loaded. 
            // Better to run a direct query for totals if we want efficiency, but sticking to existing pattern for consistency unless it's huge.
            // Actually, $banks = Bank::all() loads everything. $activeBank is one of them.
            // To be safe and correct with "Life Time" vs "Filtered", we should query specifically.
            
            $lifeTimeTrans = BankTransaction::where('bank_id', $activeBank->id)->get();
            $total_deposit = $lifeTimeTrans->where('trans_type', 'Deposit')->sum('amount');
            $total_withdraw = $lifeTimeTrans->where('trans_type', 'Withdraw')->sum('amount');
            $balance = $total_deposit - $total_withdraw;

            // 2. Fetch Filtered Transactions for List
            $transQuery = $activeBank->transactions()->whereBetween('trans_date', [$startDate, $endDate]);

            if ($type != 'All') {
                $transQuery->where('trans_type', $type);
            }

            // Export CSV
            if ($request->has('export') && $request->export == 'csv') {
                $filename = "bank_transactions_" . date('Y-m-d_H-i-s') . ".csv";
                $transactions = $transQuery->orderBy('trans_date', 'desc')->get();
                
                return response()->streamDownload(function() use ($transactions, $activeBank) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['Bank: ' . $activeBank->name, 'Account: ' . $activeBank->account_no]);
                    fputcsv($file, ['Date', 'Type', 'Amount', 'Description']);
                    
                    foreach ($transactions as $trans) {
                        fputcsv($file, [
                            $trans->trans_date,
                            $trans->trans_type,
                            $trans->amount,
                            $trans->note
                        ]);
                    }
                    fclose($file);
                }, $filename);
            }

            // Export PDF
            if ($request->has('export') && $request->export == 'pdf') {
                $transactions = $transQuery->orderBy('trans_date', 'desc')->get();
                $filtered_deposit = $transactions->where('trans_type', 'Deposit')->sum('amount');
                $filtered_withdraw = $transactions->where('trans_type', 'Withdraw')->sum('amount');

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.banks.pdf', compact('activeBank', 'transactions', 'startDate', 'endDate', 'filtered_deposit', 'filtered_withdraw'));
                return $pdf->download('bank_transactions_' . date('Y-m-d_H-i-s') . '.pdf');
            }

            $transactions = $transQuery->orderBy('trans_date', 'desc')->orderBy('id', 'desc')->get();
            
            // Calculate totals for the filtered list
            $filtered_deposit = $transactions->where('trans_type', 'Deposit')->sum('amount');
            $filtered_withdraw = $transactions->where('trans_type', 'Withdraw')->sum('amount');
        }

        return view('admin.banks.index', compact(
            'banks', 'activeBank', 'total_deposit', 'total_withdraw', 'balance', 'transactions', 
            'startDate', 'endDate', 'type', 'filtered_deposit', 'filtered_withdraw'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'account_no' => 'nullable'
        ]);

        Bank::create($request->all());

        return redirect()->back()->with('success', 'Bank Added Successfully');
    }

    public function show($id)
    {
        $bank = Bank::with(['transactions' => function($q) {
            $q->orderBy('trans_date', 'desc')->orderBy('id', 'desc');
        }])->findOrFail($id);

        $total_deposit = $bank->transactions->where('trans_type', 'Deposit')->sum('amount');
        $total_withdraw = $bank->transactions->where('trans_type', 'Withdraw')->sum('amount');
        $balance = $total_deposit - $total_withdraw;

        return view('admin.banks.show', compact('bank', 'total_deposit', 'total_withdraw', 'balance'));
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'trans_type' => 'required|in:Deposit,Withdraw',
            'amount' => 'required|numeric|min:0',
            'trans_date' => 'required|date',
            'note' => 'nullable'
        ]);

        BankTransaction::create($request->all());

        return redirect()->back()->with('success', 'Transaction Added Successfully');
    }
    
    public function destroy($id)
    {
        Bank::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Bank Deleted Successfully');
    }
}
