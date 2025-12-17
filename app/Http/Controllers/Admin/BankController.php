<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankTransaction;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::withSum(['transactions as total_deposit' => function($q) {
            $q->where('trans_type', 'Deposit');
        }], 'amount')
        ->withSum(['transactions as total_withdraw' => function($q) {
            $q->where('trans_type', 'Withdraw');
        }], 'amount')
        ->get();
        
        return view('admin.banks.index', compact('banks'));
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
