@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Doctors</h2>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">Add New Doctor</a>
            <input type="text" id="course_search" class="form-control w-25" placeholder="Search Doctors...">
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
            fetchDoctors(query, 1);
        });

        // Pagination functionality
        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            var url = $(this).attr('href');
            var page = new URL(url).searchParams.get('page');
            var query = $('#course_search').val();
            fetchDoctors(query, page);
        });

        function fetchDoctors(query, page) {
            $.ajax({
                url: "{{ route('admin.doctors.index') }}",
                method: 'GET',
                data: {
                    search: query,
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

