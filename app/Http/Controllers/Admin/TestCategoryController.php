<?php

namespace App\Http\Controllers\Admin;

use App\Models\TestCategory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = TestCategory::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('category_name', 'like', "%$search%");
            });
        }
        $items = $query->paginate(10);
        if ($request->ajax()) {
            return view('admin.testCategories.table', compact('items'))->render();
        }
        return view('admin.testCategories.index', compact('items'));
    }


    public function exportPdf()
    {
        $items = TestCategory::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.testCategories.pdf', compact('items'));
        return $pdf->download('testCategories.pdf');
    }

    public function exportExcel()
    {
        // Excel export logic using maatwebsite/excel
        // For simplicity, we can use a simple CSV download or implement proper Excel export class later
        return response()->streamDownload(function () {
            $items = TestCategory::all();
            $handle = fopen('php://output', 'w');
            // Add Header
            fputcsv($handle, ['ID', 'Category_Name', 'Status']);
            foreach ($items as $item) {
                fputcsv($handle, [$item->id, $item->category_name, $item->status]);
            }
            fclose($handle);
        }, 'testCategories.csv');
    }


    public function create()
    {

        return view('admin.testCategories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_name' => 'required|required',
            'status' => 'required',
        ]);

        $data = $request->except(['_token', '_method']);

        TestCategory::create($data);
        return redirect()->route('admin.testCategories.index')->with('success', 'TestCategory created successfully.');
    }

    public function edit($id)
    {
        $item = TestCategory::findOrFail($id);

        return view('admin.testCategories.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = TestCategory::findOrFail($id);
        $data = $request->validate([
            'category_name' => 'required|required',
            'status' => 'required',
        ]);

        $data = $request->except(['_token', '_method']);

        $item->update($data);
        return redirect()->route('admin.testCategories.index')->with('success', 'TestCategory updated successfully.');
    }

    public function destroy($id)
    {
        $item = TestCategory::findOrFail($id);

        $item->delete();
        return back()->with('success', 'TestCategory deleted successfully.');
    }
}
