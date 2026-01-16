@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Patient Test List</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Patient Name</th>
                                <th>Age</th>
                                <th>Phone Number</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($patients as $test)
                                {{-- @dd($test)
                                 --}}
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $test->patient->name }}</td>
                                    <td>{{ $test->patient->age }}</td>
                                    <td>{{ $test->patient->mobile }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('admin.patients.test_entry', $test->id) }}" class="btn btn-primary btn-sm" title="Entry/View">
                                            <i class="fas fa-edit"></i> Entry
                                        </a>
                                        <a href="{{ route('patient.test.print', $test->id) }}" target="_blank" class="btn btn-success btn-sm" title="Print">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('patient.test.pdf', $test->id) }}" class="btn btn-danger btn-sm" title="PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
