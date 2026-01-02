<!DOCTYPE html>
<html>
<head>
    <title>Commission Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f4f4f4; }
        .text-end { text-align: right; }
        h2, h4 { text-align: center; margin: 0; }
        .header { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Apollo Digital Diagnostic Center</h2>
        <h4>Doctor's Commission Report ({{ ucfirst($status) }})</h4>
        <p style="text-align: center;">Generated on: {{ date('d M Y, h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Report ID</th>
                <th>Doctor Name</th>
                <th>Test Name</th>
                <th>Price</th>
                <th>Comm. (Calc)</th>
                @if($status == 'approved')
                <th>Approved Amt</th>
                <th>Approved Date</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($commissions as $commission)
            <tr>
                <td>{{ $commission->report->report_date }}</td>
                <td>{{ $commission->report->report_code }}</td>
                <td>{{ $commission->report->referenceDoctor->name }}</td>
                <td>{{ $commission->category->test_name }}</td>
                <td>{{ $commission->price }}</td>
                <td>{{ $commission->commission_amount }}</td>
                @if($status == 'approved')
                <td>{{ $commission->approved_amount }}</td>
                <td>{{ $commission->approved_at }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-end">Total</th>
                <th>{{ $status == 'pending' ? $commissions->sum('commission_amount') : '' }}</th>
                @if($status == 'approved')
                <th>{{ $commissions->sum('approved_amount') }}</th>
                <th></th>
                @endif
            </tr>
        </tfoot>
    </table>
</body>
</html>
