<?php

namespace App\Http\Controllers\Admin;

use App\Models\TestField;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ReportCategory;


class TestFieldController extends Controller
{
    public function index(Request $request)
    {
        $query = TestField::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('perameter', 'like', "%$search%")
                  ->orWhere('unit', 'like', "%$search%")
                  ->orWhere('ref_val', 'like', "%$search%");
            });
        }
        $items = $query->paginate(10);
        if ($request->ajax()) {
            return view('admin.testFields.table', compact('items'))->render();
        }
        return view('admin.testFields.index', compact('items'));
    }


    public function exportPdf()
    {
        $items = TestField::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.testFields.pdf', compact('items'));
        return $pdf->download('testFields.pdf');
    }

    public function exportExcel()
    {
        // Excel export logic using maatwebsite/excel
        // For simplicity, we can use a simple CSV download or implement proper Excel export class later
        return response()->streamDownload(function () {
            $items = TestField::all();
            $handle = fopen('php://output', 'w');
            // Add Header
            fputcsv($handle, ['ID', 'Test_Id', 'Perameter', 'Unit', 'Ref_Val']);
            foreach ($items as $item) {
                fputcsv($handle, [$item->id, $item->test_id, $item->perameter, $item->unit, $item->ref_val]);
            }
            fclose($handle);
        }, 'testFields.csv');
    }


    public function create()
    {

        return view('admin.testFields.create');
    }

    public function store(Request $request)
    {
        // $data = $request->validate([
        //     'test_id' => 'required',
        //     'perameter' => 'required',
        //     'unit' => 'required',
        //     'ref_val' => 'required',
        // ]);

        $request->validate([
            'test_id' => 'required|integer',
            'fields' => 'required|array|min:1',
            'fields.*.perameter' => 'required|string',
            'fields.*.unit' => 'required|string',
            'fields.*.ref_val' => 'required|string',
        ]);

        foreach ($request->fields as $field) {
            TestField::create([
                'test_id'   => $request->test_id,
                'perameter' => $field['perameter'], // keep DB column name
                'unit'      => $field['unit'],
                'ref_val'   => $field['ref_val'],
            ]);
        }
        return redirect()->route('reports.index')->with('success', 'TestField created successfully.');
    }

    public function edit($id)
    {
        $item = TestField::findOrFail($id);

        return view('admin.testFields.edit', compact('item'));
    }

    public function byTest($testId)
    {
        return TestField::where('test_id', $testId)->get();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'test_id' => 'required',
            'fields' => 'required|array',
            'fields.*.perameter' => 'required',
            'fields.*.unit' => 'required',
            'fields.*.ref_val' => 'required',
        ]);

        foreach ($request->fields as $field) {

            if (!empty($field['id'])) {
                TestField::where('id', $field['id'])->update([
                    'perameter' => $field['perameter'],
                    'unit' => $field['unit'],
                    'ref_val' => $field['ref_val'],
                ]);
            } else {
                TestField::create([
                    'test_id' => $request->test_id,
                    'perameter' => $field['perameter'],
                    'unit' => $field['unit'],
                    'ref_val' => $field['ref_val'],
                ]);
            }
        }

        return back()->with('success', 'Test fields updated successfully');
    }


    public function destroy($id)
    {
        $item = TestField::findOrFail($id);

        $item->delete();
        return back()->with('success', 'TestField deleted successfully.');
    }
}
