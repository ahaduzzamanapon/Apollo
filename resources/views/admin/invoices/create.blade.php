@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Create Invoice</h2>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Patient Report: {{ $report->report_code }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.invoices.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="patient_report_id" value="{{ $report->id }}">

                        <div class="form-group mb-3">
                            <label for="patientName">Patient Name</label>
                            <input type="text" class="form-control" value="{{ $report->patient->name }}" disabled>
                        </div>

                        <div class="form-group mb-3">
                            <label for="reportCode">Report Code</label>
                            <input type="text" class="form-control" value="{{ $report->report_code }}" disabled>
                        </div>

                        <div class="form-group mb-3">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" id="amount" name="amount" class="form-control" step="0.01" min="0" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="discount">Discount</label>
                            <input type="number" id="discount" name="discount" class="form-control" step="0.01" min="0" value="0">
                        </div>

                        <div class="form-group mb-3">
                            <label for="paymentMethod">Payment Method</label>
                            <select name="payment_method" class="form-control">
                                <option value="">-- Select Payment Method --</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="check">Check</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="online">Online</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Invoice with Barcode
                            </button>
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Patient Information</h5>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Name:</strong> {{ $report->patient->name }}<br>
                        <strong>NID:</strong> {{ $report->patient->nid }}<br>
                        <strong>Mobile:</strong> {{ $report->patient->mobile }}<br>
                        <strong>Age:</strong> {{ $report->patient->age }} {{ $report->patient->age_unit }}<br>
                        <strong>Gender:</strong> {{ ucfirst($report->patient->gender) }}
                    </p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5>Tests</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($report->tests as $test)
                            <li class="list-group-item">
                                {{ $test->category->name ?? 'N/A' }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
