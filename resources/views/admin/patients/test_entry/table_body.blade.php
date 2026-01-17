@foreach($patients as $test)
    <tr>
        <td>{{ $loop->iteration + ($patients->firstItem() - 1) }}</td>
        <td>
            <strong>{{ $test->report_code }}</strong><br>
            <small class="text-muted">{{ $test->report_date }}</small>
        </td>
        <td>{{ $test->patient->name }}</td>
        <td>{{ $test->patient->age }} {{ $test->patient->age_unit }}</td>
        <td>{{ $test->patient->mobile }}</td>
        <td class="text-nowrap">
            <a href="{{ route('admin.patients.test_entry', $test->id) }}" class="btn btn-primary btn-sm" title="Entry/View">
                <i class="fas fa-edit"></i> Entry
            </a>
            <a href="{{ route('patient.test.print', $test->id) }}" target="_blank" class="btn btn-success btn-sm" title="Print">
                <i class="fas fa-print"></i>
            </a>
            <a href="{{ route('patient.test.pdf', $test->id) }}" class="btn btn-danger btn-sm" title="PDF">
                <i class="fas fa-file-pdf"></i>
            </a>
        </td>
    </tr>
@endforeach
