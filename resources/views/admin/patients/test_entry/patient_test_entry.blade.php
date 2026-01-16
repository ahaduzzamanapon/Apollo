@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            {{-- Patient Info --}}
            @php
                $patient = $patients->first();
            @endphp

            <div class="card mb-3">
                <div class="card-body">
                    <strong>Patient:</strong> {{ $patient->name }} <br>
                    <strong>Age:</strong> {{ $patient->age }} {{ $patient->age_unit }} <br>
                    <strong>Gender:</strong> {{ $patient->gender }} <br>
                    <strong>Mobile:</strong> {{ $patient->mobile }}
                </div>
            </div>

            {{-- Test List --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Patient Test List</h5>
                    <div>
                        <a href="{{ route('patient.test.print', $reportId) }}" target="_blank" class="btn btn-success btn-sm">
                            <i class="fas fa-print"></i> Print
                        </a>
                        <a href="{{ route('patient.test.pdf', $reportId) }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-file-pdf"></i> PDF
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('patient.test.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">

                        @php
                            // Step 1: Category wise
                            $categories = $patients->groupBy('test_category_name');
                        @endphp

                        @forelse($categories as $categoryName => $categoryRows)

                            {{-- Category Name --}}
                            <h5 class="text-primary mt-3">
                                {{ $categoryName ?? 'Uncategorized' }}
                            </h5>

                            @php
                                // Step 2: Test wise under category
                                $tests = $categoryRows->groupBy('test_name');
                            @endphp

                            @foreach($tests as $testName => $rows)

                                {{-- Test Name --}}
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong>{{ $testName }}</strong>
                                    </div>

                                    {{-- Parameters Table --}}
                                    <div class="card-body p-0">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th>Parameter</th>
                                                    <th>Result</th>
                                                    <th>Unit</th>
                                                    <th>Reference Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($rows as $row)
                                                    @php
                                                        $currentTestResult = $savedResults[$row->test_id] ?? null;
                                                        $resultData = $currentTestResult ? json_decode($currentTestResult->resilt, true) : [];
                                                        $val = $resultData[$row->field_id] ?? '';
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $row->perameter }}</td>
                                                        <td>
                                                            <input type="text" 
                                                                   name="results[{{ $row->test_id }}][{{ $row->field_id }}]" 
                                                                   class="form-control form-control-sm"
                                                                   value="{{ $val }}">
                                                        </td>
                                                        <td>{{ $row->unit }}</td>
                                                        <td>{{ $row->ref_val }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            @endforeach

                        @empty
                            <p class="text-muted text-center">
                                No tests found
                            </p>
                        @endforelse

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">Save Results</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
