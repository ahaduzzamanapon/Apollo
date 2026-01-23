<?php

/**
 * Invoice Barcode System Helper Functions
 *
 * Add these functions to your app for easier barcode operations
 *
 * Usage:
 * - generate_invoice_barcode($invoiceCode)
 * - get_invoice_by_barcode($barcodeData)
 * - create_invoice_with_barcode($patientReportId, $amount, $discount = 0)
 */

use App\Models\Invoice;
use App\Services\BarcodeService;

/**
 * Generate barcode for invoice code
 *
 * @param string $invoiceCode
 * @return array
 */
function generate_invoice_barcode($invoiceCode)
{
    return BarcodeService::generateBarcode($invoiceCode);
}

/**
 * Get invoice by barcode data
 *
 * @param string $barcodeData
 * @return Invoice|null
 */
function get_invoice_by_barcode($barcodeData)
{
    return Invoice::with(['patientReport.patient', 'patientReport.tests.category'])
        ->where('barcode_data', $barcodeData)
        ->first();
}

/**
 * Create invoice with barcode
 *
 * @param int $patientReportId
 * @param float $amount
 * @param float $discount
 * @param string|null $paymentMethod
 * @param string|null $remarks
 * @return Invoice|null
 */
function create_invoice_with_barcode($patientReportId, $amount, $discount = 0, $paymentMethod = null, $remarks = null)
{
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
        return null;
    }

    // Calculate total
    $amount = (float)$amount;
    $discount = (float)$discount;
    $total = $amount - $discount;

    // Create invoice
    return Invoice::create([
        'patient_report_id' => $patientReportId,
        'invoice_code' => $invoiceCode,
        'barcode' => $barcodeResult['barcode'],
        'barcode_data' => $barcodeResult['barcode_data'],
        'amount' => $amount,
        'discount' => $discount,
        'total' => $total,
        'payment_method' => $paymentMethod,
        'remarks' => $remarks,
        'status' => 'pending'
    ]);
}

/**
 * Get all invoices for a patient
 *
 * @param int $patientId
 * @return \Illuminate\Database\Eloquent\Collection
 */
function get_patient_invoices($patientId)
{
    return Invoice::with(['patientReport.patient'])
        ->whereHas('patientReport', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })
        ->get();
}

/**
 * Get pending invoices for a patient
 *
 * @param int $patientId
 * @return \Illuminate\Database\Eloquent\Collection
 */
function get_patient_pending_invoices($patientId)
{
    return Invoice::with(['patientReport.patient'])
        ->whereHas('patientReport', function ($query) use ($patientId) {
            $query->where('patient_id', $patientId);
        })
        ->where('status', 'pending')
        ->get();
}

/**
 * Calculate total due for patient
 *
 * @param int $patientId
 * @return float
 */
function get_patient_total_due($patientId)
{
    return Invoice::whereHas('patientReport', function ($query) use ($patientId) {
        $query->where('patient_id', $patientId);
    })
    ->where('status', 'pending')
    ->sum('total');
}

/**
 * Mark invoice as paid
 *
 * @param int $invoiceId
 * @return Invoice|null
 */
function mark_invoice_paid($invoiceId)
{
    $invoice = Invoice::find($invoiceId);
    if ($invoice) {
        $invoice->update(['status' => 'paid']);
    }
    return $invoice;
}

/**
 * Cancel invoice
 *
 * @param int $invoiceId
 * @return Invoice|null
 */
function cancel_invoice($invoiceId)
{
    $invoice = Invoice::find($invoiceId);
    if ($invoice) {
        $invoice->update(['status' => 'cancelled']);
    }
    return $invoice;
}

/**
 * Get invoice barcode HTML
 *
 * @param int $invoiceId
 * @return string
 */
function get_invoice_barcode_html($invoiceId)
{
    $invoice = Invoice::find($invoiceId);
    if ($invoice && $invoice->barcode) {
        return '<img src="' . $invoice->barcode . '" alt="' . $invoice->invoice_code . '" style="max-width: 100%; height: auto;" />';
    }
    return '';
}

/**
 * Generate invoice summary for display
 *
 * @param int $invoiceId
 * @return array
 */
function get_invoice_summary($invoiceId)
{
    $invoice = Invoice::with(['patientReport.patient'])->find($invoiceId);

    if (!$invoice) {
        return [];
    }

    return [
        'invoice_code' => $invoice->invoice_code,
        'patient_name' => $invoice->patientReport->patient->name,
        'amount' => number_format($invoice->amount, 2),
        'discount' => number_format($invoice->discount, 2),
        'total' => number_format($invoice->total, 2),
        'status' => $invoice->status,
        'created_at' => $invoice->created_at->format('Y-m-d H:i'),
    ];
}

/**
 * Check if invoice is paid
 *
 * @param int $invoiceId
 * @return bool
 */
function is_invoice_paid($invoiceId)
{
    $invoice = Invoice::find($invoiceId);
    return $invoice && $invoice->status === 'paid';
}

/**
 * Check if invoice is pending
 *
 * @param int $invoiceId
 * @return bool
 */
function is_invoice_pending($invoiceId)
{
    $invoice = Invoice::find($invoiceId);
    return $invoice && $invoice->status === 'pending';
}

/**
 * Get recent invoices
 *
 * @param int $limit
 * @return \Illuminate\Database\Eloquent\Collection
 */
function get_recent_invoices($limit = 10)
{
    return Invoice::with(['patientReport.patient'])
        ->latest()
        ->limit($limit)
        ->get();
}

/**
 * Get invoices by status
 *
 * @param string $status
 * @param int $limit
 * @return \Illuminate\Database\Eloquent\Collection
 */
function get_invoices_by_status($status, $limit = 10)
{
    return Invoice::with(['patientReport.patient'])
        ->where('status', $status)
        ->latest()
        ->limit($limit)
        ->get();
}
