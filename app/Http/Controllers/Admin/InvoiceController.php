<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PatientReport;
use App\Services\BarcodeService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display list of invoices
     */
    public function index()
    {
        $invoices = Invoice::with(['patientReport.patient'])
            ->latest()
            ->paginate(15);

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Create invoice with barcode
     */
    public function create(PatientReport $report)
    {
        return view('admin.invoices.create', compact('report'));
    }

    /**
     * Store invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_report_id' => 'required|exists:patient_reports,id',
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $invoice = Invoice::where('patient_report_id', $validated['patient_report_id'])->first();

        if (!$invoice) {
            // Generate invoice code
            $invoiceCode = 'INV-' . date('Ymd') . '-' . str_pad(
                Invoice::count() + 1,
                5,
                '0',
                STR_PAD_LEFT
            );

            // Generate barcode
            $barcodeResult = BarcodeService::generateBarcode($invoiceCode);

            if (!$barcodeResult['success']) {
                return back()->with('error', 'Failed to generate barcode: ' . $barcodeResult['error']);
            }

            // Calculate total
            $amount = (float)$validated['amount'];
            $discount = (float)($validated['discount'] ?? 0);
            $total = $amount - $discount;

            // Create invoice
            $invoice = Invoice::create([
                'patient_report_id' => $validated['patient_report_id'],
                'invoice_code' => $invoiceCode,
                'barcode' => $barcodeResult['barcode'],
                'barcode_data' => $barcodeResult['barcode_data'],
                'amount' => $amount,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'remarks' => $validated['remarks'],
                'status' => 'pending'
            ]);
        }

        return redirect()->route('admin.invoices.show', $invoice)->with('success', 'Invoice created successfully');
    }

    /**
     * Show invoice details
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['patientReport.patient', 'patientReport.tests.category']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Scan barcode and retrieve invoice details
     */
    public function scanBarcode(Request $request)
    {
        $barcode = $request->input('barcode');

        if (!$barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode is required'
            ], 400);
        }

        $invoice = Invoice::with([
            'patientReport.patient',
            'patientReport.tests.category'
        ])->where('barcode_data', $barcode)->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found for this barcode'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'invoice' => [
                'id' => $invoice->id,
                'invoice_code' => $invoice->invoice_code,
                'total' => $invoice->total,
                'status' => $invoice->status,
                'patient' => [
                    'id' => $invoice->patientReport->patient->id,
                    'name' => $invoice->patientReport->patient->name,
                    'nid' => $invoice->patientReport->patient->nid,
                    'mobile' => $invoice->patientReport->patient->mobile,
                ],
                'report' => [
                    'id' => $invoice->patientReport->id,
                    'report_code' => $invoice->patientReport->report_code,
                    'amount' => $invoice->amount,
                    'discount' => $invoice->discount,
                ]
            ]
        ]);
    }

    /**
     * Download invoice PDF with barcode
     */
    public function downloadInvoice(Invoice $invoice)
    {
        $invoice->load(['patientReport.patient', 'patientReport.tests.category']);

        // You can use existing PDF logic from PatientController
        $pdf = \PDF::loadView('admin.invoices.pdf', compact('invoice'));

        return $pdf->download('invoice_' . $invoice->invoice_code . '.pdf');
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);

        return back()->with('success', 'Invoice marked as paid');
    }

    /**
     * Cancel invoice
     */
    public function cancel(Invoice $invoice)
    {
        $invoice->update(['status' => 'cancelled']);

        return back()->with('success', 'Invoice cancelled');
    }

    /**
     * Delete invoice
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('admin.invoices.index')->with('success', 'Invoice deleted');
    }
}
