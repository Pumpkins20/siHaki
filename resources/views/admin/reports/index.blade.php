@extends('layouts.admin')

@section('title', 'Reports & Analytics')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Reports & Analytics</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Reports & Analytics</h1>
                    <p class="text-muted mb-0">Comprehensive insights and performance metrics</p>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download"></i> Export Reports
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.reports.export', ['type' => 'summary', 'format' => 'excel']) }}">
                            <i class="bi bi-file-earmark-excel"></i> Summary Report (Excel)</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.export', ['type' => 'detailed', 'format' => 'excel']) }}">
                            <i class="bi bi-file-earmark-excel"></i> Detailed Report (Excel)</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.export', ['type' => 'department', 'format' => 'excel']) }}">
                            <i class="bi bi-building"></i> Department Report</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.reports.export', ['type' => 'user', 'format' => 'excel']) }}">
                            <i class="bi bi-people"></i> User Performance Report</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.reports') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-info w-100" onclick="refreshCharts()">
                                    <i class="bi bi-arrow-repeat"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Submissions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_submissions']) }}</div>
                            <div class="text-xs text-success mt-1">
                                <i class="bi bi-arrow-up"></i> All time submissions
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['approved_submissions']) }}</div>
                            <div class="text-xs text-muted mt-1">
                                {{ $stats['total_submissions'] > 0 ? round(($stats['approved_submissions'] / $stats['total_submissions']) * 100, 1) : 0 }}% approval rate
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Review</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_submissions']) }}</div>
                            <div class="text-xs text-warning mt-1">
                                <i class="bi bi-clock"></i> Needs attention
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg Review Time</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $avgReviewTime ? round($avgReviewTime, 1) : 0 }} days
                            </div>
                            <div class="text-xs text-info mt-1">
                                <i class="bi bi-speedometer2"></i> Performance metric
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-graph-up fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Trends Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Monthly Submission Trends</h6>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="updateChart('monthly', 6)">6M</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateChart('monthly', 12)">1Y</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateChart('monthly', 24)">2Y</button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendsChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Status Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" width="100%" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Type Distribution & Department Performance -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Submission by Type</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" width="100%" height="60"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Department Performance</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th class="text-center">Users</th>
                                    <th class="text-center">Submissions</th>
                                    <th class="text-center">Approval Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departmentStats->take(10) as $dept)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $dept->name }}</div>
                                        <small class="text-muted">{{ $dept->code }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $dept->users_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $dept->total_submissions }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($dept->approval_rate >= 80)
                                            <span class="badge bg-success">{{ $dept->approval_rate }}%</span>
                                        @elseif($dept->approval_rate >= 60)
                                            <span class="badge bg-warning">{{ $dept->approval_rate }}%</span>
                                        @else
                                            <span class="badge bg-danger">{{ $dept->approval_rate }}%</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Top Users -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Recent Activities</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($recentActivities as $activity)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="timeline-title">{{ $activity->action }}</div>
                                <div class="timeline-text">
                                    <strong>{{ $activity->user->nama }}</strong> 
                                    @if($activity->submission)
                                        - {{ Str::limit($activity->submission->title, 50) }}
                                    @endif
                                </div>
                                <div class="timeline-date text-muted">
                                    <i class="bi bi-clock"></i> {{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Top Contributors</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Approved</th>
                                    <th class="text-center">Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topUsers as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar me-2">
                                                @if($user->foto && $user->foto !== 'default.png')
                                                    <img src="{{ asset('storage/profile_photos/' . $user->foto) }}" 
                                                         alt="Avatar" class="rounded-circle" width="32" height="32">
                                                @else
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width: 32px; height: 32px;">
                                                        <i class="bi bi-person text-white"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $user->nama }}</div>
                                                <small class="text-muted">{{ $user->program_studi }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $user->total_submissions }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">{{ $user->approved_submissions }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $rate = $user->total_submissions > 0 ? round(($user->approved_submissions / $user->total_submissions) * 100, 1) : 0;
                                        @endphp
                                        @if($rate >= 80)
                                            <span class="badge bg-success">{{ $rate }}%</span>
                                        @elseif($rate >= 60)
                                            <span class="badge bg-warning">{{ $rate }}%</span>
                                        @else
                                            <span class="badge bg-danger">{{ $rate }}%</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart configurations and functions
let monthlyChart, statusChart, typeChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Monthly Trends Chart
    initMonthlyTrendsChart();
    
    // Status Distribution Chart
    initStatusChart();
    
    // Type Distribution Chart
    initTypeChart();
}

function initMonthlyTrendsChart() {
    const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
    
    const monthlyData = @json($monthlyStats);
    
    monthlyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyData.map(item => item.month_name || item.month),
            datasets: [
                {
                    label: 'Total Submissions',
                    data: monthlyData.map(item => item.total),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderWidth: 2,
                    fill: true
                },
                {
                    label: 'Approved',
                    data: monthlyData.map(item => item.approved),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    borderWidth: 2,
                    fill: false
                },
                {
                    label: 'Rejected',
                    data: monthlyData.map(item => item.rejected),
                    borderColor: '#e74a3b',
                    backgroundColor: 'rgba(231, 74, 59, 0.1)',
                    borderWidth: 2,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}

function initStatusChart() {
    const ctx = document.getElementById('statusChart').getContext('2d');
    
    // Fetch data via API
    fetch('{{ route("admin.reports.analytics-api") }}?type=status_distribution')
        .then(response => response.json())
        .then(data => {
            statusChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.status),
                    datasets: [{
                        data: data.map(item => item.count),
                        backgroundColor: data.map(item => item.color),
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        });
}

function initTypeChart() {
    const ctx = document.getElementById('typeChart').getContext('2d');
    
    fetch('{{ route("admin.reports.analytics-api") }}?type=type_distribution')
        .then(response => response.json())
        .then(data => {
            typeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.type),
                    datasets: [{
                        label: 'Submissions',
                        data: data.map(item => item.count),
                        backgroundColor: '#4e73df',
                        borderColor: '#4e73df',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
}

function updateChart(type, period) {
    if (type === 'monthly') {
        fetch(`{{ route("admin.reports.analytics-api") }}?type=monthly_trends&months=${period}`)
            .then(response => response.json())
            .then(data => {
                monthlyChart.data.labels = data.map(item => item.month_name);
                monthlyChart.data.datasets[0].data = data.map(item => item.total);
                monthlyChart.data.datasets[1].data = data.map(item => item.approved);
                monthlyChart.data.datasets[2].data = data.map(item => item.rejected);
                monthlyChart.update();
            });
    }
}

function refreshCharts() {
    if (monthlyChart) monthlyChart.destroy();
    if (statusChart) statusChart.destroy();
    if (typeChart) typeChart.destroy();
    
    initializeCharts();
}
</script>
@endpush

@push('styles')
<style>
.timeline {
    position: relative;
    max-height: 400px;
    overflow-y: auto;
}

.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: 0;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e3e6f0;
}

.timeline-content {
    background: #f8f9fc;
    padding: 12px;
    border-radius: 6px;
    border-left: 3px solid #4e73df;
}

.timeline-title {
    font-weight: 600;
    font-size: 0.875rem;
    margin-bottom: 4px;
}

.timeline-text {
    font-size: 0.8rem;
    margin-bottom: 6px;
}

.timeline-date {
    font-size: 0.75rem;
}

.user-avatar img, .user-avatar div {
    object-fit: cover;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
</style>
@endpush
@endsection