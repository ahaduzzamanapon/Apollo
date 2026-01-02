@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-gray-800 fw-bold">Edit Division</h5>
            <a href="{{ route('admin.divisions.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.divisions.update', $item->id) }}" method="POST" >
                @csrf
                @method('PUT')
                <div class="row">
            <div class="col-md-6 mb-3">
                <label for="division_name" class="form-label">Division Name</label>
                <input type="text" class="form-control @error('division_name') is-invalid @enderror" id="division_name" name="division_name" value="{{ old('division_name', $item->division_name) }}">
                @error('division_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 mb-3">
                <label for="description_" class="form-label">Description </label>
                <textarea class="form-control editor @error('description_') is-invalid @enderror" id="description_" name="description_" rows="3">{{ old('description_', $item->description_) }}</textarea>
                @error('description_')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.divisions.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Division
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
