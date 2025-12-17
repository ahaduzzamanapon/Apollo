<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $report->report_code }}</title>
    <style>
        @page { margin: 15px; }
        body { font-family: sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; font-weight: bold; }
        .header p { margin: 2px 0; }
        .invoice-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; text-align: center; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 2px; vertical-align: top; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 4px; }
        .items-table th { background-color: #f8f9fa; text-align: left; }
        .text-end { text-align: right; }
        
        .totals-table { width: 45%; margin-left: auto; border-collapse: collapse; }
        .totals-table th, .totals-table td { border: 1px solid #ddd; padding: 4px; }
        
        .footer { width: 100%; text-align: center; font-size: 10px; color: #666; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Apollo Digital Diagnostic Center</h2>
        <p>Pirgang, Thakurgaon | Mobile: 01712-345678</p> 
    </div>

    <div class="invoice-title">MONEY RECEIPT</div>

    <table class="info-table">
        <tr>
            <td width="55%">
                <strong>Bill No:</strong> {{ $report->report_code }}<br>
                <strong>Name:</strong> {{ $report->patient->name }}<br>
                <strong>Mobile:</strong> {{ $report->patient->mobile }}
            </td>
            <td width="45%" class="text-end">
                <strong>Date:</strong> {{ date('d M, Y', strtotime($report->report_date)) }}<br>
                <strong>Age/Gender:</strong> {{ $report->patient->age }}Y / {{ $report->patient->gender }}<br>
                <strong>Ref:</strong> {{ $report->referenceDoctor->name ?? 'Self' }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Test Name</th>
                <th class="text-end" width="20%">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->tests as $test)
            <tr>
                <td>{{ $test->category->test_name }}</td>
                <td class="text-end">{{ number_format($test->price, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <th>Total</th>
            <td class="text-end">{{ number_format($report->total_amount, 0) }}</td>
        </tr>
        <tr>
            <th>Discount</th>
            <td class="text-end">{{ number_format($report->discount, 0) }}</td>
        </tr>
         <tr>
            <th>Net Payable</th>
            <td class="text-end fw-bold">{{ number_format($report->final_amount, 0) }}</td>
        </tr>
        <tr>
            <th>Paid</th>
            <td class="text-end">{{ number_format($report->paid_amount, 0) }}</td>
        </tr>
        <tr>
            <th>Due</th>
            <td class="text-end">{{ number_format($report->due_amount, 0) }}</td>
        </tr>
    </table>
    
    <div class="footer">
        Computer Generated Invoice. Printed: {{ date('d M, Y h:i A') }}
    </div>
</body>
</html>
