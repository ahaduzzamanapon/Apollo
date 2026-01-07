<div class="table-responsive">
    <table class="table table-hover table-striped mb-0" id="dataTable" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 50px;">SL</th>
                    <th>Test Id</th>
                    <th>Perameter</th>
                    <th>Unit</th>
                    <th>Ref Val</th>
                <th style="width: 150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->test_id }}</td>
                    <td>{{ $item->perameter }}</td>
                    <td>{{ $item->unit }}</td>
                    <td>{{ $item->ref_val }}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.testFields.edit', $item->id) }}" class="btn btn-outline-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.testFields.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="card-footer bg-white border-top py-3">
    <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} entries
        </div>
        <div>
            {{ $items->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
