<!DOCTYPE html>
<html>
<head>
    <title>Accounts Report Daily Summary</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f4f4f4; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        h2, h4 { text-align: center; margin: 0; }
        .header { margin-bottom: 20px; }
        .total-row td { font-weight: bold; background-color: #fff3cd; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Apollo Digital Diagnostic Center</h2>
        <h4>Accounts Report Daily Summary</h4>
        <p class="text-center">
            Period: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Note/Description</th>
                <th class="text-end">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
                <td>{{ $row['description'] }}</td>
                <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-end">Total Income (All with paid or unpaid)</td>
                <td class="text-end">{{ number_format($grandTotalIncome, 2) }}</td>
            </tr>
            <tr class="total-row">
                 <td colspan="2" class="text-end">All Total Expenses</td>
                <td class="text-end">{{ number_format($allTotalExpenses, 2) }}</td>
            </tr>
            <tr class="total-row">
                 <td colspan="2" class="text-end">Total Balance Remain In Cash Now</td>
                <td class="text-end">{{ number_format($totalBalance, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
