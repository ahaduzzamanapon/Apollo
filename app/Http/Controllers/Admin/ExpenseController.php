<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\AccountsLedger;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('ledger')->latest()->paginate(20);
        $ledgers = AccountsLedger::where('type', 'Expense')->get();
        return view('admin.expenses.index', compact('expenses', 'ledgers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'ledger_id' => 'required|exists:accounts_ledgers,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        Expense::create($request->all());

        return back()->with('success', 'Expense recorded successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }
}
