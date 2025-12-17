@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 text-danger">Patient Due List</h2>
            <div>
                <a href="{{ route('patients.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> New Patient Entry
                </a>
            </div>
        </div>

        <div class="card card-fixed shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Report ID</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Mobile</th>
                                <th>Ref. Doctor</th>
                                <th>Total Amt</th>
                                <th>Paid</th>
                                <th class="text-danger">Due Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr>
                                <td>{{ $report->report_code }}</td>
                                <td>{{ $report->report_date }}</td>
                                <td>{{ $report->patient->name }}</td>
                                <td>{{ $report->patient->mobile }}</td>
                                <td>{{ $report->referenceDoctor->name ?? 'Self' }}</td>
                                <td>{{ $report->final_amount }}</td>
                                <td>{{ $report->paid_amount }}</td>
                                <td>
                                    <span class="badge bg-danger fs-6">{{ $report->due_amount }} TK</span>
                                </td>
                                <td>
                                    <a href="{{ route('patients.show', $report->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i> View/Pay
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Thinking positive! No outstanding dues found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top">
                {{ $reports->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
