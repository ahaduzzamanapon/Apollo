@extends('admin.layouts.app')

@section('content')
<style>
    .card-modern {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        transition: transform 0.2s;
        overflow: hidden;
    }
    .card-modern:hover {
        transform: translateY(-5px);
    }
    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-right: 15px;
    }
    .bg-gradient-primary-soft { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .bg-gradient-success-soft { background: linear-gradient(135deg, #2af598 0%, #009efd 100%); color: white; }
    .bg-gradient-danger-soft { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%); color: white; } /* Maybe too light, adjusting */
    .bg-gradient-danger-bold { background: linear-gradient(135deg, #ff512f 0%, #dd2476 100%); color: white; }
    .bg-gradient-info-soft { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); color: white; }
    .bg-gradient-warning-soft { background: linear-gradient(135deg, #f09819 0%, #edde5d 100%); color: white; }
    .bg-gradient-dark-soft { background: linear-gradient(135deg, #434343 0%, #000000 100%); color: white; }
    
    .stat-label { font-size: 0.9rem; opacity: 0.9; }
    .stat-value { font-size: 1.8rem; font-weight: 700; }
</style>

<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-3 text-secondary">Dashboard Overview</h2>
    </div>
</div>

<!-- Daily Status Section -->
<div class="row mb-3">
    <div class="col-12"><h5 class="text-muted mb-3"><i class="bi bi-calendar-day"></i> Today's Summary</h5></div>
    
    <!-- Today's Income -->
    <div class="col-md-3 mb-4">
        <div class="card card-modern bg-gradient-success-soft">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-white bg-opacity-25">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <div class="stat-label">Today's Income</div>
                    <div class="stat-value">{{ number_format($todayIncome) }}</div>
                    <small>Patient Payments</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Expense -->
    <div class="col-md-3 mb-4">
        <div class="card card-modern bg-gradient-danger-bold">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-white bg-opacity-25">
                    <i class="bi bi-cart-x"></i>
                </div>
                <div>
                    <div class="stat-label">Today's Expense</div>
                    <div class="stat-value">{{ number_format($todayExpense) }}</div>
                    <small>Vouchers/Bills</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Balance -->
    <div class="col-md-3 mb-4">
        <div class="card card-modern bg-gradient-primary-soft">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-white bg-opacity-25">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div class="stat-label">Today's Cash Balance</div>
                    <div class="stat-value">{{ number_format($todayBalance) }}</div>
                    <small>Cash In Hand (Net)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Patients -->
    <div class="col-md-3 mb-4">
        <div class="card card-modern bg-dark text-white">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-white bg-opacity-25">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-label">Today's Patients</div>
                    <div class="stat-value">{{ $todayPatients }}</div>
                    <small>New Entries</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Monthly/Total Status Section -->
<div class="row">
    <div class="col-12"><h5 class="text-muted mb-3 mt-2"><i class="bi bi-calendar-month"></i> Monthly & Overall</h5></div>

    <div class="col-md-3 mb-4">
        <div class="card card-modern shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-currency-exchange"></i>
                </div>
                <div>
                    <div class="stat-label text-muted">Total Due</div>
                    <div class="stat-value text-dark">{{ number_format($totalDue) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card card-modern shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div>
                    <div class="stat-label text-muted">Monthly Income</div>
                    <div class="stat-value text-dark">{{ number_format($totalMonthlyIncome) }}</div>
                    <small class="text-success">Profit: {{ number_format($totalProfit) }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card card-modern shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-credit-card"></i>
                </div>
                <div>
                    <div class="stat-label text-muted">Monthly Expense</div>
                    <div class="stat-value text-dark">{{ number_format($totalMonthlyExpenses) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card card-modern shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="card-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-person-hearts"></i>
                </div>
                <div>
                    <div class="stat-label text-muted">Daily Honorarium</div>
                    <div class="stat-value text-dark">{{ number_format($totalDailyHonorarium) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
