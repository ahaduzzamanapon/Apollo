<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.patients.payment') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="patient_report_id" id="payment_report_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Report Code</label>
                        <input type="text" id="payment_report_code" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Due Amount</label>
                        <input type="text" id="payment_due_amount" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pay Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" required min="1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="Card">Card</option>
                            <option value="Mobile Banking">Mobile Banking</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPaymentModal(id, code, due) {
        document.getElementById('payment_report_id').value = id;
        document.getElementById('payment_report_code').value = code;
        document.getElementById('payment_due_amount').value = due;
        
        var myModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        myModal.show();
    }
</script>
