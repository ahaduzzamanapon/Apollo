@extends('admin.layouts.app')

@section('content')
<style>
    .table tbody td, .table thead th {
        padding : 2px;
    }
    .barcode-container img {
        max-width: 100%;
        height: auto;
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
                        <h4 class="border-bottom">Money Receipt</h4>
                    </div>

                    <!-- Barcode Section -->
                    <div style="margin-bottom: 10px;">
                        <span>Bill No: <strong>{{ $report->report_code }}</strong></span>
                        {!! \App\Services\BarcodeService::getHtmlImg($report->report_code, ['style' => 'height: 30px;width: 200px;float: right']) !!}
                    </div>

                    <div class="row mb-4" style="font-size:16px;border: 1px dashed #000;line-height: 15px;padding-top: 8px;height:auto">
                        <div class="col-md-12">
                            <div class="d-flex flex-row">
                                <div class="d-flex flex-column">
                                    <div class="mb-2">
                                        <span>ID No:</span> <strong>{{ $report->daily_id ?? $report->id }}</strong>
                                        <span style="margin-left: 45px">Mobile:</span> <strong> {{ $report->patient->mobile }}</strong>
                                        <span style="margin-left: 40px">Date & Time:</span> <strong>{{ $report->report_date.date(' h:i A',strtotime($report->created_at)) }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <span>Patient Name:</span> <strong>{{ $report->patient->name }}</strong>
                                        <span style="margin-left: 15px">Age:</span> <strong>{{ $report->patient->age }} {{ $report->patient->age_unit }}</strong>
                                        <span style="margin-left: 15px">Gender:</span> <strong>{{ $report->patient->gender }}</strong>
                                    </div>
                                </div>
   
                            </div>
                            <div class="d-flex flex-row mb-2">
                                <div class="d-flex flex-column">
                                    <span>Ref. by: <strong>{{ $report->referenceDoctor->name ?? 'Self' }}</strong></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="font-size:17px">Test Name</th>
                                <th style="font-size: 17px" class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->tests as $test)
                            <tr>
                                <td  style="font-weight: bold;font-size:16px">{{ $test->category->test_name }}</td>
                                <td  style="font-weight: bold;font-size:16px" class="text-end">{{ $test->price }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th class="text-end" style="font-size:16px">Total Amount</th>
                                <th class="text-end" style="font-size:16px">{{ $report->total_amount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end" style="font-size:16px">Discount</th>
                                <th class="text-end" style="font-size:16px">{{ $report->discount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end" style="font-size:16px">Net Payable</th>
                                <th class="text-end" style="font-size:16px">{{ $report->final_amount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end" style="font-size:16px">Paid Amount</th>
                                <th class="text-end" style="font-size:16px">{{ $report->paid_amount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end" style="font-size:16px">Due Amount</th>
                                <th class="text-end" style="font-size:16px">{{ $report->due_amount }}</th>
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
                                <td style="font-size:16px">{{ $payment->created_at->format('Y-m-d h:i A') }}</td>
                                <td style="font-size:16px">{{ $payment->payment_method }}</td>
                                <td style="font-size:16px" class="fw-bold">{{ $payment->amount }}</td>
                                <td style="font-size:16px" class="text-danger">{{ $payment->discount > 0 ? $payment->discount : '-' }}</td>
                                <td style="font-size:16px">{{ $payment->collectedBy->name ?? 'N/A' }}</td>
                                <td style="font-size:16px;white-space:pre-line">{{ $payment->remarks }}</td>
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
                        <button type="button" class="btn btn-sm btn-success me-2" onclick="openPaymentModal({{ $report->id }}, '{{ $report->report_code }}', {{ $report->due_amount }})">
                            <i class="bi bi-cash"></i> Make Payment
                        </button>
                        @endif
                        <a href="{{ route('patients.download_invoice', $report->id) }}" class="btn btn-sm btn-danger me-2"><i class="bi bi-file-earmark-pdf"></i> Download PDF</a>
                        <a href="{{ route('patients.print_invoice', $report->id) }}" target="_blank" class="btn btn-sm btn-danger me-2"><i class="bi bi-printer"></i> Print Invoice</a>
                        <a href="{{ route('admin.patients.index') }}" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left"></i> Back to List</a>
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
