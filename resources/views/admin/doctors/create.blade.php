@extends('admin.layouts.app')

@section('content')
<style>
    table tr td{
        padding: 5px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">

        <h2>Add New Doctor</h2>

        <form action="{{ route('admin.doctors.store') }}" method="POST">
            @csrf

            {{-- BASIC INFO --}}
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

            {{-- HONORARIUM SETTINGS --}}
            <div class="card mb-4">
                <div class="card-header">Honorarium Settings</div>
                <div class="card-body">

                    {{-- CATEGORY TABS --}}
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        @foreach($tests as $categoryName => $items)
                            <li class="nav-item">
                                <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                                   data-bs-toggle="tab"
                                   href="#tab-{{ Str::slug($categoryName) }}">
                                    {{ $categoryName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    {{-- TAB CONTENT --}}
                    <div class="tab-content">

                        @foreach($tests as $categoryName => $items)

                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                             id="tab-{{ Str::slug($categoryName) }}">

                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Test Name</th>
                                        <th>Fixed Amount (TK)</th>
                                        <th>Percentage (%)</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach($items as $index => $test)

                                    @php
                                        $prefillAmount = '';
                                        $prefillPercent = '';

                                        if(isset($latestDoctor)) {
                                            $hon = $latestDoctor->honorariums
                                                ->where('report_category_id', $test->id)
                                                ->first();

                                            if($hon) {
                                                $prefillAmount = $hon->amount ?: '';
                                                $prefillPercent = $hon->percentage ?: '';
                                            }
                                        }
                                    @endphp

                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $test->test_name }}</td>

                                        <td>
                                            <input type="number" step="0.01"
                                                name="honorariums[{{ $test->id }}][amount]"
                                                class="form-control"
                                                value="{{ $prefillAmount }}"
                                                placeholder="0">
                                        </td>

                                        <td>
                                            <input type="number" step="0.01"
                                                name="honorariums[{{ $test->id }}][percentage]"
                                                class="form-control"
                                                value="{{ $prefillPercent }}"
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

            <button type="submit" class="btn btn-success">Save Doctor</button>
            <a href="{{ route('admin.doctors.index') }}" class="btn btn-secondary">Cancel</a>

        </form>
    </div>
</div>
@endsection
