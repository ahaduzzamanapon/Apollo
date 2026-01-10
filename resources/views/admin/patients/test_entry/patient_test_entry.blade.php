@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Test Entry Form </div>
                <div class="card-body">
                    <h5>Patient Info</h5>
                    <table class="table table-borderless" style="border:none">
                        @foreach($patients as $row)
                        <tr>
                            <td>Name:  {{ $row->name }}</td>
                            <td>Age: {{ $row->age }}</td>
                            <td>Phone: {{ $row->mobile }}</td>
                        </tr>
                        @endforeach
                    </table>

                    <table class="table table-borderless" style="border:none">
                        <tr>
                            <th>Test Category</th>
                            <th class="text-end">Action</th>
                        </tr>
                        @foreach($patients as $row)
                        @foreach($row->category as $test)
                        <tr>
                            <td>{{ $test->category->test_name }}</td>
                            <td class="text-end">{{ number_format($test->price, 0) }}</td>
                        </tr>
                        @endforeach
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
