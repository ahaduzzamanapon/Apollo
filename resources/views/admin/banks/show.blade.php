@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2>Bank Details: {{ $bank->name }}</h2>
                <p class="text-muted">Account No: {{ $bank->account_no }}</p>
            </div>
            <a href="{{ route('banks.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5>Total Deposit</h5>
                        <h3>{{ number_format($total_deposit, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h5>Total Withdraw</h5>
                        <h3>{{ number_format($total_withdraw, 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>Current Balance</h5>
                        <h3>{{ number_format($balance, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Transaction Form -->
        <div class="card mb-4">
            <div class="card-header">Add Transaction</div>
            <div class="card-body">
                <form action="{{ route('banks.transaction.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="bank_id" value="{{ $bank->id }}">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Date</label>
                            <input type="date" name="trans_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label>Type</label>
                            <select name="trans_type" class="form-control" required>
                                <option value="Deposit">Deposit</option>
                                <option value="Withdraw">Withdraw</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Amount</label>
                            <input type="number" name="amount" class="form-control" step="any" required>
                        </div>
                        <div class="col-md-3">
                            <label>Note (Optional)</label>
                            <input type="text" name="note" class="form-control">
                        </div>
                        <div class="col-md-12 mt-3 text-end">
                            <button type="submit" class="btn btn-primary">Save Transaction</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card">
            <div class="card-header">Transaction History</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Note</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bank->transactions as $trans)
                        <tr>
                            <td>{{ date('d M, Y', strtotime($trans->trans_date)) }}</td>
                            <td>
                                <span class="badge {{ $trans->trans_type == 'Deposit' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $trans->trans_type }}
                                </span>
                            </td>
                            <td>{{ $trans->note }}</td>
                            <td class="text-end">{{ number_format($trans->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
