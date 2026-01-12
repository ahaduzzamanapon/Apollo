<!DOCTYPE html>
<html>
<head>
    <title>Bank Transactions</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .success { color: green; }
        .danger { color: red; }
        h2, h4 { margin: 0; }
        .header { margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $activeBank->name }} ({{ $activeBank->account_no }})</h2>
        <h4>Transaction History</h4>
        <p>Date: {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trans)
            <tr>
                <td>{{ $trans->trans_date }}</td>
                <td class="{{ $trans->trans_type == 'Deposit' ? 'success' : 'danger' }}">
                    {{ $trans->trans_type }}
                </td>
                <td>{{ $trans->note }}</td>
                <td class="text-end">{{ number_format($trans->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #eee; font-weight: bold;">
                <td colspan="3" class="text-end">Total Deposit (Filtered)</td>
                <td class="text-end success">{{ number_format($filtered_deposit, 2) }}</td>
            </tr>
            <tr style="background-color: #eee; font-weight: bold;">
                <td colspan="3" class="text-end">Total Withdraw (Filtered)</td>
                <td class="text-end danger">{{ number_format($filtered_withdraw, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
