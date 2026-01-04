@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">

        <h2 class="mb-4">All Test List</h2>

        {{-- CATEGORY TABS --}}
        <ul class="nav nav-tabs" role="tablist">
            @foreach($categories as $categoryName => $categoryData)
                @php
                    $tabId = 'tab_' . Str::slug($categoryName);
                @endphp
                <li class="nav-item">
                    <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                       data-bs-toggle="tab"
                       href="#{{ $tabId }}"
                       role="tab">
                        {{ $categoryName }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content mt-3">
            @foreach($categories as $categoryName => $categoryData)
                @php
                    $tabId = 'tab_' . Str::slug($categoryName);
                    $categoryId = $categoryData['id'];
                    $tests = $categoryData['tests'];
                @endphp

                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                     id="{{ $tabId }}"
                     role="tabpanel">

                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('reports.create', ['category_id' => $categoryId, 'category_name' => $categoryName ?? null ]) }}" class="btn btn-sm btn-primary mb-3">
                                Add New Test
                            </a>
                            @if($tests->isNotEmpty())
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Test Name</th>
                                            <th>Price (TK)</th>
                                            <th>Room No</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tests as $test)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $test->test_name }}</td>
                                                <td>{{ $test->price }}</td>
                                                <td>{{ $test->room_no }}</td>
                                                <td>
                                                    <a href="{{ route('reports.edit', $test->id) }}" class="btn btn-sm btn-warning">Edit </a>

                                                    <form action="{{ route('reports.destroy', $test->id) }}"
                                                          method="POST"
                                                          style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Are you sure?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No tests added for this category yet.</p>
                            @endif

                        </div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</div>
@endsection
