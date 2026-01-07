@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-gray-800 fw-bold">Edit TestField</h5>
            <a href="{{ route('admin.testFields.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.testFields.update', $item->id) }}" method="POST" >
                @csrf
                @method('PUT')
                <div class="row">
            <div class="col-md-6 mb-3">
                <label for="test_id" class="form-label">Test Id</label>
                <input type="text" class="form-control @error('test_id') is-invalid @enderror" id="test_id" name="test_id" value="{{ old('test_id', $item->test_id) }}">
                @error('test_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="perameter" class="form-label">Perameter</label>
                <input type="text" class="form-control @error('perameter') is-invalid @enderror" id="perameter" name="perameter" value="{{ old('perameter', $item->perameter) }}">
                @error('perameter')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="unit" class="form-label">Unit</label>
                <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', $item->unit) }}">
                @error('unit')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="ref_val" class="form-label">Ref Val</label>
                <input type="text" class="form-control @error('ref_val') is-invalid @enderror" id="ref_val" name="ref_val" value="{{ old('ref_val', $item->ref_val) }}">
                @error('ref_val')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.testFields.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update TestField
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
