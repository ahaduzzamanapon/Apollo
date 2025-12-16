@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6 offset-md-3">
        <h2>Edit Test Report</h2>
        <form action="{{ route('reports.update', $report->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label>Category Name</label>
                        <input type="text" name="category_name" class="form-control" list="categories" value="{{ $report->category_name }}" required>
                        <datalist id="categories">
                            <option value="Haematology">
                            <option value="Immunology">
                            <option value="Biochemistry">
                            <option value="Urine">
                            <option value="Hormone Analysis">
                        </datalist>
                    </div>
                    <div class="mb-3">
                        <label>Test Name</label>
                        <input type="text" name="test_name" class="form-control" value="{{ $report->test_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Price (TK)</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="{{ $report->price }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Room No</label>
                        <input type="text" name="room_no" class="form-control" value="{{ $report->room_no }}">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Test</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
