@extends('admin.layouts.app')

@section('content')
<style>
    /* Uniform Card Sizing & Modern Style */
    .dashboard-card {
        height: 100%;
        min-height: 140px;
        border: none;
        border-radius: 15px; /* Softer radius */
        box-shadow: 0 4px 20px rgba(0,0,0,0.05); /* Softer shadow */
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.2s;
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .card-body {
        display: flex;
        align-items: center; /* Align Icon and Text */
        padding: 1.5rem;
        width: 100%;
    }
    
    /* Icon Definitions */
    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 15px;
        flex-shrink: 0;
    }

    /* Text Definitions */
    .stat-content {
        flex-grow: 1;
    }
    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .stat-label {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.85;
        margin-bottom: 2px;
    }
    .stat-note {
        font-size: 0.8rem;
        opacity: 0.7;
    }

    /* Daily Gradient Cards */
    .bg-gradient-daily-income { background: linear-gradient(135deg, #11998e, #38ef7d); color: white; }
    .bg-gradient-daily-expense { background: linear-gradient(135deg, #ff512f, #dd2476); color: white; }
    .bg-gradient-daily-balance { background: linear-gradient(135deg, #2193b0, #6dd5ed); color: white; }
    .bg-gradient-daily-patients { background: linear-gradient(135deg, #1f1c2c, #928dab); color: white; }
    
    /* Monthly/Total Cards (White Modern) */
    .bg-white-modern { background: white; color: #333; }
</style>

<div class="row mb-3">
    <div class="col-12"><h4 class="text-secondary"><i class="bi bi-speedometer2"></i> Dashboard Overview</h4></div>
</div>

<!-- Daily Stats Row (Gradients) -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card dashboard-card bg-gradient-daily-income">
            <div class="card-body">
                <div class="card-icon bg-white bg-opacity-25"><i class="bi bi-cash-coin"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Today's Income</div>
                    <div class="stat-value">{{ number_format($todayIncome) }}</div>
                    <div class="stat-note">Patient Payments</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card bg-gradient-daily-expense">
            <div class="card-body">
                <div class="card-icon bg-white bg-opacity-25"><i class="bi bi-cart-x"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Today's Expense</div>
                    <div class="stat-value">{{ number_format($todayExpense) }}</div>
                    <div class="stat-note">Vouchers/Bills</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card bg-gradient-daily-balance">
            <div class="card-body">
                <div class="card-icon bg-white bg-opacity-25"><i class="bi bi-wallet2"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Today's Balance</div>
                    <div class="stat-value">{{ number_format($todayBalance) }}</div>
                    <div class="stat-note">Cash In Hand</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card bg-gradient-daily-patients">
            <div class="card-body">
                <div class="card-icon bg-white bg-opacity-25"><i class="bi bi-people"></i></div>
                <div class="stat-content">
                    <div class="stat-label">Today's Patients</div>
                    <div class="stat-value">{{ $todayPatients }}</div>
                    <div class="stat-note">New Registrations</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly/Total Stats Row (Modern White Style) -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card dashboard-card bg-white-modern">
            <div class="card-body">
                <div class="card-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-currency-exchange"></i></div>
                <div class="stat-content">
                    <div class="stat-label text-muted">Total Due</div>
                    <div class="stat-value text-dark">{{ number_format($totalDue) }}</div>
                    <div class="stat-note text-muted">Receivable Amount</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card bg-white-modern">
            <div class="card-body">
                <div class="card-icon bg-info bg-opacity-10 text-info"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-content">
                    <div class="stat-label text-muted">Monthly Income</div>
                    <div class="stat-value text-dark">{{ number_format($totalMonthlyIncome) }}</div>
                    <div class="stat-note text-success">Profit: {{ number_format($totalProfit) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card bg-white-modern">
            <div class="card-body">
                <div class="card-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-credit-card"></i></div>
                <div class="stat-content">
                    <div class="stat-label text-muted">Monthly Expense</div>
                    <div class="stat-value text-dark">{{ number_format($totalMonthlyExpenses) }}</div>
                    <div class="stat-note text-muted">This Month</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card bg-white-modern">
            <div class="card-body">
                <div class="card-icon bg-success bg-opacity-10 text-success"><i class="bi bi-person-hearts"></i></div>
                <div class="stat-content">
                    <div class="stat-label text-muted">Daily Honorarium</div>
                    <div class="stat-value text-dark">{{ number_format($totalDailyHonorarium) }}</div>
                    <div class="stat-note text-muted">Doctor Comm.</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Extra Stats -->
    <div class="col-md-3">
        <div class="card dashboard-card bg-white-modern">
            <div class="card-body">
                <div class="card-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-file-earmark-text"></i></div>
                <div class="stat-content">
                    <div class="stat-label text-muted">Monthly Reports</div>
                    <div class="stat-value text-dark">{{ $totalMonthlyReports }}</div>
                    <div class="stat-note text-muted">Tests Conducted</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card dashboard-card bg-white-modern">
            <div class="card-body">
                <div class="card-icon bg-dark bg-opacity-10 text-dark"><i class="bi bi-people-fill"></i></div>
                <div class="stat-content">
                    <div class="stat-label text-muted">Total Patients</div>
                    <div class="stat-value text-dark">{{ $totalPatients }}</div>
                    <div class="stat-note text-muted">All Time</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-secondary"><i class="bi bi-graph-up"></i> Income vs Expense (Last 7 Days)</h5>
            </div>
            <div class="card-body">
                <canvas id="incomeExpenseChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-secondary"><i class="bi bi-pie-chart"></i> Overview</h5>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center">
                <canvas id="overviewChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Daily Data Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="mb-0 text-secondary"><i class="bi bi-list-task"></i> Today's Transactions</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Time</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th class="text-end pe-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayTransactions as $trans)
                            <tr>
                                <td class="ps-4 text-muted">{{ date('h:i A', strtotime($trans['time'])) }}</td>
                                <td>{{ $trans['desc'] }}</td>
                                <td><span class="{{ $trans['class'] }} fw-medium px-2 py-1 rounded bg-light">{{ $trans['type'] }}</span></td>
                                <td class="text-end pe-4 fw-bold text-dark">{{ number_format($trans['amount'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No transactions found for today.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Income vs Expense Chart
    const ctx = document.getElementById('incomeExpenseChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartDates) !!}, // ['12 Dec', '13 Dec'...]
            datasets: [{
                label: 'Income',
                data: {!! json_encode($chartIncome) !!},
                borderColor: '#2af598',
                backgroundColor: 'rgba(42, 245, 152, 0.1)',
                tension: 0.3,
                fill: true
            }, {
                label: 'Expense',
                data: {!! json_encode($chartExpense) !!},
                borderColor: '#ff512f',
                backgroundColor: 'rgba(255, 81, 47, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Overview Pie Chart (Income vs Expense Total)
    const ctx2 = document.getElementById('overviewChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Total Income', 'Total Expense'],
            datasets: [{
                data: [{{ array_sum($chartIncome) }}, {{ array_sum($chartExpense) }}],
                backgroundColor: ['#2af598', '#ff512f'],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection
