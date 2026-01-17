<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\TestCategory;
use App\Models\ReportCategory;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $doctors = Doctor::with('honorariums')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.doctors.table_rows', compact('doctors'))->render();
        }

        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        $latestDoctor = Doctor::with('honorariums')->latest()->first();
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

        $tests = $categoriesArr;
        return view('admin.doctors.create', compact('tests', 'latestDoctor'));
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

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor created successfully.');
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('honorariums');

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

        $tests = $categoriesArr;

        return view('admin.doctors.edit', compact('doctor', 'tests'));
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

        return redirect()->route('admin.doctors.index')->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();
        return redirect()->route('admin.doctors.index')->with('success', 'Doctor deleted successfully.');
    }
}
