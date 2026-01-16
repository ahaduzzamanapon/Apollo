@if($doctors->count() > 0)
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
            <td>{{ ($doctors->currentPage() - 1) * $doctors->perPage() + $loop->iteration }}</td>
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
                <a href="{{ route('admin.doctors.edit', $doctor->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('admin.doctors.destroy', $doctor->id) }}" method="POST" style="display:inline;">
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
@else
    <div class="text-center py-5">
        <h4 class="text-muted">No Data Found</h4>
    </div>
@endif
