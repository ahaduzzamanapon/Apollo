@extends('admin.layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-barcode"></i> Barcode Scanner</h5>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="barcode-input" class="form-label"><strong>Scan or Enter Bill Number</strong></label>
                        <input
                            type="text"
                            id="barcode-input"
                            class="form-control form-control-lg"
                            placeholder="ADDC-000001"
                            autofocus
                        >
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Use a barcode scanner or manually enter the bill number
                        </small>
                    </div>

                    <button type="button" class="btn btn-primary w-100 mb-3" id="search-btn">
                        <i class="bi bi-search"></i> Search Bill
                    </button>

                    <!-- Loading Spinner -->
                    <div id="loading" class="text-center" style="display:none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Searching...</p>
                    </div>

                    <!-- Results -->
                    <div id="result-container" style="display:none;"></div>

                    <!-- Error Message -->
                    <div id="error-message" class="alert alert-danger mt-3" style="display:none;"></div>

                    <!-- Success Message -->
                    <div id="success-message" class="alert alert-success mt-3" style="display:none;"></div>
                </div>
            </div>

            <!-- Recent Scans -->
            <div class="card shadow mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Scans</h5>
                </div>
                <div class="card-body">
                    <div id="recent-scans">
                        <p class="text-muted">No recent scans</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Result Modal -->
<div class="modal fade" id="resultModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-check-circle"></i> Bill Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modal-result-content"></div>
            </div>
            <div class="modal-footer">
                <a href="#" id="view-bill-btn" class="btn btn-primary">
                    <i class="bi bi-eye"></i> View Full Invoice
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    #barcode-input {
        font-size: 1.2rem;
        letter-spacing: 2px;
        font-weight: 600;
    }

    #barcode-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .bill-detail-item {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }

    .bill-detail-item:last-child {
        border-bottom: none;
    }

    .bill-detail-label {
        font-weight: 600;
        color: #666;
        display: inline-block;
        width: 40%;
    }

    .bill-detail-value {
        font-weight: 500;
        color: #333;
    }

    .recent-scan-item {
        padding: 10px;
        margin: 5px 0;
        background-color: #f8f9fa;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .recent-scan-item:hover {
        background-color: #e9ecef;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeInput = document.getElementById('barcode-input');
    const searchBtn = document.getElementById('search-btn');
    const loadingDiv = document.getElementById('loading');
    const resultContainer = document.getElementById('result-container');
    const errorMessage = document.getElementById('error-message');
    const successMessage = document.getElementById('success-message');
    const resultModal = new bootstrap.Modal(document.getElementById('resultModal'));
    let recentScans = JSON.parse(localStorage.getItem('recentBarcodeScans')) || [];

    // Load recent scans
    loadRecentScans();

    // Search on Enter key
    barcodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchBill();
        }
    });

    // Search button click
    searchBtn.addEventListener('click', searchBill);

    function searchBill() {
        const barcode = barcodeInput.value.trim();

        if (!barcode) {
            showError('Please enter or scan a bill number');
            return;
        }

        loadingDiv.style.display = 'block';
        errorMessage.style.display = 'none';
        successMessage.style.display = 'none';
        resultContainer.style.display = 'none';

        fetch('{{ route("admin.barcode.lookup") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ barcode: barcode })
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';

            if (data.success) {
                displayResult(data.data);
                addToRecentScans(data.data);
                loadRecentScans();
                barcodeInput.value = '';
                barcodeInput.focus();
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            loadingDiv.style.display = 'none';
            showError('Error: ' + error.message);
        });
    }

    function displayResult(data) {
        const content = `
            <div class="bill-detail-item">
                <span class="bill-detail-label">Bill Number:</span>
                <span class="bill-detail-value">${data.bill_number}</span>
            </div>
            <div class="bill-detail-item">
                <span class="bill-detail-label">Patient Name:</span>
                <span class="bill-detail-value">${data.patient_name}</span>
            </div>
            <div class="bill-detail-item">
                <span class="bill-detail-label">Mobile:</span>
                <span class="bill-detail-value">${data.patient_mobile}</span>
            </div>
            <div class="bill-detail-item">
                <span class="bill-detail-label">Date:</span>
                <span class="bill-detail-value">${data.date}</span>
            </div>
            <div class="bill-detail-item">
                <span class="bill-detail-label">Doctor:</span>
                <span class="bill-detail-value">${data.doctor}</span>
            </div>
            <div class="bill-detail-item">
                <span class="bill-detail-label">Tests:</span>
                <span class="bill-detail-value">${data.test_count}</span>
            </div>
            <div class="bill-detail-item">
                <span class="bill-detail-label">Total Amount:</span>
                <span class="bill-detail-value text-primary"><strong>${data.total_amount}</strong></span>
            </div>
            <div class="bill-detail-item">
                <span class="bill-detail-label">Paid Amount:</span>
                <span class="bill-detail-value text-success"><strong>${data.paid_amount}</strong></span>
            </div>
            <div class="bill-detail-item">
                <span class="bill-detail-label">Due Amount:</span>
                <span class="bill-detail-value ${data.due_amount > 0 ? 'text-danger' : 'text-success'}"><strong>${data.due_amount}</strong></span>
            </div>
        `;

        document.getElementById('modal-result-content').innerHTML = content;
        document.getElementById('view-bill-btn').href = data.url;
        resultModal.show();
        showSuccess('Bill found successfully!');
    }

    function addToRecentScans(data) {
        const scan = {
            bill_number: data.bill_number,
            patient_name: data.patient_name,
            timestamp: new Date().toLocaleTimeString(),
            url: data.url
        };

        recentScans.unshift(scan);
        if (recentScans.length > 10) {
            recentScans.pop();
        }
        localStorage.setItem('recentBarcodeScans', JSON.stringify(recentScans));
    }

    function loadRecentScans() {
        const container = document.getElementById('recent-scans');
        const scans = JSON.parse(localStorage.getItem('recentBarcodeScans')) || [];

        if (scans.length === 0) {
            container.innerHTML = '<p class="text-muted">No recent scans</p>';
            return;
        }

        let html = '';
        scans.forEach((scan, index) => {
            html += `
                <div class="recent-scan-item" onclick="goToBill('${scan.url}')">
                    <div>
                        <strong>${scan.bill_number}</strong>
                        <span class="text-muted ms-2">(${scan.patient_name})</span>
                    </div>
                    <small class="text-muted">${scan.timestamp}</small>
                </div>
            `;
        });
        container.innerHTML = html;
    }

    function goToBill(url) {
        window.location.href = url;
    }

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.style.display = 'block';
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 3000);
    }
});
</script>
@endsection
