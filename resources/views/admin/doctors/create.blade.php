@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>Add New Doctor</h2>
        <form action="{{ route('admin.doctors.store') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header">Basic Info</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Honorarium Settings</div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Report Category</th>
                                <th>Test Name</th>
                                <th>Fixed Amount (TK)</th>
                                <th>Percentage (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportCategories as $category)
                            @php
                                $prefillAmount = '';
                                $prefillPercent = '';
                                if(isset($latestDoctor)) {
                                    $hon = $latestDoctor->honorariums->where('report_category_id', $category->id)->first();
                                    if($hon) {
                                        $prefillAmount = $hon->amount > 0 ? $hon->amount : '';
                                        $prefillPercent = $hon->percentage > 0 ? $hon->percentage : '';
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $category->category_name }}</td>
                                <td>{{ $category->test_name }}</td>
                                <td>
                                    <input type="number" step="0.01" name="honorariums[{{ $category->id }}][amount]" class="form-control" placeholder="0" value="{{ $prefillAmount }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="honorariums[{{ $category->id }}][percentage]" class="form-control" placeholder="0" value="{{ $prefillPercent }}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Save Doctor</button>
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
