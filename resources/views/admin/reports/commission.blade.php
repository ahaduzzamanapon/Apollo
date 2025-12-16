@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Doctor's Commission Report</h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('commission.index') }}" method="GET" class="row">
                    <div class="col-md-4">
                        <select name="doctor_id" class="form-control">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Doctor Name</th>
                            <th>Report ID</th>
                            <th>Test Name</th>
                            <th>Honorarium (TK)</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $commission)
                        <tr>
                            <td>{{ $commission->report->referenceDoctor->name }}</td>
                            <td>{{ $commission->report->report_code }}</td>
                            <td>{{ $commission->category->test_name }}</td>
                            <td>{{ $commission->commission_amount }}</td>
                            <td>{{ $commission->report->report_date }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total Honorarium</td>
                            <td class="fw-bold">{{ $totalCommission }} TK</td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                {{ $commissions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
