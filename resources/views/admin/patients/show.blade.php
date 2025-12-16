@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2>Apollo Digital Diagnostic Center</h2>
                        <p>Pirgang, Thakurgaon</p>
                        <h4>Money Receipt</h4>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Bill No:</strong> {{ $report->report_code }}</p>
                            <p><strong>Date:</strong> {{ $report->report_date }}</p>
                            <p><strong>Patient Name:</strong> {{ $report->patient->name }}</p>
                            <p><strong>Mobile:</strong> {{ $report->patient->mobile }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p><strong>Age/Gender:</strong> {{ $report->patient->age }} / {{ $report->patient->gender }}</p>
                            <p><strong>Ref. Doctor:</strong> {{ $report->referenceDoctor->name ?? 'Self' }}</p>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Test Name</th>
                                <th class="text-end">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->tests as $test)
                            <tr>
                                <td>{{ $test->category->test_name }}</td>
                                <td class="text-end">{{ $test->price }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th class="text-end">Total Amount</th>
                                <th class="text-end">{{ $report->total_amount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end">Discount</th>
                                <th class="text-end">{{ $report->discount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end">Net Payable</th>
                                <th class="text-end">{{ $report->final_amount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end">Paid Amount</th>
                                <th class="text-end">{{ $report->paid_amount }}</th>
                            </tr>
                             <tr>
                                <th class="text-end">Due Amount</th>
                                <th class="text-end">{{ $report->due_amount }}</th>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="text-center mt-5">
                        <button onclick="window.print()" class="btn btn-primary no-print">Print Invoice</button>
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary no-print">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none; }
        .card { border: none; }
    }
</style>
@endsection
