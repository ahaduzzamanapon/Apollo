@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2 class="mb-4">Account Ledgers</h2>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ledgers as $ledger)
                        <tr>
                            <td>{{ $ledger->name }}</td>
                            <td>
                                <span class="badge bg-{{ $ledger->type == 'Income' ? 'success' : 'danger' }}">
                                    {{ $ledger->type }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('ledgers.destroy', $ledger->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $ledgers->links() }}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <h3>Add Ledger</h3>
        <div class="card">
            <div class="card-body">
                <form action="{{ route('ledgers.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label>Ledger Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Type</label>
                        <select name="type" class="form-control" required>
                            <option value="Expense">Expense</option>
                            <option value="Income">Income</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
