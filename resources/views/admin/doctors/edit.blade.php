@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>Edit Doctor</h2>
        <form action="{{ route('admin.doctors.update', $doctor->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card mb-4">
                <div class="card-header">Basic Info</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $doctor->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Mobile</label>
                            <input type="text" name="mobile" class="form-control" value="{{ $doctor->mobile }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $doctor->email }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Address</label>
                            <textarea name="address" class="form-control">{{ $doctor->address }}</textarea>
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
                                $hon = $doctor->honorariums->where('report_category_id', $category->id)->first();
                            @endphp
                            <tr>
                                <td>{{ $category->category_name }}</td>
                                <td>{{ $category->test_name }}</td>
                                <td>
                                    <input type="number" step="0.01" name="honorariums[{{ $category->id }}][amount]" class="form-control" value="{{ $hon ? $hon->amount : '' }}" placeholder="0">
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="honorariums[{{ $category->id }}][percentage]" class="form-control" value="{{ $hon ? $hon->percentage : '' }}" placeholder="0">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Doctor</button>
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
