<?php

namespace App\Http\Controllers\Admin;

use App\Models\PatientTestResult;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PatientTestResultController extends Controller
{
    public function index(Request $request)
    {
        $query = PatientTestResult::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                  ->orWhere('patien_id', 'like', "%$search%")
                  ->orWhere('resilt', 'like', "%$search%");
            });
        }
        $items = $query->paginate(10);
        if ($request->ajax()) {
            return view('admin.patientTestResults.table', compact('items'))->render();
        }
        return view('admin.patientTestResults.index', compact('items'));
    }


    public function exportPdf()
    {
        $items = PatientTestResult::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.patientTestResults.pdf', compact('items'));
        return $pdf->download('patientTestResults.pdf');
    }

    public function exportExcel()
    {
        // Excel export logic using maatwebsite/excel
        // For simplicity, we can use a simple CSV download or implement proper Excel export class later
        return response()->streamDownload(function () {
            $items = PatientTestResult::all();
            $handle = fopen('php://output', 'w');
            // Add Header
            fputcsv($handle, ['ID', 'Id', 'Patien_Id', 'Test_Id', 'Resilt']);
            foreach ($items as $item) {
                fputcsv($handle, [$item->id, $item->id, $item->patien_id, $item->test_id, $item->resilt]);
            }
            fclose($handle);
        }, 'patientTestResults.csv');
    }


    public function create()
    {

        return view('admin.patientTestResults.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'patien_id' => 'required',
            'test_id' => 'required',
            'resilt' => 'required',
        ]);

        $data = $request->except(['_token', '_method']);

        PatientTestResult::create($data);
        return redirect()->route('admin.patientTestResults.index')->with('success', 'PatientTestResult created successfully.');
    }

    public function edit($id)
    {
        $item = PatientTestResult::findOrFail($id);

        return view('admin.patientTestResults.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = PatientTestResult::findOrFail($id);
        $data = $request->validate([
            'id' => 'required',
            'patien_id' => 'required',
            'test_id' => 'required',
            'resilt' => 'required',
        ]);

        $data = $request->except(['_token', '_method']);

        $item->update($data);
        return redirect()->route('admin.patientTestResults.index')->with('success', 'PatientTestResult updated successfully.');
    }

    public function destroy($id)
    {
        $item = PatientTestResult::findOrFail($id);

        $item->delete();
        return back()->with('success', 'PatientTestResult deleted successfully.');
    }
}
