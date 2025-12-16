@extends('admin.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <h2>Dashboard</h2>
    </div>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Total Due</h5>
                <h2 class="display-4">{{ number_format($totalDue) }}</h2>
                <p>TK</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Monthly Income</h5>
                <h2 class="display-4">{{ number_format($totalMonthlyIncome) }}</h2>
                <p>Paid Amount (This Month)</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-danger text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Monthly Expenses</h5>
                <h2 class="display-4">{{ number_format($totalMonthlyExpenses) }}</h2>
                <p>Total Expenses (This Month)</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-dark h-100">
            <div class="card-body">
                <h5 class="card-title">Total Profit</h5>
                <h2 class="display-4">{{ number_format($totalProfit) }}</h2>
                <p>Income - Expenses</p>
            </div>
        </div>
    </div>

     <div class="col-md-3 mb-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <h5 class="card-title">Daily Honorarium</h5>
                <h2 class="display-4">{{ number_format($totalDailyHonorarium) }}</h2>
                <p>Doctor's Commission (Today)</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-secondary text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Monthly Reports</h5>
                <h2 class="display-4">{{ $totalMonthlyReports }}</h2>
                <p>Total Tests Done</p>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-dark text-white h-100">
            <div class="card-body">
                <h5 class="card-title">Total Patients</h5>
                <h2 class="display-4">{{ $totalPatients }}</h2>
                <p>Registered Patients</p>
            </div>
        </div>
    </div>
</div>
@endsection
