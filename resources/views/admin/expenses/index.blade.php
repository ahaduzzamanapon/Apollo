@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Expenses</h2>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <div class="row g-2">
                             <div class="col-md-12 mb-2">
                                <h5 class="card-title mb-0">Expense List</h5>
                             </div>
                            <div class="col-md-6">
                                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                            </div>
                            <div class="col-md-6 d-flex gap-1">
                                <button class="btn btn-success w-100" onclick="exportData('true')" title="CSV"><i class="bi bi-file-earmark-excel"></i></button>
                                <button class="btn btn-danger w-100" onclick="exportData('pdf')" title="PDF"><i class="bi bi-file-earmark-pdf"></i></button>
                            </div>
                            <div class="col-md-6">
                                <input type="date" id="startDate" class="form-control" placeholder="Start" onchange="filterData()">
                            </div>
                            <div class="col-md-6">
                                <input type="date" id="endDate" class="form-control" placeholder="End" onchange="filterData()">
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-secondary w-100" onclick="resetFilters()">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Ledger</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @include('admin.expenses.table_body')
                            </tbody>
                        </table>
                    </div>
                </div>

<script>
    function filterData() {
        let search = document.getElementById('searchInput').value;
        let start_date = document.getElementById('startDate').value;
        let end_date = document.getElementById('endDate').value;

        let query = `?search=${search}&start_date=${start_date}&end_date=${end_date}`;

        fetch("{{ route('expenses.index') }}" + query, {
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.text())
        .then(html => {
            document.getElementById('tableBody').innerHTML = html;
        });
    }

    function resetFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        filterData();
    }

    function exportData(type) {
        let search = document.getElementById('searchInput').value;
        let start_date = document.getElementById('startDate').value;
        let end_date = document.getElementById('endDate').value;
        let query = `?export=${type}&search=${search}&start_date=${start_date}&end_date=${end_date}`;
        window.location.href = "{{ route('expenses.index') }}" + query;
    }

    // Live search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterData();
    });
</script>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Record Expense</div>
                    <div class="card-body">
                        <form action="{{ route('expenses.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="mb-3">
                                <label>Ledger Head</label>
                                <select name="ledger_id" class="form-control" required>
                                    @foreach($ledgers as $ledger)
                                        <option value="{{ $ledger->id }}">{{ $ledger->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Amount</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Save Expense</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
