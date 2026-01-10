<?php

namespace App\Http\Controllers\Admin;

use App\Models\CenterDetails;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CenterDetailsController extends Controller
{
    public function index(Request $request)
    {
        $query = CenterDetails::query();
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name_bn', 'like', "%$search%")
                  ->orWhere('name_en', 'like', "%$search%")
                  ->orWhere('about', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%")
                  ->orWhere('logo_image', 'like', "%$search%");
            });
        }
        $items = $query->paginate(10);
        if ($request->ajax()) {
            return view('admin.centerDetails.table', compact('items'))->render();
        }
        return view('admin.centerDetails.index', compact('items'));
    }


    public function exportPdf()
    {
        $items = CenterDetails::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.centerDetails.pdf', compact('items'));
        return $pdf->download('centerDetails.pdf');
    }

    public function exportExcel()
    {
        // Excel export logic using maatwebsite/excel
        // For simplicity, we can use a simple CSV download or implement proper Excel export class later
        return response()->streamDownload(function () {
            $items = CenterDetails::all();
            $handle = fopen('php://output', 'w');
            // Add Header
            fputcsv($handle, ['ID', 'Name_Bn', 'Name_En', 'About', 'Address', 'Phone', 'Logo_Image']);
            foreach ($items as $item) {
                fputcsv($handle, [$item->id, $item->name_bn, $item->name_en, $item->about, $item->address, $item->phone, $item->logo_image]);
            }
            fclose($handle);
        }, 'centerDetails.csv');
    }


    public function create()
    {

        return view('admin.centerDetails.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_bn' => 'required',
            'name_en' => 'required',
            'about' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'logo_image' => 'required',
        ]);

        $data = $request->except(['_token', '_method', 'logo_image']);
        if ($request->hasFile('logo_image')) {
            $data['logo_image'] = $request->file('logo_image')->store('centerDetails', 'public');
        }

        CenterDetails::create($data);
        return redirect()->route('admin.centerDetails.index')->with('success', 'CenterDetails created successfully.');
    }

    public function edit($id)
    {
        $item = CenterDetails::findOrFail($id);

        return view('admin.centerDetails.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = CenterDetails::findOrFail($id);

        // Validation
        $data = $request->validate([
            'name_bn'  => 'required|string',
            'name_en'  => 'required|string',
            'about'    => 'required|string',
            'address'  => 'required|string',
            'phone'    => 'required|string',
            'logo_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // If new image is uploaded
        if ($request->hasFile('logo_image')) {

            // Delete old image if exists
            if ($item->logo_image && Storage::disk('public')->exists($item->logo_image)) {
                Storage::disk('public')->delete($item->logo_image);
            }

            // Store new image
            $data['logo_image'] = $request->file('logo_image')
                ->store('centerDetails', 'public');
        } else {
            // Keep old image
            unset($data['logo_image']);
        }

        $item->update($data);

        return redirect()
            ->route('admin.centerDetails.index')
            ->with('success', 'CenterDetails updated successfully.');
    }


    public function destroy($id)
    {
        $item = CenterDetails::findOrFail($id);
        if ($item->logo_image) {
            Storage::disk('public')->delete($item->logo_image);
        }

        $item->delete();
        return back()->with('success', 'CenterDetails deleted successfully.');
    }
}
