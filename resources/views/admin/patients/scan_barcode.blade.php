@extends('admin.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-barcode"></i> Scan Invoice Barcode</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="barcode_input" class="form-label">Scan or Enter Bill Number:</label>
                        <input
                            type="text"
                            class="form-control form-control-lg"
                            id="barcode_input"
                            placeholder="Place cursor here and scan barcode..."
                            autofocus
                        >
                    </div>
                    <button type="button" class="btn btn-primary" onclick="searchBarcode()">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>

            <!-- Invoice Display Section -->
            <div id="invoice_result" style="display: none; margin-top: 20px;">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h4 class="border-bottom pb-3">Invoice</h4>
                        </div>

                        <!-- Patient & Invoice Info -->
                        <div class="row mb-4" style="font-size:12px; border: 1px solid; line-height: 0px; padding-top: 25px; height: auto;">
                            <div class="col-md-6">
                                <p><strong>Bill No:</strong> <span id="bill_no"></span></p>
                                <p><strong>Date:</strong> <span id="invoice_date"></span></p>
                                <p><strong>Patient Name:</strong> <span id="patient_name"></span></p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p><strong>Gender:</strong> <span id="patient_gender"></span></p>
                                <p><strong>Age:</strong> <span id="patient_age"></span></p>
                                <p><strong>Mobile:</strong> <span id="patient_mobile"></span></p>
                            </div>
                            <div class="col-md-12" style="margin-top: -10px;">
                                <p style="line-height: 18px;"><strong>Ref. Doctor:</strong> DR. <span id="ref_doctor"></span></p>
                            </div>
                        </div>

                        <!-- Tests Table -->
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="font-size:12px;">Test Name</th>
                                    <th style="font-size:12px; width: 100px;" class="text-end">Price</th>
                                </tr>
                            </thead>
                            <tbody id="tests_table">
                            </tbody>
                            <tbody>
                                <tr>
                                    <th class="text-end" style="font-size:11px;">Total Amount</th>
                                    <th class="text-end" style="font-size:11px;"><span id="total_amount"></span></th>
                                </tr>
                                <tr>
                                    <th class="text-end" style="font-size:11px;">Discount</th>
                                    <th class="text-end" style="font-size:11px;"><span id="discount_amount"></span></th>
                                </tr>
                                <tr>
                                    <th class="text-end" style="font-size:11px;">Net Payable</th>
                                    <th class="text-end" style="font-size:11px;"><span id="net_payable"></span></th>
                                </tr>
                                <tr>
                                    <th class="text-end" style="font-size:11px;">Paid Amount</th>
                                    <th class="text-end" style="font-size:11px;"><span id="paid_amount"></span></th>
                                </tr>
                                <tr>
                                    <th class="text-end" style="font-size:11px;">Due Amount</th>
                                    <th class="text-end" style="font-size:11px;"><span id="due_amount"></span></th>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Payment History -->
                        <h5 class="mt-4 mb-3">Payment History</h5>
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th style="font-size:12px;" class="text-center">Date</th>
                                    <th style="font-size:12px;" class="text-center">Method</th>
                                    <th style="font-size:12px;" class="text-center">Amount</th>
                                    <th style="font-size:12px;" class="text-center">Discount</th>
                                    <th style="font-size:12px;" class="text-center">Collected By</th>
                                </tr>
                            </thead>
                            <tbody id="payments_table">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No payments recorded</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-secondary" onclick="clearResults()">
                                <i class="bi bi-x-circle"></i> Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div id="error_message" class="alert alert-danger" style="display: none; margin-top: 20px;">
            </div>
        </div>
    </div>
</div>

<script>
    // Auto search when barcode is scanned
    document.getElementById('barcode_input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBarcode();
        }
    });

    function searchBarcode() {
        const barcode = document.getElementById('barcode_input').value.trim();

        if (!barcode) {
            showError('Please enter or scan a bill number');
            return;
        }

        // Hide previous results
        document.getElementById('invoice_result').style.display = 'none';
        document.getElementById('error_message').style.display = 'none';

        // Call API
        fetch('{{ route("admin.patients.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                barcode: barcode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayInvoice(data.invoice);
            } else {
                showError(data.message || 'Invoice not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An error occurred while fetching invoice data');
        });
    }

    function displayInvoice(invoice) {
        // Populate patient info
        document.getElementById('bill_no').textContent = invoice.bill_no;
        document.getElementById('invoice_date').textContent = invoice.date;
        document.getElementById('patient_name').textContent = invoice.patient.name;
        document.getElementById('patient_gender').textContent = invoice.patient.gender;
        document.getElementById('patient_age').textContent = invoice.patient.age + ' ' + invoice.patient.age_unit;
        document.getElementById('patient_mobile').textContent = invoice.patient.mobile;
        document.getElementById('ref_doctor').textContent = invoice.doctor.name;

        // Populate tests
        const testsTable = document.getElementById('tests_table');
        testsTable.innerHTML = '';
        invoice.tests.forEach(test => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td style="font-size:11px;">${test.name}</td>
                <td style="font-size:11px;" class="text-end">${test.price}</td>
            `;
            testsTable.appendChild(row);
        });

        // Populate amounts
        document.getElementById('total_amount').textContent = invoice.amounts.total;
        document.getElementById('discount_amount').textContent = invoice.amounts.discount;
        document.getElementById('net_payable').textContent = invoice.amounts.net_payable;
        document.getElementById('paid_amount').textContent = invoice.amounts.paid;
        document.getElementById('due_amount').textContent = invoice.amounts.due;

        // Populate payments
        const paymentsTable = document.getElementById('payments_table');
        if (invoice.payments.length > 0) {
            paymentsTable.innerHTML = '';
            invoice.payments.forEach(payment => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td style="font-size:11px;" class="text-center">${payment.date}</td>
                    <td style="font-size:11px;" class="text-center">${payment.method}</td>
                    <td style="font-size:11px;" class="text-center fw-bold">${payment.amount}</td>
                    <td style="font-size:11px;" class="text-center text-danger">${payment.discount > 0 ? payment.discount : '-'}</td>
                    <td style="font-size:11px;" class="text-center">${payment.collected_by}</td>
                `;
                paymentsTable.appendChild(row);
            });
        }

        // Show result
        document.getElementById('invoice_result').style.display = 'block';
        document.getElementById('error_message').style.display = 'none';
    }

    function showError(message) {
        document.getElementById('error_message').textContent = message;
        document.getElementById('error_message').style.display = 'block';
        document.getElementById('invoice_result').style.display = 'none';
    }

    function clearResults() {
        document.getElementById('barcode_input').value = '';
        document.getElementById('invoice_result').style.display = 'none';
        document.getElementById('error_message').style.display = 'none';
        document.getElementById('barcode_input').focus();
    }
</script>

<style>
    #barcode_input {
        font-size: 18px;
        padding: 12px 15px;
    }

    #barcode_input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
</style>
@endsection
