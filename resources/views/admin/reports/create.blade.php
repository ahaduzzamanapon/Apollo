@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-6 offset-md-3">
        <h2>Add New Test Report</h2>

        <form action="{{ route('reports.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">

                    <div class="mb-3">
                        <label>Category Name</label>
                        <input type="hidden" name="category_name" value="{{ $category->category_id ?? request('category_id') }}" required>
                        <input type="text" class="form-control" value="{{ $_GET['category_name'] ?? request('category_name') }}" readonly disabled>
                    </div>

                    <div class="mb-3">
                        <label>Test Name</label>
                        <input type="text" name="test_name" class="form-control" value="{{ old('test_name') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Price (TK)</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" required>
                    </div>

                    <div class="mb-3">
                        <label>Room No</label>
                        <input type="text" name="room_no" class="form-control" value="{{ old('room_no') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Save Test</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">Cancel</a>

                </div>
            </div>
        </form>
    </div>
</div>
@endsection
