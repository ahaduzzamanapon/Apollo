<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportCategory;
use Illuminate\Http\Request;

class ReportCategoryController extends Controller
{
    public function index()
    {
        $categories = ReportCategory::latest()->paginate(10);
        return view('admin.reports.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.reports.create');
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
        // Route param is 'report' but model is ReportCategory
        return view('admin.reports.edit', compact('report'));
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
