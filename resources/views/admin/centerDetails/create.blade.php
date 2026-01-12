@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-gray-800 fw-bold">Create Center Details</h5>
            <a href="{{ route('admin.centerDetails.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.centerDetails.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
            <div class="col-md-4 mb-3">
                <label for="name_bn" class="form-label">Name Bn</label>
                <input type="text" class="form-control @error('name_bn') is-invalid @enderror" id="name_bn" name="name_bn" value="{{ old('name_bn') }}">
                @error('name_bn')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="name_en" class="form-label">Name En</label>
                <input type="text" class="form-control @error('name_en') is-invalid @enderror" id="name_en" name="name_en" value="{{ old('name_en') }}">
                @error('name_en')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 mb-3">
                <label for="about" class="form-label">About</label>
                <textarea class="form-control editor @error('about') is-invalid @enderror" id="about" name="about" rows="3">{{ old('about') }}</textarea>
                @error('about')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="number" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4 mb-3">
                <label for="logo_image" class="form-label">Logo Image</label>
                <input type="file" class="form-control @error('logo_image') is-invalid @enderror" id="logo_image" name="logo_image">
                @error('logo_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.centerDetails.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save CenterDetails
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
