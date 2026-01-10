@extends('admin.layouts.app')

@section('content')

<style>
    table tr td {
        padding: 5px !important;
        vertical-align: middle;
    }
</style>

<div class="row">
    <div class="col-md-12">

        <h2>Edit Doctor</h2>

        <form action="{{ route('admin.doctors.update', $doctor->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- ================= BASIC INFO ================= --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Basic Info</strong>
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label>Name</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   value="{{ $doctor->name }}"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Mobile</label>
                            <input type="text"
                                   name="mobile"
                                   class="form-control"
                                   value="{{ $doctor->mobile }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ $doctor->email }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Address</label>
                            <textarea name="address"
                                      class="form-control">{{ $doctor->address }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ================= HONORARIUM SETTINGS ================= --}}
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Honorarium Settings</strong>
                </div>

                <div class="card-body">

                    {{-- ========= CATEGORY TABS ========= --}}
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        @foreach($tests as $categoryName => $items)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                                   data-bs-toggle="tab"
                                   href="#tab-{{ Str::slug($categoryName) }}"
                                   role="tab">
                                    {{ $categoryName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    {{-- ========= TAB CONTENT ========= --}}
                    <div class="tab-content">

                        @foreach($tests as $categoryName => $items)

                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                             id="tab-{{ Str::slug($categoryName) }}"
                             role="tabpanel">

                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Test Name</th>
                                        <th width="180">Fixed Amount (TK)</th>
                                        <th width="180">Percentage (%)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach($items['tests'] as $index => $test)

                                    @php
                                        $hon = $doctor->honorariums
                                            ->where('report_category_id', $test->id)
                                            ->first();
                                    @endphp

                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td>{{ $test->test_name }}</td>

                                        <td>
                                            <input type="number"
                                                   step="0.01"
                                                   name="honorariums[{{ $test->id }}][amount]"
                                                   class="form-control"
                                                   value="{{ $hon?->amount }}"
                                                   placeholder="0">
                                        </td>

                                        <td>
                                            <input type="number"
                                                   step="0.01"
                                                   name="honorariums[{{ $test->id }}][percentage]"
                                                   class="form-control"
                                                   value="{{ $hon?->percentage }}"
                                                   placeholder="0">
                                        </td>
                                    </tr>

                                @endforeach
                                </tbody>
                            </table>

                        </div>

                        @endforeach

                    </div>
                </div>
            </div>

            {{-- ================= ACTION BUTTONS ================= --}}
            <div class="mb-4">
                <button type="submit" class="btn btn-primary">
                    Update Doctor
                </button>

                <a href="{{ route('admin.doctors.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </div>
</div>

@endsection
