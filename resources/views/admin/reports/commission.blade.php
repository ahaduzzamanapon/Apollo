@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Doctor's Commission Report</h2>
        
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('commission.index') }}" method="GET" class="row align-items-end">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="col-md-3">
                        <label>Doctor</label>
                        <select name="doctor_id" class="form-control">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                     <div class="col-md-3">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
                <a class="nav-link {{ $status == 'pending' ? 'active' : '' }}" href="{{ route('commission.index', array_merge(request()->all(), ['status' => 'pending'])) }}">
                    Pending Approval
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $status == 'approved' ? 'active' : '' }}" href="{{ route('commission.index', array_merge(request()->all(), ['status' => 'approved'])) }}">
                    Approved History
                </a>
            </li>
        </ul>

        <div class="card">
            <div class="card-body">
                @if($status == 'pending')
                <form action="{{ route('commission.approve') }}" method="POST" id="approvalForm">
                    @csrf
                @endif

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            @if($status == 'pending')
                            <th width="40"><input type="checkbox" id="selectAll"></th>
                            @endif
                            <th>Date</th>
                            <th>Report ID</th>
                            <th>Doctor Name</th>
                            <th>Test Name</th>
                            <th>Price</th>
                            <th>Comm. (Calc)</th>
                            @if($status == 'pending')
                            <th>Approval Amount</th>
                            @else
                            <th>Approved Amount</th>
                            <th>Approved Date</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $commission)
                        <tr>
                            @if($status == 'pending')
                            <td>
                                <input type="checkbox" name="selected_commissions[]" value="{{ $commission->id }}" class="row-checkbox">
                            </td>
                            @endif
                            <td>{{ $commission->report->report_date }}</td>
                            <td>{{ $commission->report->report_code }}</td>
                            <td>{{ $commission->report->referenceDoctor->name }}</td>
                            <td>{{ $commission->category->test_name }}</td>
                            <td>{{ $commission->price }}</td>
                            <td>{{ $commission->commission_amount }}</td>
                            
                            @if($status == 'pending')
                            <td>
                                <input type="number" step="0.01" name="approved_amounts[{{ $commission->id }}]" class="form-control form-control-sm" value="{{ $commission->commission_amount }}"
                                       onfocus="if(this.value=='0' || this.value=='0.00') this.value=''" 
                                       onblur="if(this.value=='') this.value='0.00'">
                            </td>
                            @else
                            <td class="fw-bold text-success">{{ $commission->approved_amount }}</td>
                            <td>{{ $commission->approved_at }}</td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $status == 'pending' ? '8' : '8' }}" class="text-center text-muted">No records found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="{{ $status == 'pending' ? '7' : '6' }}" class="text-end">Total</th>
                            <th>
                                @if($status == 'pending')
                                    {{ $totalCommission }} TK (Calc)
                                @else
                                    {{ $totalApproved }} TK
                                @endif
                            </th>
                            @if($status == 'approved') <th></th> @endif
                        </tr>
                    </tfoot>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        {{ $commissions->appends(request()->all())->links() }}
                    </div>
                    <div>
                        @if($status == 'pending')
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-check-circle"></i> Approve Selected
                            </button>
                             <a href="{{ route('commission.index', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success ms-2">
                                <i class="bi bi-file-excel"></i> Export Pending CSV
                            </a>
                        @else
                             <a href="{{ route('commission.index', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-outline-danger">
                                <i class="bi bi-file-pdf"></i> Export PDF
                            </a>
                             <a href="{{ route('commission.index', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-outline-success">
                                <i class="bi bi-file-excel"></i> Export CSV
                            </a>
                        @endif
                    </div>
                </div>

                @if($status == 'pending')
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>
@endpush
@endsection
