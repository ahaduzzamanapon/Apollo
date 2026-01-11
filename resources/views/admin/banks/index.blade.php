@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Bank Management</h2>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Bank Selection & Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('banks.index') }}" method="GET" id="bankFilterForm" class="row align-items-end">
                    <!-- Bank Select -->
                    <div class="col-md-3">
                        <label class="fw-bold mb-1">Bank Account</label>
                        <select name="bank_id" class="form-control select2" onchange="document.getElementById('bankFilterForm').submit()">
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" {{ $activeBank && $activeBank->id == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->name }} ({{ $bank->account_no }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div class="col-md-2">
                        <label class="fw-bold mb-1">Type</label>
                        <select name="trans_type" class="form-control" onchange="document.getElementById('bankFilterForm').submit()">
                            <option value="All" {{ $type == 'All' ? 'selected' : '' }}>All Types</option>
                            <option value="Deposit" {{ $type == 'Deposit' ? 'selected' : '' }}>Deposit only</option>
                            <option value="Withdraw" {{ $type == 'Withdraw' ? 'selected' : '' }}>Withdraw only</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-2">
                        <label class="fw-bold mb-1">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" onchange="document.getElementById('bankFilterForm').submit()">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold mb-1">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" onchange="document.getElementById('bankFilterForm').submit()">
                    </div>

                    <!-- Actions -->
                    <div class="col-md-3">
                         <label class="d-block mb-1">&nbsp;</label>
                         <div class="d-flex gap-1">
                            <button type="button" class="btn btn-success flex-fill" onclick="exportData('csv')" title="CSV"><i class="bi bi-file-earmark-spreadsheet"></i></button>
                            <button type="button" class="btn btn-danger flex-fill" onclick="exportData('pdf')" title="PDF"><i class="bi bi-file-earmark-pdf"></i></button>
                            <button type="button" class="btn btn-primary flex-fill" data-bs-toggle="modal" data-bs-target="#addBankModal" title="Add Bank">
                                <i class="bi bi-plus-circle"></i>
                            </button>
                         </div>
                    </div>
                </form>
            </div>
        </div>

<script>
    function exportData(type) {
        let form = document.getElementById('bankFilterForm');
        let originalAction = form.action;
        let url = new URL(originalAction);
        
        // Append current params
        new FormData(form).forEach((value, key) => {
            url.searchParams.append(key, value);
        });
        url.searchParams.append('export', type);
        
        window.location.href = url.toString();
    }
</script>

        @if($activeBank)
            <!-- Bank Details -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Total Deposit</h5>
                            <h3>{{ number_format($total_deposit, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>Total Withdraw</h5>
                            <h3>{{ number_format($total_withdraw, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Current Balance</h5>
                            <h3>{{ number_format($balance, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions & Transactions -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transaction History - {{ $activeBank->name }}</h5>
                    <div>
                        <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#depositModal">
                            <i class="bi bi-arrow-down-circle"></i> Deposit
                        </button>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <i class="bi bi-arrow-up-circle"></i> Withdraw
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $trans)
                                    <tr>
                                        <td>{{ $trans->trans_date }}</td>
                                        <td>
                                            <span class="badge bg-{{ $trans->trans_type == 'Deposit' ? 'success' : 'danger' }}">
                                                {{ $trans->trans_type }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($trans->amount, 2) }}</td>
                                        <td>{{ $trans->note }}</td>
                                        <td>{{ $trans->user_id ?? 'Admin' }}</td> <!-- Assuming user relation exists or nullable -->
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan="2" class="text-end fw-bold">Total (Filtered):</td>
                                    <td colspan="3" class="fw-bold">
                                         <span class="text-success">Dep: {{ number_format($filtered_deposit, 2) }}</span>
                                         <span class="mx-2">|</span>
                                         <span class="text-danger">With: {{ number_format($filtered_withdraw, 2) }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">Please add a bank account to get started.</div>
        @endif
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

@if($activeBank)
<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Deposit to {{ $activeBank->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('banks.transaction.store') }}" method="POST">
                @csrf
                <input type="hidden" name="bank_id" value="{{ $activeBank->id }}">
                <input type="hidden" name="trans_type" value="Deposit">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" name="trans_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Note</label>
                        <input type="text" name="note" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Confirm Deposit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Withdraw from {{ $activeBank->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('banks.transaction.store') }}" method="POST">
                @csrf
                <input type="hidden" name="bank_id" value="{{ $activeBank->id }}">
                <input type="hidden" name="trans_type" value="Withdraw">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Date</label>
                        <input type="date" name="trans_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Note</label>
                        <input type="text" name="note" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Confirm Withdraw</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
</script>
@endsection
@endsection
