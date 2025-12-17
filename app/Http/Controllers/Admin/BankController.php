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

        if ($request->has('bank_id')) {
            $activeBank = $banks->firstWhere('id', $request->bank_id);
        } else {
            $activeBank = $banks->first();
        }

        if ($activeBank) {
            // Load transactions for calculations
            // Optimization: We could do this with aggregates if list is huge, but for now loading relations is fine
            $activeBank->load(['transactions' => function($q) {
                $q->orderBy('trans_date', 'desc')->orderBy('id', 'desc');
            }]);

            $total_deposit = $activeBank->transactions->where('trans_type', 'Deposit')->sum('amount');
            $total_withdraw = $activeBank->transactions->where('trans_type', 'Withdraw')->sum('amount');
            $balance = $total_deposit - $total_withdraw;
            $transactions = $activeBank->transactions;
        }

        return view('admin.banks.index', compact('banks', 'activeBank', 'total_deposit', 'total_withdraw', 'balance', 'transactions'));
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
