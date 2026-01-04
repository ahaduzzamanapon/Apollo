@extends('admin.layouts.app')

@section('content')
<style>
    .table tbody td, .table thead th {
        padding : 2px;
    }
    </style>
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-1">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        {{-- @include('admin.reports.center_header')
                        h4 --}}
                        <h4 class="border-bottom">Invoice</h4>
                    </div>

                    <div class="row mb-4" style="font-size:12px;border: 1px solid;line-height: 0px;padding-top: 25px;height:auto">
                        <div class="col-md-6">
                            <p><strong>Bill No:</strong> {{ $report->report_code }}</p>
                            <p><strong>Date:</strong> {{ $report->report_date }}</p>
                            <p><strong>Patient Name:</strong> {{ $report->patient->name }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            
                            <p><strong>Gender:</strong>  {{ $report->patient->gender }}</p>
                            <p><strong>Age:</strong> {{ $report->patient->age }} {{ $report->patient->age_unit }}</p>
                            <p><strong>Mobile:</strong> {{ $report->patient->mobile }}</p>
                        </div>
                        <div class="col-md-12" style="margin-top:-10px">
                            <p style="line-height: 18px"><strong>Ref. Doctor: DR.</strong> {{ $report->referenceDoctor->name ?? 'Self' }}</p>
                        </div>    
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="font-size:12px">Test Name</th>
                                <th style="font-size: 12px" class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->tests as $test)
                            <tr>
                                <td  style="font-size:11px">{{ $test->category->test_name }}</td>
                                <td style="font-size:11px" class="text-end">{{ $test->price }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th class="text-end" style="font-size:11px">Total Amount</th>
                                <th class="text-end" style="font-size:11px">{{ $report->total_amount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end" style="font-size:11px">Discount</th>
                                <th class="text-end" style="font-size:11px">{{ $report->discount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end" style="font-size:11px">Net Payable</th>
                                <th class="text-end" style="font-size:11px">{{ $report->final_amount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end" style="font-size:11px">Paid Amount</th>
                                <th class="text-end" style="font-size:11px">{{ $report->paid_amount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end" style="font-size:11px">Due Amount</th>
                                <th class="text-end" style="font-size:11px">{{ $report->due_amount }}</th>
                            </tr>
                        </tbody>
                        </tbody>
                    </table>

                    <!-- Payment History -->
                    <h5 class="mt-4 mb-3">Payment History</h5>
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th style="font-size:12px" class="text-center">Date</th>
                                <th style="font-size:12px" class="text-center">Method</th>
                                <th style="font-size:12px" class="text-center">Amount</th>
                                <th style="font-size:12px" class="text-center">Discount</th>
                                <th style="font-size:12px" class="text-center">Collected By</th>
                                <th style="font-size:12px" class="text-center">Remarks</th>
                            </tr>
                        </thead>
                        <tbody >
                            @forelse($report->payments as $payment)
                            <tr>
                                <td style="font-size:11px">{{ $payment->created_at->format('Y-m-d h:i A') }}</td>
                                <td style="font-size:11px">{{ $payment->payment_method }}</td>
                                <td style="font-size:11px" class="fw-bold">{{ $payment->amount }}</td>
                                <td style="font-size:11px" class="text-danger">{{ $payment->discount > 0 ? $payment->discount : '-' }}</td>
                                <td style="font-size:11px">{{ $payment->collectedBy->name ?? 'N/A' }}</td>
                                <td style="font-size:11px;white-space:pre-line">{{ $payment->remarks }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No payments recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="text-center mt-5 no-print">
                        @if($report->due_amount > 0)
                        <button type="button" class="btn btn-success me-2" onclick="openPaymentModal({{ $report->id }}, '{{ $report->report_code }}', {{ $report->due_amount }})">
                            <i class="bi bi-cash"></i> Make Payment
                        </button>
                        @endif
                        <a href="{{ route('patients.download_invoice', $report->id) }}" class="btn btn-danger me-2"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.patients.payment_modal')

<style>
    @media print {
        .no-print { display: none; }
        .card { border: none; }
    }
</style>
@endsection
