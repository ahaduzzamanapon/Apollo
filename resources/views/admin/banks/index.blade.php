@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Bank Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBankModal">
                <i class="bi bi-plus-circle"></i> Add New Bank
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Bank Name</th>
                            <th>Account No</th>
                            <th>Total Deposit</th>
                            <th>Total Withdraw</th>
                            <th>Current Balance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banks as $key => $bank)
                            @php
                                $balance = ($bank->total_deposit ?? 0) - ($bank->total_withdraw ?? 0);
                            @endphp
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $bank->name }}</td>
                                <td>{{ $bank->account_no }}</td>
                                <td class="text-end">{{ number_format($bank->total_deposit ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($bank->total_withdraw ?? 0, 2) }}</td>
                                <td class="text-end fw-bold">{{ number_format($balance, 2) }}</td>
                                <td>
                                    <a href="{{ route('banks.show', $bank->id) }}" class="btn btn-info btn-sm text-white">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <form action="{{ route('banks.destroy', $bank->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this bank?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Bank Modal -->
<div class="modal fade" id="addBankModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Bank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('banks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Bank Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Account Number</label>
                        <input type="text" name="account_no" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Bank</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
