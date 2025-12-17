@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Accounts Report</h2>
            <form action="{{ route('accounts.report') }}" method="GET" class="d-flex gap-2">
                <input type="date" name="date" class="form-control" value="{{ $date }}">
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5>Today's Income</h5>
                        <h3>{{ number_format($total_income, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h5>Today's Expense</h5>
                        <h3>{{ number_format($total_expense, 2) }}</h3>
                    </div>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h5>Bank Deposit</h5>
                        <h3>{{ number_format($total_deposit, 2) }}</h3>
                    </div>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card bg-info text-dark">
                    <div class="card-body text-center">
                        <h5>Bank Withdraw</h5>
                        <h3>{{ number_format($total_withdraw, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 bg-primary text-white">
            <div class="card-body text-center">
                <h4>Today's Cash Balance (Income + Withdraw - Expense - Deposit)</h4>
                <h2>{{ number_format(($total_income + $total_withdraw) - ($total_expense + $total_deposit), 2) }}</h2>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Detailed Transactions</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trans)
                        <tr>
                            <td>{{ date('h:i A', strtotime($trans['created_at'])) }}</td>
                            <td>{{ $trans['description'] }}</td>
                            <td>
                                <span class="badge {{ $trans['type'] == 'Income' || $trans['type'] == 'Withdraw' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $trans['type'] }}
                                </span>
                            </td>
                            <td class="text-end">{{ number_format($trans['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                        @if(empty($transactions))
                            <tr><td colspan="4" class="text-center">No transactions found for this date.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
