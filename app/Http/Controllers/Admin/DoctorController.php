<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\ReportCategory;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('honorariums')->latest()->paginate(10);
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $reportCategories = ReportCategory::all();
        return view('admin.doctors.create', compact('reportCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'honorariums' => 'array', // Array of report_category_id => [amount => X, percentage => Y]
        ]);

        $doctor = Doctor::create($request->only('name', 'mobile', 'email', 'address'));

        if ($request->has('honorariums')) {
            foreach ($request->honorariums as $categoryId => $data) {
                // Only create if at least one value is set
                if (!empty($data['amount']) || !empty($data['percentage'])) {
                    $doctor->honorariums()->create([
                        'report_category_id' => $categoryId,
                        'amount' => $data['amount'] ?? 0,
                        'percentage' => $data['percentage'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('doctors.index')->with('success', 'Doctor created successfully.');
    }

    public function edit(Doctor $doctor)
    {
        $reportCategories = ReportCategory::all();
        $doctor->load('honorariums');
        return view('admin.doctors.edit', compact('doctor', 'reportCategories'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $doctor->update($request->only('name', 'mobile', 'email', 'address'));

        // Sync honorariums (delete all and recreate is simplest for now, or update existing)
        $doctor->honorariums()->delete();

        if ($request->has('honorariums')) {
            foreach ($request->honorariums as $categoryId => $data) {
                 if (!empty($data['amount']) || !empty($data['percentage'])) {
                    $doctor->honorariums()->create([
                        'report_category_id' => $categoryId,
                        'amount' => $data['amount'] ?? 0,
                        'percentage' => $data['percentage'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}
