@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>New Patient Entry</h2>
            <div>
                <a href="{{ route('admin.patients.due') }}" class="btn btn-warning me-2">
                    <i class="bi bi-exclamation-circle me-1"></i> Due List
                </a>
                <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                    <i class="bi bi-list-ul me-1"></i> All Patient List
                </a>
            </div>
        </div>
        <form action="{{ route('patients.store') }}" method="POST">
            @csrf
            
            <!-- Patient Info -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Patient Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
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
                        <div class="col-md-3 mb-3">
                            <label>Age</label>
                            <input type="number" name="age" class="form-control" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Gender</label>
                            <select name="gender" class="form-control">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>NID (Optional)</label>
                            <input type="text" name="nid" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Reference Doctor</label>
                            <select name="reference_doctor_id" class="form-control search-select">
                                <option value="">Select Doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                @endforeach
                            </select>
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
                        <button type="button" class="btn btn-success mt-2" onclick="addTest()">Add Test</button>
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
                                <th colspan="2" class="text-end">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <label class="me-2">Discount Type</label>
                                        <select id="discountType" class="form-control form-control-sm" style="width: 100px;" onchange="calculateTotal()">
                                            <option value="flat">Flat</option>
                                            <option value="percent">Percent (%)</option>
                                        </select>
                                    </div>
                                </th>
                                <th>
                                    <input type="number" name="discount" id="discount" class="form-control" value="0" oninput="calculateTotal()">
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-end">Net Payable</th>
                                <th><input type="number" id="netPayable" class="form-control" readonly value="0"></th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-end">Paid Amount</th>
                                <th><input type="number" name="paid_amount" id="paidAmount" class="form-control" value="0" oninput="calculateTotal()"></th>
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

<script>
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
    }

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
        let discountInput = parseFloat(document.getElementById('discount').value) || 0;
        const discountType = document.getElementById('discountType').value;
        const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
        
        let discountAmount = 0;

        if (discountType === 'percent') {
            discountAmount = (total * discountInput) / 100;
        } else {
            discountAmount = discountInput;
        }

        // Prevent discount from exceeding total
        if(discountAmount > total) {
             discountAmount = total;
        }
        
        const net = Math.round(total - discountAmount);
        const due = Math.round(net - paid);

        document.getElementById('netPayable').value = net;
        document.getElementById('dueAmount').value = due;
    }
</script>
@endsection
