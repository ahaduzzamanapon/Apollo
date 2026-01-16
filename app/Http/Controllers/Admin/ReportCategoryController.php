<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportCategory;
use App\Models\TestCategory;
use Illuminate\Http\Request;

class ReportCategoryController extends Controller
{
    public function index()
    {
        $categories = TestCategory::with(['tests' => function ($query) {
            $query->orderBy('id', 'desc');
        }])->orderBy('id')->get();

        $categoriesArr = [];

        foreach ($categories as $category) {
            $categoriesArr[$category->category_name] = [
                'id' => $category->id,
                'tests' => $category->tests // collection, empty if none
            ];
        }
        return view('admin.reports.index', ['categories' => $categoriesArr]);
    }


    public function create(Request $request)
    {
        $category = null;

        if ($request->filled('category_id')) {
            $category = TestCategory::find($request->category_id);

            if (!$category) {
                return redirect()
                    ->route('reports.index')
                    ->with('error', 'Category not found!');
            }
        }

        return view('admin.reports.create', compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string',
            'test_name' => 'required|string',
            'price' => 'required|numeric',
            'room_no' => 'nullable|string',
        ]);

        ReportCategory::create($request->all());

        return redirect()->route('reports.index')->with('success', 'Report Category created successfully.');
    }

    public function edit(ReportCategory $report)
    {
        $category = TestCategory::find($report->category_name);

        return view('admin.reports.edit', compact('report', 'category'));
    }

    public function update(Request $request, ReportCategory $report)
    {
        $request->validate([
            'category_name' => 'required|string',
            'test_name' => 'required|string',
            'price' => 'required|numeric',
            'room_no' => 'nullable|string',
        ]);

        $report->update($request->all());

        return redirect()->route('reports.index')->with('success', 'Report Category updated successfully.');
    }

    public function destroy(ReportCategory $report)
    {
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Report Category deleted successfully.');
    }
}
