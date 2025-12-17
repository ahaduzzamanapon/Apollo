<!DOCTYPE html>
<html>
<head>
    <title>Expense Report</title>
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
        <p>Expense Report</p>
        <p>Date: {{ date('d M, Y') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Ledger</th>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $total_amount = 0; @endphp
            @foreach($expenses as $expense)
            @php $total_amount += $expense->amount; @endphp
            <tr>
                <td>{{ date('d M, Y', strtotime($expense->date)) }}</td>
                <td>{{ $expense->ledger->name }}</td>
                <td>{{ $expense->description }}</td>
                <td class="text-right">{{ $expense->amount }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th class="text-right">{{ number_format($total_amount, 2) }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>
