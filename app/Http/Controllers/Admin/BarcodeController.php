<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientReport;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    /**
     * Display barcode scanner page
     *
     * @return \Illuminate\View\View
     */
    public function scanner()
    {
        return view('admin.barcode.scanner');
    }

    /**
     * Lookup bill by barcode (bill number)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $barcode = strtoupper(trim($request->barcode));

        try {
            $report = PatientReport::with(['patient', 'referenceDoctor', 'tests', 'payments'])
                ->where('report_code', $barcode)
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bill not found for barcode: ' . $barcode
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bill found',
                'data' => [
                    'id' => $report->id,
                    'bill_number' => $report->report_code,
                    'patient_name' => $report->patient->name,
                    'patient_mobile' => $report->patient->mobile,
                    'date' => $report->report_date,
                    'doctor' => $report->referenceDoctor->name ?? 'Self',
                    'total_amount' => $report->final_amount,
                    'paid_amount' => $report->paid_amount,
                    'due_amount' => $report->due_amount,
                    'test_count' => $report->tests->count(),
                    'payment_count' => $report->payments->count(),
                    'url' => route('admin.patients.show', $report->id)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing barcode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate barcode preview
     *
     * @param string $billNumber
     * @return \Illuminate\Http\Response
     */
    public function preview($billNumber)
    {
        try {
            $barcode = \App\Services\BarcodeService::generateBarcodeImage($billNumber);

            if (!$barcode) {
                return response()->json(['error' => 'Failed to generate barcode'], 500);
            }

            return response()->json([
                'success' => true,
                'barcode' => 'data:image/png;base64,' . $barcode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
