@extends('admin.layouts.app')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 2px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    label {
        font-size: 14px;
    }
    .form-control, .form-select {
        border-radius: 6px;
        padding: 6px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>New Patient Entry</h2>
            <div>
                <a href="{{ route('admin.patients.due') }}" class="btn btn-warning me-2">
                    <i class="bi bi-exclamation-circle me-1"></i> Due List
                </a>
                <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        <form action="{{ route('admin.patients.store') }}" method="POST">
            @csrf

            <!-- Patient Info -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Patient Information</div>
                <div class="card-body" >
                    <div class="row" >
                        <div class="col-md-2 mb-3">
                            <label>Date</label>
                            <input type="date" name="report_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Patient Name</label>
                            <input type="text" name="patient_name" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" class="form-control" required>
                        </div>
                        <div class="col-md-1 mb-3">
                            <label>Years</label>
                            <input type="number" name="age_years" class="form-control" value="0" min="0" onfocus="if(this.value=='0') this.value=''" onblur="if(this.value=='') this.value='0'">
                        </div>
                        <div class="col-md-1 mb-3">
                            <label>Months</label>
                            <input type="number" name="age_months" class="form-control" value="0" min="0" max="11" onfocus="if(this.value=='0') this.value=''" onblur="if(this.value=='') this.value='0'">
                        </div>
                        <div class="col-md-1 mb-3">
                            <label>Days</label>
                            <input type="number" name="age_days" class="form-control" value="0" min="0" max="30" onfocus="if(this.value=='0') this.value=''" onblur="if(this.value=='') this.value='0'">
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>NID (Optional)</label>
                            <input type="text" name="nid" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex justify-content-between">
                                <label>Reference Doctor</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="refBySomeone" name="ref_by_someone" value="1">
                                    <label class="form-check-label" for="refBySomeone" style="font-size: 0.8rem;">
                                        Ref. by Someone (No Comm.)
                                    </label>
                                </div>
                            </div>
                            <!-- Standard Commission List -->
                            <div id="refDoctorContainer">
                                <div class="input-group">
                                    <select name="reference_doctor_id" id="referenceDoctorSelect" class="form-control search-select">
                                        <option value="">Select Doctor</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!-- No Commission List -->
                            <div id="refSomeoneContainer" style="display: none;">
                                <div class="input-group">
                                    <select name="reference_doctor_id" id="refSomeoneSelect" class="form-control search-select" disabled>
                                        <option value="">Select Doctor (No Comm.)</option>
                                        @foreach($doctors as $doctor)
                                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label class="d-block">&nbsp;</label>
                            <a href="{{ route('admin.doctors.create') }}" class="btn btn-outline-primary w-100" target="_blank">
                                <i class="bi bi-plus-lg"></i> Add New
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Selection -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">Test Selection</div>
                <div class="card-body">
                     <div class="mb-3">
                        <label>Select Test</label>
                        <select id="testSelect" class="form-control">
                            <option value="">Select a Test...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" data-price="{{ $category->price }}" data-name="{{ $category->test_name }}">
                                    {{ $category->test_name }} - {{ $category->price }} TK
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width:30pc">Test Name</th>
                                <th>Price</th>
                                <th>Flat Discount</th>
                                <th>Percentage(%) Discount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="testTableBody">
                            <!-- Dynamic Rows -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th><input type="number" id="totalAmount" class="form-control" readonly value="0"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Total Discount</th>
                                <th>
                                    <input type="number" name="discount" id="finalDiscount" class="form-control" readonly value="0">
                                </th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Net Payable</th>
                                <th><input type="number" id="netPayable" class="form-control" readonly value="0"></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Paid Amount</th>
                                <th>
                                    <input type="number" name="paid_amount" id="paidAmount" class="form-control mb-2" value="0" step="0.01" 
                                           onfocus="if(this.value=='0') this.value=''" 
                                           onblur="if(this.value=='') this.value='0'; calculateTotal()" 
                                           oninput="calculateTotal()">

                                    <div class="d-flex gap-2">
                                        <select name="payment_method" class="form-control form-control-sm">
                                            <option value="Cash">Cash</option>
                                            <option value="Card">Card</option>
                                            <option value="Mobile Banking">Mobile Banking</option>
                                        </select>
                                        <input type="text" name="remarks" class="form-control form-control-sm" placeholder="Remarks">
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">Due Amount</th>
                                <th><input type="number" id="dueAmount" class="form-control" readonly value="0"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100">Save & Print Invoice</button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#testSelect').select2({
            placeholder: "Select a Test...",
            allowClear: true,
            width: '100%'
        });

        // Bind Select2 select event
        $('#testSelect').on('select2:select', function (e) {
            addTest();
            });

        // Reference by Someone Logic (Toggle Lists)
        $('#refBySomeone').change(function() {
            if(this.checked) {
                // Hide Standard, Show "Someone"
                $('#refDoctorContainer').hide();
                $('#refSomeoneContainer').show();
                
                // Disable Standard, Enable "Someone"
                $('#referenceDoctorSelect').prop('disabled', true).val(null).trigger('change');
                $('#refSomeoneSelect').prop('disabled', false).val(null).trigger('change');
            } else {
                // Show Standard, Hide "Someone"
                $('#refDoctorContainer').show();
                $('#refSomeoneContainer').hide();
                
                // Enable Standard, Disable "Someone"
                $('#referenceDoctorSelect').prop('disabled', false).val(null).trigger('change');
                $('#refSomeoneSelect').prop('disabled', true).val(null).trigger('change');
            }
        });


        // Also initialize other selects if needed, e.g. doctor reference
        $('.search-select').select2({
            placeholder: "Select Doctor",
            allowClear: true,
            width: '100%'
        });
    });

    let total = 0;

    function addTest() {
        const select = document.getElementById('testSelect');
        const id = select.value;
        if(!id) return;

        const option = select.options[select.selectedIndex];
        const name = option.getAttribute('data-name');
        const price = parseFloat(option.getAttribute('data-price'));

        // Check if already added
        if(document.getElementById('row-'+id)) {
            alert('Test already added!');
            $('#testSelect').val(null).trigger('change'); // Reset Select2
            return;
        }

        const tbody = document.getElementById('testTableBody');
        const row = `
            <tr id="row-${id}">
                <td>${name} <input type="hidden" name="tests[]" value="${id}"></td>
                <td>${price}</td>
                <td>
                    <input type="number" name="test_discounts[]" class="form-control form-control-sm test-discount-flat" 
                           value="0" min="0" max="${price}" step="0.01" 
                           onfocus="if(this.value=='0' || this.value=='0.00') this.value=''" 
                           onblur="if(this.value=='') this.value='0'; syncDiscount(${id}, 'flat')" 
                           oninput="syncDiscount(${id}, 'flat')">
                </td>
                <td>
                    <input type="number" name="test_discount_percents[]" class="form-control form-control-sm test-discount-percent" 
                           value="0" min="0" max="100" step="0.01" 
                           onfocus="if(this.value=='0' || this.value=='0.00') this.value=''" 
                           onblur="if(this.value=='') this.value='0'; syncDiscount(${id}, 'percent')" 
                           oninput="syncDiscount(${id}, 'percent')">
                </td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeTest(${id}, ${price})">X</button></td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);

        total += price;
        updateCalculations();

        $('#testSelect').val(null).trigger('change'); // Reset Select2 for next input
    }

    function syncDiscount(id, type) {
        const row = document.getElementById('row-'+id);
        const price = parseFloat(row.cells[1].innerText);
        const flatInput = row.querySelector('.test-discount-flat');
        const percentInput = row.querySelector('.test-discount-percent');

        if(type === 'flat') {
            let flat = parseFloat(flatInput.value) || 0;
            if(flat > price) {
                flat = price;
                flatInput.value = flat;
            }
            percentInput.value = ((flat / price) * 100).toFixed(2);
        } else {
            let percent = parseFloat(percentInput.value) || 0;
            if(percent > 100) {
                percent = 100;
                percentInput.value = percent;
            }
            flatInput.value = ((price * percent) / 100).toFixed(2);
        }
        calculateTotal();
    }

    // ... (rest of functions: removeTest, updateCalculations, calculateTotal) ...

    function removeTest(id, price) {
        document.getElementById('row-'+id).remove();
        total -= price;
        updateCalculations();
    }

    function updateCalculations() {
        document.getElementById('totalAmount').value = total.toFixed(2);
        calculateTotal();
    }

    function calculateTotal() {
        let totalDiscount = 0;
        document.querySelectorAll('.test-discount-flat').forEach(input => {
            totalDiscount += parseFloat(input.value) || 0;
        });

        let paid = parseFloat(document.getElementById('paidAmount').value) || 0;

        // Update hidden input used for submission
        document.getElementById('finalDiscount').value = totalDiscount.toFixed(2);

        const net = total - totalDiscount;
        const due = net - paid;

        document.getElementById('netPayable').value = net.toFixed(2);
        document.getElementById('dueAmount').value = due.toFixed(2);
    }
</script>
@endpush

