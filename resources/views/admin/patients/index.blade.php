@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Patient Reports / Billing</h2>
        <a href="{{ route('patients.create') }}" class="btn btn-primary mb-3">New Patient Entry</a>
        <div class="card card-fixed">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Report ID</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Mobile</th>
                                <th>Ref. Doctor</th>
                                <th>Total Amt</th>
                                <th>Paid</th>
                                <th>Due</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                            <tr>
                                <td>{{ $report->report_code }}</td>
                                <td>{{ $report->report_date }}</td>
                                <td>{{ $report->patient->name }}</td>
                                <td>{{ $report->patient->mobile }}</td>
                                <td>{{ $report->referenceDoctor->name ?? 'Self' }}</td>
                                <td>{{ $report->final_amount }}</td>
                                <td>{{ $report->paid_amount }}</td>
                                <td>
                                    @if($report->due_amount > 0)
                                        <span class="badge bg-danger">{{ $report->due_amount }}</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('patients.show', $report->id) }}" class="btn btn-sm btn-info">View/Print</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
