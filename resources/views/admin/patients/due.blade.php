@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 text-danger">Patient Due List</h2>
            <div>
                <a href="{{ route('admin.patients.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> New Patient Entry
                </a>
            </div>
        </div>

        <div class="card card-fixed shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by Name, Mobile, Code...">
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="startDate" class="form-control" placeholder="Start Date" onchange="filterData()">
                    </div>
                    <div class="col-md-2">
                        <input type="date" id="endDate" class="form-control" placeholder="End Date" onchange="filterData()">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary w-100" onclick="filterData()">Search</button>
                        <button class="btn btn-secondary w-100" onclick="resetFilters()">Reset</button>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button class="btn btn-danger w-100" onclick="exportData('pdf')" title="PDF"><i class="bi bi-file-earmark-pdf"></i></button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Sl No.</th>
                                <th>ID No.</th>
                                <th>Invoice ID</th>
                                <th>Date</th>
                                <th>Patient Name</th>
                                <th>Mobile</th>
                                <th>Ref. Doctor</th>
                                <th>Total Amt</th>
                                <th>Paid</th>
                                <th class="text-danger">Due Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @include('admin.patients.table_body')
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.patients.payment_modal')

<script>
    function filterData() {
        let search = document.getElementById('searchInput').value;
        let start_date = document.getElementById('startDate').value;
        let end_date = document.getElementById('endDate').value;

        let query = `?search=${search}&start_date=${start_date}&end_date=${end_date}`;

        fetch("{{ route('admin.patients.due') }}" + query, {
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
        window.location.href = "{{ route('admin.patients.due') }}" + query;
    }

    // Live search
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterData();
    });
</script>
@endsection
