@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-gray-800 fw-bold">Create PatientTestResult</h5>
            <a href="{{ route('admin.patientTestResults.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.patientTestResults.store') }}" method="POST" >
                @csrf
                <div class="row">
            <div class="col-md-4 mb-3">
                <label for="id" class="form-label">Id</label>
                <input type="text" class="form-control @error('id') is-invalid @enderror" id="id" name="id" value="{{ old('id') }}">
                @error('id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="patien_id" class="form-label">Patien Id</label>
                <input type="text" class="form-control @error('patien_id') is-invalid @enderror" id="patien_id" name="patien_id" value="{{ old('patien_id') }}">
                @error('patien_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="test_id" class="form-label">Test Id</label>
                <input type="text" class="form-control @error('test_id') is-invalid @enderror" id="test_id" name="test_id" value="{{ old('test_id') }}">
                @error('test_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="resilt" class="form-label">Resilt</label>
                <input type="text" class="form-control @error('resilt') is-invalid @enderror" id="resilt" name="resilt" value="{{ old('resilt') }}">
                @error('resilt')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.patientTestResults.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save PatientTestResult
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
