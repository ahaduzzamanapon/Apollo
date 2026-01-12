@extends('admin.layouts.app')

@section('content')
<style>
    /* Global Card Style */
    .dashboard-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
        min-height: 150px;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .card-body {
        position: relative;
        z-index: 2;
        padding: 1.5rem;
    }

    /* Icon Styling */
    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 1rem;
    }

    /* Typography */
    .card-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 0.5rem;
        opacity: 0.9;
    }
    .card-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0;
        line-height: 1.2;
    }
    .card-note {
        font-size: 0.8rem;
        margin-top: 0.5rem;
        opacity: 0.8;
    }

    /* Section Headers */
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #555;
        border-left: 4px solid #4e73df;
        padding-left: 10px;
        margin-bottom: 1.5rem;
        margin-top: 1rem;
    }

    /* Daily Section - Vibrant Gradients */
    .bg-daily-income { background: linear-gradient(135deg, #0f9b0f 0%, #52c234 100%); color: white; }
    .bg-daily-expense { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: white; }
    .bg-daily-balance { background: linear-gradient(135deg, #1fa2ff 0%, #12d8fa 100%); color: white; }
    .bg-daily-honorarium { background: linear-gradient(135deg, #f09819 0%, #edde5d 100%); color: white; }
    
    .bg-daily-income .icon-box { background: rgba(255,255,255,0.2); }
    .bg-daily-expense .icon-box { background: rgba(255,255,255,0.2); }
    .bg-daily-balance .icon-box { background: rgba(255,255,255,0.2); }
    .bg-daily-honorarium .icon-box { background: rgba(255,255,255,0.2); }

    /* Monthly Section - Clean Light + Border Accent */
    .bg-monthly { background: #fff; border-left: 5px solid #ccc; }
    .border-income { border-color: #0f9b0f; }
    .border-expense { border-color: #eb3349; }
    .border-profit { border-color: #4e73df; }
    .border-reports { border-color: #6610f2; }

    .bg-monthly .card-value { color: #333; }
    .bg-monthly .card-label { color: #666; }
    
    /* All Time Section - Dark/Solid */
    .bg-all-due { background: linear-gradient(135deg, #FF416C 0%, #FF4B2B 100%); color: white; }
    .bg-all-patients { background: linear-gradient(135deg, #232526 0%, #414345 100%); color: white; }
    .bg-all-due .icon-box { background: rgba(255,255,255,0.2); }
    .bg-all-patients .icon-box { background: rgba(255,255,255,0.2); }

</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="text-secondary"><i class="bi bi-grid-fill"></i> Dashboard</h3>
    <span class="text-muted">{{ date('l, d F Y') }}</span>
</div>

<!-- DAILY CARDS SECTION -->
<style>
    .daily-card {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.2s;
    }
    .daily-card:hover {
        transform: translateY(-5px);
        color: inherit;
    }
    .card-gradient-1 { background: linear-gradient(135deg, #FF9966 0%, #FF5E62 100%); color: white; } /* Due - Reddish/Orange */
    .card-gradient-2 { background: linear-gradient(135deg, #56CCF2 0%, #2F80ED 100%); color: white; } /* Report - Blue */
    .card-gradient-3 { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; } /* Profit - Green */
    .card-gradient-4 { background: linear-gradient(135deg, #F2994A 0%, #F2C94C 100%); color: white; } /* Honorarium - Yellow/Orange */
    .card-gradient-5 { background: linear-gradient(135deg, #EB3349 0%, #F45C43 100%); color: white; } /* Expense - Red */
    .card-gradient-6 { background: linear-gradient(135deg, #0cebeb 0%, #20e3b2 100%, #29ffc6 100%); color: white; } /* Income - Teal/Cyan */
    .card-gradient-7 { background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%); color: white; } /* Patient - Purple */
</style>

<div class="row g-3 mb-4">
    <!-- 1. Total Daily Due -->
    <div class="col-md-3">
        <a href="#" class="daily-card" data-bs-toggle="modal" data-bs-target="#dueModal">
            <div class="card dashboard-card card-gradient-1">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-label text-white">Total Daily Due</div>
                            <div class="card-value text-white">{{ number_format($todayDue) }}</div>
                        </div>
                        <div class="icon-box text-white bg-white bg-opacity-25"><i class="bi bi-exclamation-circle-fill"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- 2. Total Test Report Daily -->
    <div class="col-md-3">
        <a href="#" class="daily-card" data-bs-toggle="modal" data-bs-target="#reportModal">
            <div class="card dashboard-card card-gradient-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-label text-white">Total Test Report Daily</div>
                            <div class="card-value text-white">{{ number_format($todayReports) }}</div>
                        </div>
                        <div class="icon-box text-white bg-white bg-opacity-25"><i class="bi bi-file-earmark-medical-fill"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- 3. Total Daily Profit -->
    <div class="col-md-3">
        <a href="#" class="daily-card" data-bs-toggle="modal" data-bs-target="#profitModal">
            <div class="card dashboard-card card-gradient-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-label text-white">Total Daily Profit</div>
                            <div class="card-value text-white">{{ number_format($todayProfit) }}</div>
                        </div>
                        <div class="icon-box text-white bg-white bg-opacity-25"><i class="bi bi-coin"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- 4. Total Doctor Honorium Daily -->
    <div class="col-md-3">
        <a href="#" class="daily-card" data-bs-toggle="modal" data-bs-target="#honorariumModal">
            <div class="card dashboard-card card-gradient-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-label text-white">Total Honorarium Daily</div>
                            <div class="card-value text-white">{{ number_format($totalDailyHonorarium) }}</div>
                        </div>
                        <div class="icon-box text-white bg-white bg-opacity-25"><i class="bi bi-person-check-fill"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    
    <!-- 5. Total Daily Expenses -->
    <div class="col-md-4">
        <a href="#" class="daily-card" data-bs-toggle="modal" data-bs-target="#expenseModal">
            <div class="card dashboard-card card-gradient-5">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-label text-white">Total Daily Expenses</div>
                            <div class="card-value text-white">{{ number_format($todayExpense) }}</div>
                        </div>
                        <div class="icon-box text-white bg-white bg-opacity-25"><i class="bi bi-cart-x-fill"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- 6. Total Daily Income -->
    <div class="col-md-4">
        <a href="#" class="daily-card" data-bs-toggle="modal" data-bs-target="#incomeModal">
            <div class="card dashboard-card card-gradient-6">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-label text-white">Total Daily Income</div>
                            <div class="card-value text-white">{{ number_format($todayIncome) }}</div>
                        </div>
                        <div class="icon-box text-white bg-white bg-opacity-25"><i class="bi bi-cash"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <!-- 7. Total Daily Patient -->
    <div class="col-md-4">
        <a href="#" class="daily-card" data-bs-toggle="modal" data-bs-target="#patientModal">
            <div class="card dashboard-card card-gradient-7">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="card-label text-white">Total Daily Patient</div>
                            <div class="card-value text-white">{{ number_format($todayPatients) }}</div>
                        </div>
                        <div class="icon-box text-white bg-white bg-opacity-25"><i class="bi bi-people-fill"></i></div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- CHARTS SECTION -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 text-secondary"><i class="bi bi-graph-up"></i> Income vs Expense (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="financeChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
             <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 text-secondary"><i class="bi bi-people"></i> Patient Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="patientChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Finance Chart
    const ctxFinance = document.getElementById('financeChart').getContext('2d');
    new Chart(ctxFinance, {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Income',
                    data: @json($chartIncome),
                    backgroundColor: 'rgba(12, 235, 235, 0.6)', // Teal
                    borderColor: 'rgba(12, 235, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Expense',
                    data: @json($chartExpense),
                    backgroundColor: 'rgba(235, 51, 73, 0.6)', // Red
                    borderColor: 'rgba(235, 51, 73, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Patient Chart
    const ctxPatient = document.getElementById('patientChart').getContext('2d');
    new Chart(ctxPatient, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'New Patients',
                data: @json($chartPatients),
                backgroundColor: 'rgba(142, 45, 226, 0.2)', // Purple
                borderColor: 'rgba(142, 45, 226, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
@endpush

<style>
    /* Fix Nav Dropdown Z-Index */
    /* Ensure the navbar (which is outside this view, but we can target classes) lies above */
    .navbar {
        position: relative;
        z-index: 1050 !important; /* Higher than card z-index */
    }
    
    /* Ensure Modal is above everything */
    .modal {
        z-index: 1060 !important;
    }
    .modal-backdrop {
        z-index: 1055 !important;
    }
</style>
<!-- MODALS -->

<!-- 1. Due Modal -->
<div class="modal fade" id="dueModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-1 text-white" style="background: linear-gradient(135deg, #FF9966 0%, #FF5E62 100%);">
                <h5 class="modal-title">Today's Due List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead><tr><th>Invoice</th><th>Patient</th><th>Amount</th><th>Due</th></tr></thead>
                    <tbody>
                        @foreach($todayDueList as $item)
                        <tr><td>#{{ $item->invoice_no }}</td><td>{{ $item->patient->name ?? '-' }}</td><td>{{ $item->total_amount }}</td><td class="text-danger fw-bold">{{ $item->due_amount }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 2. Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-2 text-white" style="background: linear-gradient(135deg, #56CCF2 0%, #2F80ED 100%);">
                <h5 class="modal-title">Today's Reports</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead><tr><th>Invoice</th><th>Patient</th><th>Tests</th></tr></thead>
                    <tbody>
                        @foreach($todayReportList as $item)
                        <tr><td>#{{ $item->invoice_no }}</td><td>{{ $item->patient->name ?? '-' }}</td><td>{{ $item->tests->count() }} Tests</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 3. Profit Modal -->
<div class="modal fade" id="profitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-3 text-white" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <h5 class="modal-title">Today's Profit Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Income
                        <span class="badge bg-success rounded-pill">{{ number_format($todayIncome) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Total Expense
                        <span class="badge bg-danger rounded-pill">{{ number_format($todayExpense) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                        Net Profit
                        <span class="badge bg-primary rounded-pill">{{ number_format($todayProfit) }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- 4. Honorarium Modal -->
<div class="modal fade" id="honorariumModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-4 text-white" style="background: linear-gradient(135deg, #F2994A 0%, #F2C94C 100%);">
                <h5 class="modal-title">Today's Doctor Honorarium</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead><tr><th>Doctor</th><th>Test</th><th>Patient</th><th>Comm.</th></tr></thead>
                    <tbody>
                        @foreach($dailyHonorariumList as $item)
                        <tr>
                            <td>{{ $item->doctor->name ?? '-' }}</td>
                            <td>{{ $item->test->name ?? '-' }}</td>
                            <td>{{ $item->report->patient->name ?? '-' }}</td>
                            <td class="fw-bold">{{ $item->commission_amount }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 5. Expense Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-5 text-white" style="background: linear-gradient(135deg, #EB3349 0%, #F45C43 100%);">
                <h5 class="modal-title">Today's Expenses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead><tr><th>Description</th><th>Amount</th><th>Method</th></tr></thead>
                    <tbody>
                        @foreach($todayExpenseList as $item)
                        <tr><td>{{ $item->description }}</td><td class="text-danger fw-bold">{{ $item->amount }}</td><td>{{ $item->payment_method }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 6. Income Modal -->
<div class="modal fade" id="incomeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-6 text-white" style="background: linear-gradient(135deg, #0cebeb 0%, #20e3b2 100%);">
                <h5 class="modal-title">Today's Income (Payments)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead><tr><th>Patient</th><th>Amount</th><th>Method</th><th>Time</th></tr></thead>
                    <tbody>
                        @foreach($todayIncomeList as $item)
                        <tr>
                            <td>{{ $item->report->patient->name ?? '-' }}</td>
                            <td class="text-success fw-bold">{{ $item->amount }}</td>
                            <td>{{ $item->payment_method }}</td>
                            <td>{{ $item->created_at->format('h:i A') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 7. Patient Modal -->
<div class="modal fade" id="patientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-7 text-white" style="background: linear-gradient(135deg, #8E2DE2 0%, #4A00E0 100%);">
                <h5 class="modal-title">Today's New Patients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead><tr><th>Name</th><th>Mobile</th><th>Age</th></tr></thead>
                    <tbody>
                        @foreach($todayPatientList as $item)
                        <tr><td>{{ $item->name }}</td><td>{{ $item->mobile }}</td><td>{{ $item->age }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
