@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">

        <h2 class="mb-4">All Test List</h2>

        {{-- CATEGORY TABS --}}
        <ul class="nav nav-tabs" role="tablist">
            @foreach($categories as $categoryName => $categoryData)
                @php $tabId = 'tab_' . Str::slug($categoryName); @endphp
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

        {{-- CATEGORY CONTENT --}}
        <div class="tab-content mt-3">
            @foreach($categories as $categoryName => $categoryData)
                @php
                    $tabId = 'tab_' . Str::slug($categoryName);
                    $categoryId = $categoryData['id'];
                    $tests = $categoryData['tests'];
                @endphp

                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">

                    <div class="card">
                        <div class="card-body">
                            <a href="{{ route('reports.create', ['category_id' => $categoryId, 'category_name' => $categoryName]) }}" class="btn btn-sm btn-primary mb-3">
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

                                                    <a href="{{ route('reports.edit', $test->id) }}" class="btn btn-sm btn-warning">Edit</a>



                                                    {{-- Add Fields Modal Trigger --}}
                                                    <button type="button" class="btn btn-sm btn-secondary"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#addTestFieldModal"
                                                            data-test-id="{{ $test->id }}"
                                                            data-test-name="{{ $test->test_name }}">
                                                        Add Field
                                                    </button>

                                                    {{-- Edit Fields Modal Trigger --}}
                                                    <button type="button" class="btn btn-sm btn-info text-white"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editTestFieldModal"
                                                            data-test-id="{{ $test->id }}"
                                                            data-test-name="{{ $test->test_name }}">
                                                        Edit Fields
                                                    </button>

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

{{-- ADD FIELDS MODAL --}}
<div class="modal fade" id="addTestFieldModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.testFields.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Test Fields</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <input type="hidden" id="testId" name="test_id">
                    <div class="mb-3">
                        <label class="form-label">Test Name</label>
                        <input type="text" class="form-control" id="testName" readonly>
                    </div>

                    <table class="table table-bordered" id="fieldTable">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Unit</th>
                                <th>Reference Value</th>
                                <th width="50">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><input type="text" name="fields[0][perameter]" class="form-control" required></td>
                                <td><input type="text" name="fields[0][unit]" class="form-control" required></td>
                                <td><input type="text" name="fields[0][ref_val]" class="form-control" required></td>
                                <td><button type="button" class="btn btn-danger btn-sm removeRow">×</button></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-sm btn-success" id="addRow">+ Add More Field</button>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save All</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT FIELDS MODAL --}}
<div class="modal fade" id="editTestFieldModal" tabindex="-1" inert>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.testFields.update', 0) }}" id="editForm">
                @csrf
                @method('PUT')

                <input type="hidden" name="test_id" id="editTestId">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Test Fields</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="text" id="editTestName" class="form-control mb-3" readonly>

                    <table class="table table-bordered" id="editFieldTable">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Unit</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <button type="button" class="btn btn-success btn-sm" id="editAddRow">+ Add More</button>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update All</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
    // ------------------ ADD FIELDS MODAL ------------------
    let rowIndex = 1;
    document.getElementById('addRow').addEventListener('click', function () {
        let table = document.querySelector('#fieldTable tbody');
        table.insertAdjacentHTML('beforeend', `
            <tr>
                <td><input type="text" name="fields[${rowIndex}][perameter]" class="form-control" required></td>
                <td><input type="text" name="fields[${rowIndex}][unit]" class="form-control" required></td>
                <td><input type="text" name="fields[${rowIndex}][ref_val]" class="form-control" required></td>
                <td><button type="button" class="btn btn-danger btn-sm removeRow">×</button></td>
            </tr>
        `);
        rowIndex++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
    });

    document.getElementById('addTestFieldModal').addEventListener('show.bs.modal', function (e) {
        document.getElementById('testId').value = e.relatedTarget.dataset.testId;
        document.getElementById('testName').value = e.relatedTarget.dataset.testName;
    });

    // ------------------ EDIT FIELDS MODAL ------------------
    let editIndex = 0;

    document.getElementById('editTestFieldModal').addEventListener('show.bs.modal', function (e) {
        const testId = e.relatedTarget.dataset.testId;
        const testName = e.relatedTarget.dataset.testName;

        document.getElementById('editTestId').value = testId;
        document.getElementById('editTestName').value = testName;

        const tbody = document.querySelector('#editFieldTable tbody');
        tbody.innerHTML = '';
        editIndex = 0;

        const urlTemplate = "{{ route('admin.testFields.byTest', ['test' => '__TEST_ID__']) }}";
        const url = urlTemplate.replace('__TEST_ID__', testId);

        document.getElementById('editTestFieldModal').removeAttribute('inert');

        fetch(url)
            .then(res => res.json())
            .then(fields => fields.forEach(field => addEditRow(field)))
            .catch(err => console.error(err));
    });

    document.getElementById('editAddRow').addEventListener('click', () => addEditRow());

    function addEditRow(field = {}) {
        const tbody = document.querySelector('#editFieldTable tbody');
        const rowId = field.id ?? '';
        const html = `
            <tr>
                <td>
                    <input type="hidden" name="fields[${editIndex}][id]" value="${rowId}">
                    <input type="text" name="fields[${editIndex}][perameter]" class="form-control" value="${field.perameter ?? ''}" required>
                </td>
                <td>
                    <input type="text" name="fields[${editIndex}][unit]" class="form-control" value="${field.unit ?? ''}" required>
                </td>
                <td>
                    <input type="text" name="fields[${editIndex}][ref_val]" class="form-control" value="${field.ref_val ?? ''}" required>
                </td>

            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', html);
        editIndex++;
    }



    // Reset modal on close
    document.getElementById('editTestFieldModal').addEventListener('hidden.bs.modal', function () {
        this.setAttribute('inert', '');
        document.querySelector('#editFieldTable tbody').innerHTML = '';
        editIndex = 0;
    });
</script>
@endsection
