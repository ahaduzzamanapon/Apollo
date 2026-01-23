<!DOCTYPE html>
<html>
<head>
    <title>Patient Reports</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2, .header p { margin: 0; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Apollo Digital Diagnostic Center</h2>
        <p>Patient Reports List</p>
        <p>Date: {{ date('d M, Y') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Date</th>
                <th>Patient Name</th>
                <th>Mobile</th>
                <th>Doctor</th>
                <th class="text-right">Total</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Due</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $total_amount = 0; $paid_amount = 0; $due_amount = 0;
            @endphp
            @foreach($reports as $report)
            @php 
                $total_amount += $report->final_amount; 
                $paid_amount += $report->paid_amount;
                $due_amount += $report->due_amount;
            @endphp
            <tr>
                <td>
                    {{ $report->daily_id ?? $report->id }}<br>
                    <small style="color: #777;">{{ $report->report_code }}</small>
                </td>
                <td>{{ date('d M, Y', strtotime($report->report_date)) }}</td>
                <td>{{ $report->patient->name }}</td>
                <td>{{ $report->patient->mobile }}</td>
                <td>{{ $report->referenceDoctor->name ?? 'Self' }}</td>
                <td class="text-right">{{ $report->final_amount }}</td>
                <td class="text-right">{{ $report->paid_amount }}</td>
                <td class="text-right">{{ $report->due_amount }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="5" class="text-right">Total:</th>
                <th class="text-right">{{ number_format($total_amount, 2) }}</th>
                <th class="text-right">{{ number_format($paid_amount, 2) }}</th>
                <th class="text-right">{{ number_format($due_amount, 2) }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>
