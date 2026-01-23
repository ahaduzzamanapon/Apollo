<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 16px;
        }

        .barcode-section {
            text-align: right;
            margin-bottom: 20px;
        }

        .barcode-section img {
            max-width: 200px;
            height: auto;
        }

        .barcode-text {
            font-size: 10px;
            margin-top: 5px;
            color: #666;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 8px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        .patient-info, .invoice-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }

        .patient-info {
            margin-right: 4%;
        }

        .info-row {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 80px;
        }

        .value {
            display: inline-block;
        }

        table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
            font-size: 10px;
        }

        table th {
            background-color: #f5f5f5;
            padding: 5px;
            text-align: left;
            font-weight: bold;
            border-bottom: 1px solid #333;
        }

        table td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }

        table tr:last-child td {
            border-bottom: 1px solid #333;
        }

        .summary {
            width: 50%;
            margin-left: auto;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }

        .summary-row.total {
            font-weight: bold;
            font-size: 12px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

        .footer {
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>{{ config('app.name', 'Apollo') }}</h1>
                <p style="color: #666; font-size: 10px;">Medical & Diagnostic Center</p>
            </div>
        </div>

        <!-- Barcode Section -->
        <div class="barcode-section">
            <strong>{{ $invoice->invoice_code }}</strong><br>
            @if($invoice->barcode)
                <img src="{{ $invoice->barcode }}" alt="Barcode">
                <div class="barcode-text">{{ $invoice->barcode_data }}</div>
            @endif
        </div>

        <!-- Patient & Invoice Info -->
        <div class="section">
            <div class="patient-info">
                <div class="section-title">PATIENT INFORMATION</div>
                <div class="info-row">
                    <span class="label">Name:</span>
                    <span class="value">{{ $invoice->patientReport->patient->name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">NID:</span>
                    <span class="value">{{ $invoice->patientReport->patient->nid ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Mobile:</span>
                    <span class="value">{{ $invoice->patientReport->patient->mobile ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Age:</span>
                    <span class="value">{{ $invoice->patientReport->patient->age }} {{ $invoice->patientReport->patient->age_unit }}</span>
                </div>
            </div>

            <div class="invoice-info">
                <div class="section-title">INVOICE INFORMATION</div>
                <div class="info-row">
                    <span class="label">Invoice #:</span>
                    <span class="value">{{ $invoice->invoice_code }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Report #:</span>
                    <span class="value">{{ $invoice->patientReport->report_code }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Date:</span>
                    <span class="value">{{ $invoice->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Status:</span>
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ strtoupper($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tests -->
        @if($invoice->patientReport->tests->count() > 0)
            <div class="section">
                <div class="section-title">TESTS PERFORMED</div>
                <table>
                    <thead>
                        <tr>
                            <th>Test Name</th>
                            <th>Category</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->patientReport->tests as $test)
                            <tr>
                                <td>{{ $test->category->name ?? 'N/A' }}</td>
                                <td>{{ $test->category->category_type ?? 'N/A' }}</td>
                                <td>{{ $test->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Summary -->
        <div class="section">
            <div class="summary">
                <div class="summary-row">
                    <span>Amount:</span>
                    <span>{{ number_format($invoice->amount, 2) }}</span>
                </div>
                <div class="summary-row">
                    <span>Discount:</span>
                    <span>-{{ number_format($invoice->discount, 2) }}</span>
                </div>
                <div class="summary-row total">
                    <span>Total Due:</span>
                    <span>{{ number_format($invoice->total, 2) }}</span>
                </div>
            </div>
        </div>

        @if($invoice->remarks)
            <div class="section">
                <div class="section-title">REMARKS</div>
                <p style="font-size: 11px;">{{ $invoice->remarks }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for visiting {{ config('app.name', 'Apollo') }}</p>
            <p>For inquiries, please contact us.</p>
            <p style="margin-top: 10px; font-size: 9px;">Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
