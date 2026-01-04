@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-gray-800 fw-bold">Create TestCategory</h5>
            <a href="{{ route('admin.testCategories.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.testCategories.store') }}" method="POST" >
                @csrf
                <div class="row">
            <div class="col-md-4 mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <input type="text" class="form-control @error('category_name') is-invalid @enderror" id="category_name" name="category_name" value="{{ old('category_name') }}">
                @error('category_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="status" class="form-label">Status</label><br>
                <input type="radio" class="form-check-input @error('status') is-invalid @enderror" id="status" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}> Active
                <input type="radio" class="form-check-input @error('status') is-invalid @enderror" id="status" name="status" value="0" {{ old('status') == '0' ? 'checked' : '' }}> Inactive
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.testCategories.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save TestCategory
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
