@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Accounts Report Daily Summary</h2>
            <div>
                <a href="{{ route('accounts.report', array_merge(request()->all(), ['export' => 'pdf'])) }}" class="btn btn-danger">
                    <i class="bi bi-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('accounts.report', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-success ms-2">
                    <i class="bi bi-file-excel"></i> Export CSV
                </a>
                <button onclick="window.print()" class="btn btn-secondary ms-2">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="card mb-4 no-print">
            <div class="card-body">
                <form action="{{ route('accounts.report') }}" method="GET" class="row align-items-end">
                    <div class="col-md-4">
                        <label>Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                     <div class="col-md-4">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="bg-success text-white">
                        <tr>
                            <th>Date</th>
                            <th>Note/Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
                            <td>
                                @if(isset($row['url']) && $row['url'])
                                    <a href="{{ $row['url'] }}" target="_blank" class="text-decoration-none fw-bold">
                                        {{ $row['description'] }} <i class="bi bi-box-arrow-up-right small"></i>
                                    </a>
                                @else
                                    {{ $row['description'] }}
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No data found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <!-- Spacer Row -->
                        <tr style="height: 30px; border: none;"><td colspan="3" style="border: none;"></td></tr>
                        
                        <!-- Total Income -->
                        <tr class="bg-warning">
                            <td colspan="2" class="text-end fw-bold">Total Income (All with paid or unpaid)</td>
                            <td class="text-end fw-bold">{{ number_format($grandTotalIncome, 2) }}</td>
                        </tr>
                        
                        <!-- Total Expenses -->
                        <tr class="bg-warning">
                             <td colspan="2" class="text-end fw-bold">All Total Expenses</td>
                            <td class="text-end fw-bold">{{ number_format($allTotalExpenses, 2) }}</td>
                        </tr>

                        <!-- Total Balance -->
                        <tr class="bg-warning">
                             <td colspan="2" class="text-end fw-bold">Total Balance Remain In Cash Now</td>
                            <td class="text-end fw-bold">{{ number_format($totalBalance, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-body { padding: 0 !important; }
        .bg-success { background-color: #198754 !important; color: white !important; -webkit-print-color-adjust: exact; }
        .bg-warning { background-color: #ffc107 !important; -webkit-print-color-adjust: exact; }
    }
</style>
@endsection
