@foreach($reports as $report)
<tr>
    <td>
        <strong>{{ $report->daily_id ?? $report->id }}</strong><br>
        <small class="text-muted">{{ $report->report_code }}</small>
    </td>
    <td>{{ date('d M, Y', strtotime($report->report_date)) }}</td>
    <td>{{ $report->patient->name }}</td>
    <td>{{ $report->patient->mobile }}</td>
    <td>{{ $report->referenceDoctor->name ?? 'Self' }}</td>
    <td>{{ $report->final_amount }}</td>
    <td>{{ $report->paid_amount }}</td>
    <td>
        @if($report->due_amount > 0)
            <span class="badge bg-danger">{{ $report->due_amount }} TK</span>
        @else
            <span class="badge bg-success">Paid</span>
        @endif
    </td>
    <td>
        <a href="{{ route('admin.patients.show', $report->id) }}" class="btn btn-sm btn-info text-white" title="View"><i class="bi bi-eye"></i></a>
        @if($report->due_amount > 0)
        <button type="button" class="btn btn-sm btn-success" onclick="openPaymentModal({{ $report->id }}, '{{ $report->report_code }}', {{ $report->due_amount }})" title="Take Payment">
            <i class="bi bi-cash"></i>
        </button>
        @endif
    </td>
</tr>
@endforeach
<tr class="fw-bold fs-6">
    <td colspan="5" class="text-end">Total:</td>
    <td>{{ number_format($total_final ?? 0, 2) }}</td>
    <td>{{ number_format($total_paid ?? 0, 2) }}</td>
    <td>{{ number_format($total_due ?? 0, 2) }}</td>
    <td></td>
</tr>
<tr>
    <td colspan="9">
        {{ $reports->links('pagination::bootstrap-5') }}
    </td>
</tr>
