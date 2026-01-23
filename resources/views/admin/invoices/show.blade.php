@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Invoice: {{ $invoice->invoice_code }}</h2>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Invoice Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Patient Information</h6>
                            <p>
                                <strong>Name:</strong> {{ $invoice->patientReport->patient->name }}<br>
                                <strong>NID:</strong> {{ $invoice->patientReport->patient->nid }}<br>
                                <strong>Mobile:</strong> {{ $invoice->patientReport->patient->mobile }}<br>
                                <strong>Age:</strong> {{ $invoice->patientReport->patient->age }} {{ $invoice->patientReport->patient->age_unit }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Invoice Information</h6>
                            <p>
                                <strong>Invoice Code:</strong> {{ $invoice->invoice_code }}<br>
                                <strong>Report Code:</strong> {{ $invoice->patientReport->report_code }}<br>
                                <strong>Date:</strong> {{ $invoice->created_at->format('Y-m-d H:i') }}<br>
                                <strong>Status:</strong>
                                <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <h6>Tests Performed</h6>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Test Name</th>
                                <th>Category</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->patientReport->tests as $test)
                                <tr>
                                    <td>{{ $test->category->name ?? 'N/A' }}</td>
                                    <td>{{ $test->category->category_type ?? 'N/A' }}</td>
                                    <td>{{ $test->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <h6>Cost Summary</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Amount:</strong></td>
                                    <td>{{ number_format($invoice->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Discount:</strong></td>
                                    <td>-{{ number_format($invoice->discount, 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Total Due:</strong></td>
                                    <td><strong>{{ number_format($invoice->total, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($invoice->remarks)
                        <div class="alert alert-info">
                            <strong>Remarks:</strong> {{ $invoice->remarks }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Barcode</h5>
                </div>
                <div class="card-body text-center">
                    @if($invoice->barcode)
                        <img src="{{ $invoice->barcode }}" alt="Invoice Barcode" style="max-width: 100%; height: auto;">
                        <p class="text-muted mt-2 small">{{ $invoice->barcode_data }}</p>
                    @else
                        <p class="text-muted">No barcode available</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.invoices.download', $invoice) }}" class="btn btn-success btn-sm w-100 mb-2">
                        <i class="fas fa-download"></i> Download PDF
                    </a>

                    @if($invoice->status !== 'paid')
                        <form action="{{ route('admin.invoices.paid', $invoice) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm w-100 mb-2" onclick="return confirm('Mark this invoice as paid?')">
                                <i class="fas fa-check"></i> Mark as Paid
                            </button>
                        </form>
                    @endif

                    @if($invoice->status !== 'cancelled')
                        <form action="{{ route('admin.invoices.cancel', $invoice) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100 mb-2" onclick="return confirm('Cancel this invoice?')">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary btn-sm w-100">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
