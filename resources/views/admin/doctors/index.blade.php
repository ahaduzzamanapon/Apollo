@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2 class="mb-4">Doctors</h2>
        <a href="{{ route('doctors.create') }}" class="btn btn-primary mb-3">Add New Doctor</a>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Commission Settings</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doctors as $doctor)
                        <tr>
                            <td>{{ $doctor->id }}</td>
                            <td>{{ $doctor->name }}</td>
                            <td>{{ $doctor->mobile }}</td>
                            <td>{{ $doctor->email }}</td>
                            <td>{{ $doctor->address }}</td>
                            <td>
                                @foreach($doctor->honorariums as $hon)
                                    <span class="badge bg-info">
                                        {{ $hon->reportCategory->test_name }}: 
                                        {{ $hon->amount > 0 ? $hon->amount . ' TK' : $hon->percentage . '%' }}
                                    </span><br>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('doctors.destroy', $doctor->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $doctors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
