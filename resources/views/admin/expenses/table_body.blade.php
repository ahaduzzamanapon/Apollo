@foreach($expenses as $expense)
<tr>
    <td>{{ $expense->date }}</td>
    <td>{{ $expense->ledger->name }}</td>
    <td>{{ $expense->description }}</td>
    <td>{{ $expense->amount }}</td>
    <td>
        <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
        </form>
    </td>
</tr>
@endforeach
<tr class="fw-bold table-active">
    <td colspan="3" class="text-end">Total</td>
    <td>{{ number_format($total_amount, 2) }}</td>
    <td></td>
</tr>
<tr>
    <td colspan="5">
        {{ $expenses->links('pagination::bootstrap-5') }}
    </td>
</tr>
