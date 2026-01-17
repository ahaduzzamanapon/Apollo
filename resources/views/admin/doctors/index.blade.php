@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Doctors</h2>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">Add New Doctor</a>
            <div class="d-flex align-items-center">
                <span class="me-2">Show</span>
                <select id="per_page" class="form-control w-auto me-2">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <input type="text" id="course_search" class="form-control" placeholder="Search Doctors...">
            </div>
        </div>
        <div class="card">
            <div class="card-body" id="doctors-table-container">
                @include('admin.doctors.table_rows')
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Search functionality
        $('#course_search').on('keyup', function() {
            var query = $(this).val();
            var perPage = $('#per_page').val();
            fetchDoctors(query, perPage, 1);
        });

        // Per page filter
        $('#per_page').on('change', function() {
            var query = $('#course_search').val();
            var perPage = $(this).val();
            fetchDoctors(query, perPage, 1);
        });

        // Pagination functionality
        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            var url = $(this).attr('href');
            var page = new URL(url).searchParams.get('page');
            var query = $('#course_search').val();
            var perPage = $('#per_page').val();
            fetchDoctors(query, perPage, page);
        });

        function fetchDoctors(query, perPage, page) {
            $.ajax({
                url: "{{ route('admin.doctors.index') }}",
                method: 'GET',
                data: {
                    search: query,
                    per_page: perPage,
                    page: page
                },
                success: function(data) {
                    $('#doctors-table-container').html(data);
                }
            });
        }
    });
</script>
@endsection

