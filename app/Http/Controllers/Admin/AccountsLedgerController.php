<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountsLedger;
use Illuminate\Http\Request;

class AccountsLedgerController extends Controller
{
    public function index()
    {
        $ledgers = AccountsLedger::latest()->paginate(10);
        return view('admin.ledgers.index', compact('ledgers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:accounts_ledgers,name',
            'type' => 'required|in:Income,Expense',
        ]);

        AccountsLedger::create($request->all());

        return back()->with('success', 'Ledger created successfully.');
    }

    public function destroy(AccountsLedger $ledger)
    {
        if($ledger->expenses()->count() > 0) {
            return back()->with('error', 'Cannot delete ledger with expenses.');
        }
        $ledger->delete();
        return back()->with('success', 'Ledger deleted successfully.');
    }
}
