@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Invoices</h2>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Scan Invoice Barcode</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barcodeInput">Scan Barcode:</label>
                                <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode here...">
                            </div>
                        </div>
                    </div>
                    <div id="scanResult"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>All Invoices</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Invoice Code</th>
                                <th>Patient Name</th>
                                <th>Amount</th>
                                <th>Discount</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td><strong>{{ $invoice->invoice_code }}</strong></td>
                                    <td>{{ $invoice->patientReport->patient->name }}</td>
                                    <td>{{ number_format($invoice->amount, 2) }}</td>
                                    <td>{{ number_format($invoice->discount, 2) }}</td>
                                    <td><strong>{{ number_format($invoice->total, 2) }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('admin.invoices.download', $invoice) }}" class="btn btn-sm btn-success">Download</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No invoices found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const barcode = this.value.trim();
        if (barcode) {
            scanBarcode(barcode);
        }
    }
});

function scanBarcode(barcode) {
    const resultDiv = document.getElementById('scanResult');

    fetch('{{ route("admin.invoices.scan") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ barcode: barcode })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const invoice = data.invoice;
            const patient = invoice.patient;

            resultDiv.innerHTML = `
                <div class="alert alert-success mt-3">
                    <h6>Invoice Found!</h6>
                    <p><strong>Invoice Code:</strong> ${invoice.invoice_code}</p>
                    <p><strong>Patient:</strong> ${patient.name}</p>
                    <p><strong>NID:</strong> ${patient.nid || 'N/A'}</p>
                    <p><strong>Mobile:</strong> ${patient.mobile || 'N/A'}</p>
                    <p><strong>Total Amount:</strong> ${invoice.total}</p>
                    <p><strong>Status:</strong> ${invoice.status}</p>
                    <a href="/admin/invoices/${invoice.id}" class="btn btn-primary btn-sm">View Invoice</a>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-warning mt-3">
                    ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        resultDiv.innerHTML = `
            <div class="alert alert-danger mt-3">
                Error scanning barcode: ${error.message}
            </div>
        `;
    });

    // Clear input
    document.getElementById('barcodeInput').value = '';
}
</script>
@endsection
