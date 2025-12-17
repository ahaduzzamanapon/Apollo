<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\AccountsLedger;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('ledger');

        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('ledger', function($l) use ($search) {
                      $l->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Date Filter
        if ($request->has('start_date') && $request->start_date != '' && $request->has('end_date') && $request->end_date != '') {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Export CSV
        if ($request->has('export') && $request->export == 'true') {
            $expenses = $query->latest()->get();
            $filename = "expenses_" . date('Y-m-d_H-i-s') . ".csv";

            return response()->streamDownload(function() use ($expenses) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Date', 'Ledger', 'Description', 'Amount']);
                
                foreach ($expenses as $expense) {
                    fputcsv($file, [
                        $expense->date,
                        $expense->ledger->name,
                        $expense->description,
                        $expense->amount
                    ]);
                }
                fclose($file);
            }, $filename);
        }

        // Export PDF
        if ($request->has('export') && $request->export == 'pdf') {
            $expenses = $query->latest()->get();
            $pdf = Pdf::loadView('admin.expenses.pdf', compact('expenses'));
            return $pdf->download('expenses_' . date('Y-m-d_H-i-s') . '.pdf');
        }

        $expenses = $query->latest()->paginate(20);
        
        if ($request->ajax()) {
            return view('admin.expenses.table_body', compact('expenses'))->render();
        }

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
