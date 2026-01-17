@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Patient Test List</h5>
                    <div class="d-flex gap-2">
                        <input type="text" id="searchInput" class="form-control form-control-sm" style="width: 250px;" placeholder="Search by Code, Name, Mobile...">
                        <button class="btn btn-primary btn-sm" onclick="filterData()">Search</button>
                        <button class="btn btn-secondary btn-sm" onclick="resetFilters()">Reset</button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Report Code / Date</th>
                                    <th>Patient Name</th>
                                    <th>Age</th>
                                    <th>Phone Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">
                                @include('admin.patients.test_entry.table_body')
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div id="paginationLinks">
                        {{ $patients->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function filterData() {
        let search = document.getElementById('searchInput').value;
        let query = `?search=${search}`;

        fetch("{{ route('admin.test_entry_form.index') }}" + query, {
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
        window.location.href = "{{ route('admin.test_entry_form.index') }}";
    }

    // Live search
    document.getElementById('searchInput').addEventListener('keyup', function(e) {
        if(e.key === 'Enter') {
            filterData();
        }
    });

    // Optional: Live search on keyup with delay
    let timeout = null;
    document.getElementById('searchInput').addEventListener('keyup', function (e) {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            filterData();
        }, 500);
    });
</script>
@endsection
