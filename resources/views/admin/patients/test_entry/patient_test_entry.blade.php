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
                        <tr>
                            <td style='vertical-align:middle'>Name: {{ $patient->name }}</td>
                            <td style='vertical-align:middle'>Age: {{ $patient->age }}</td>
                            <td style='vertical-align:middle'>Phone: {{ $patient->mobile }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
