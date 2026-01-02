@extends('admin.layouts.app')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        padding: 5px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label>Report/Entry Date</label>
                            <input type="date" name="report_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Patient Name</label>
                            <input type="text" name="patient_name" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Mobile Number</label>
                            <input type="text" name="mobile" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Age</label>
                            <input type="number" name="age" class="form-control" required>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Age Unit</label>
                            <select name="age_unit" class="form-control">
                                <option value="Years">Years</option>
                                <option value="Months">Months</option>
                                <option value="Days">Days</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-3">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>NID (Optional)</label>
                            <input type="text" name="nid" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Reference Doctor</label>
                            <div class="input-group">
                                <select name="reference_doctor_id" class="form-control search-select">
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                    @endforeach
                                </select>
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
                                <th>Test Name</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="testTableBody">
                            <!-- Dynamic Rows -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Total</th>
                                <th><input type="number" id="totalAmount" class="form-control" readonly value="0"></th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-end">Discount</th>
                                <th>
                                    <div class="d-flex gap-2">
                                        <input type="number" id="discountFlat" class="form-control" placeholder="Flat" oninput="calculateTotal()">
                                        <input type="number" id="discountPercent" class="form-control" placeholder="%" oninput="calculateTotal()">
                                        <input type="hidden" name="discount" id="finalDiscount" value="0">
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-end">Net Payable</th>
                                <th><input type="number" id="netPayable" class="form-control" readonly value="0"></th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-end">Paid Amount</th>
                                <th>
                                    <input type="number" name="paid_amount" id="paidAmount" class="form-control mb-2" value="0" oninput="calculateTotal()">
                                    
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
                                <th colspan="2" class="text-end">Due Amount</th>
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
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeTest(${id}, ${price})">X</button></td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);

        total += price;
        updateCalculations();
        
        $('#testSelect').val(null).trigger('change'); // Reset Select2 for next input
    }
    
    // ... (rest of functions: removeTest, updateCalculations, calculateTotal) ...
   
    function removeTest(id, price) {
        document.getElementById('row-'+id).remove();
        total -= price;
        updateCalculations();
    }

    function updateCalculations() {
        document.getElementById('totalAmount').value = total;
        calculateTotal();
    }

    function calculateTotal() {
        let flatDiscount = parseFloat(document.getElementById('discountFlat').value) || 0;
        let percentDiscount = parseFloat(document.getElementById('discountPercent').value) || 0;
        let paid = parseFloat(document.getElementById('paidAmount').value) || 0;
        
        let totalDiscount = flatDiscount + ((total * percentDiscount) / 100);

        // Prevent discount from exceeding total
        if(totalDiscount > total) {
             totalDiscount = total;
        }
        
        // Update hidden input used for submission
        document.getElementById('finalDiscount').value = Math.round(totalDiscount);

        const net = Math.round(total - totalDiscount);
        const due = Math.round(net - paid);

        document.getElementById('netPayable').value = net;
        document.getElementById('dueAmount').value = due;
    }
</script>
@endpush

