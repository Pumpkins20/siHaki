@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8 col-12">
                            <h4 class="card-title mb-2 mb-md-1">Selamat Datang, {{ Auth::user()->nama }}!</h4>
                            <p class="card-text mb-3 mb-md-0">Dashboard Super Admin - Kelola sistem HKI dan review submission.</p>
                        </div>
                        <div class="col-md-4 col-12 text-md-end text-start">
                            <a href="{{ route('admin.submissions.index') }}" class="btn btn-light btn-lg w-100 w-md-auto">
                                <i class="bi bi-file-earmark-check"></i> Review Submissions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <!-- Total Users -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Submissions -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Submissions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_submissions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Reviews
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_reviews'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Under Review -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Under Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['under_review'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-eye fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities & Quick Actions -->
    <div class="row">
        <!-- Recent Submissions -->
        <div class="col-xl-8 col-lg-12 mb-4 order-2 order-xl-1">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
                    <h6 class="m-0 font-weight-bold text-primary mb-2 mb-sm-0">Antrian Tinjauan Terbaru</h6>
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($recent_submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="d-none d-md-table-header-group">
                                    <tr>
                                        <th>Title</th>
                                        <th>User</th>
                                        <th class="d-none d-lg-table-cell">Type</th>
                                        <th>Status</th>
                                        <th class="d-none d-lg-table-cell">Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_submissions as $submission)
                                    @php
                                        $statusColor = App\Helpers\StatusHelper::getStatusColor($submission->status);
                                        $statusIcon = App\Helpers\StatusHelper::getStatusIcon($submission->status);
                                        $statusName = App\Helpers\StatusHelper::getStatusName($submission->status);
                                    @endphp
                                    <tr>
                                        <!-- Mobile Layout -->
                                        <td class="d-md-none">
                                            <div class="mb-1">
                                                <strong>{{ Str::limit($submission->title, 25) }}</strong>
                                            </div>
                                            <div class="small text-muted mb-1">{{ $submission->user->nama }}</div>
                                            <div class="mb-2">
                                                <span class="badge bg-{{ $statusColor }}">
                                                    <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusName }}
                                                </span>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                            </div>
                                        </td>

                                        <!-- Desktop Layout -->
                                        <td class="d-none d-md-table-cell">
                                            <strong>{{ Str::limit($submission->title, 30) }}</strong>
                                        </td>
                                        <td class="d-none d-md-table-cell">{{ $submission->user->nama }}</td>
                                        <td class="d-none d-lg-table-cell">
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $submission->creation_type)) }}</span>
                                        </td>
                                        <td class="d-none d-md-table-cell">
                                            <span class="badge bg-{{ $statusColor }}">
                                                <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusName }}
                                            </span>
                                            @if($submission->status === 'submitted')
                                                <br><small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Perlu action</small>
                                            @elseif($submission->status === 'revision_needed')
                                                <br><small class="text-warning"><i class="bi bi-clock"></i> Menunggu user</small>
                                            @endif
                                        </td>
                                        <td class="d-none d-lg-table-cell">{{ $submission->submission_date->format('d M Y') }}</td>
                                        <td class="d-none d-md-table-cell">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($submission->status === 'submitted')
                                                    <form action="{{ route('admin.submissions.assign-to-self', $submission) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Assign to Self">
                                                            <i class="bi bi-person-check"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No recent submissions</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="col-xl-4 col-lg-12 mb-4 order-1 order-xl-2">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                            <i class="bi bi-person-plus"></i> Tambah User Baru
                        </a>
                        <a href="{{ route('admin.submissions.index', ['status' => 'submitted']) }}" class="btn btn-warning">
                            <i class="bi bi-file-earmark-check"></i> Review Pending
                        </a>
                        <a href="{{ route('admin.certificates.index') }}" class="btn btn-info">
                            <i class="bi bi-award"></i> Kirim Sertifikat
                        </a>
                        <a href="{{ route('admin.review-history.index') }}" class="btn btn-secondary">
                            <i class="bi bi-clock-history"></i> Riwayat Peninjauan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Today's Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Today's Activity</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="h4 mb-0 text-success">{{ $stats['approved_today'] }}</div>
                            <small class="text-muted">Approved Today</small>
                        </div>
                        <div class="col-6">
                            <div class="h4 mb-0 text-primary">{{ $stats['my_reviews'] }}</div>
                            <small class="text-muted">My Reviews</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Status</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <div class="icon-circle bg-success text-white me-2">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div>
                                    <div class="h6 mb-0">{{ $stats['approved'] ?? 0 }}</div>
                                    <small class="text-success">Approved</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <div class="icon-circle bg-danger text-white me-2">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                                <div>
                                    <div class="h6 mb-0">{{ $stats['rejected'] ?? 0 }}</div>
                                    <small class="text-danger">Rejected</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <div class="icon-circle bg-warning text-white me-2">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </div>
                                <div>
                                    <div class="h6 mb-0">{{ $stats['revision_needed'] ?? 0 }}</div>
                                    <small class="text-warning">Revision</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="d-flex align-items-center p-2 bg-light rounded">
                                <div class="icon-circle bg-primary text-white me-2">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div>
                                    <div class="h6 mb-0">{{ $stats['submitted'] ?? 0 }}</div>
                                    <small class="text-primary">Submitted</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Information</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p class="mb-1"><strong>Laravel:</strong> {{ app()->version() }}</p>
                        <p class="mb-1"><strong>PHP:</strong> {{ phpversion() }}</p>
                        <p class="mb-1"><strong>Time:</strong> {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</p>
                        <p class="mb-0"><strong>DB:</strong> {{ config('database.default') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ✅ Base Styles */
.bg-gradient-primary {
    background: linear-gradient(87deg, #667eea 0%, #764ba2 100%);
}

/* ✅ Border Left Colors */
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

/* ✅ Card Hover Effects */
.card:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

/* ✅ Icon Circle */
.icon-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0; /* Prevent shrinking */
}

/* ✅ Badge Styling */
.badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
    font-weight: 500;
}

.badge i {
    margin-right: 0.25rem;
}

/* ✅ Table Improvements */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

.table-responsive {
    border-radius: 0.375rem;
}

/* ✅ RESPONSIVE FIXES */

/* Desktop (Large screens) */
@media (min-width: 1200px) {
    .main-content {
        margin-left: 250px;
        padding: 1.5rem;
    }
}

/* Tablet (Medium screens) */
@media (max-width: 1199.98px) and (min-width: 768px) {
    .main-content {
        margin-left: 0;
        padding: 1rem;
    }
    
    /* Stack cards vertically on tablet */
    .col-xl-3 {
        margin-bottom: 1rem;
    }
    
    /* Adjust stats cards */
    .card-body .row.no-gutters {
        align-items: center;
    }
    
    .card-body .h5 {
        font-size: 1.1rem;
    }
}

/* Mobile (Small screens) */
@media (max-width: 767.98px) {
    .main-content {
        margin-left: 0;
        padding: 0.75rem;
    }
    
    /* Header adjustments */
    .card bg-gradient-primary .card-body {
        padding: 1rem;
    }
    
    .card bg-gradient-primary h4 {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    .card bg-gradient-primary p {
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    /* Stack header content */
    .col-md-4.text-end {
        text-align: left !important;
        margin-top: 1rem;
    }
    
    /* Stats cards improvements */
    .col-xl-3.col-md-6 {
        margin-bottom: 1rem;
    }
    
    .card.border-left-primary,
    .card.border-left-success,
    .card.border-left-warning,
    .card.border-left-info {
        border-left: 0.2rem solid;
        border-top: 0.2rem solid;
    }
    
    .card-body .py-2 {
        padding: 1rem !important;
    }
    
    .text-xs {
        font-size: 0.7rem !important;
    }
    
    .h5.mb-0 {
        font-size: 1.1rem;
    }
    
    /* Icon adjustments */
    .bi.fs-2 {
        font-size: 1.5rem !important;
    }
    
    /* Table responsive */
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .table th,
    .table td {
        padding: 0.5rem 0.25rem;
        vertical-align: middle;
    }
    
    /* Hide some table columns on mobile */
    .table th:nth-child(3),
    .table td:nth-child(3),
    .table th:nth-child(5),
    .table td:nth-child(5) {
        display: none;
    }
    
    /* Adjust buttons */
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .btn-lg {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    }
    
    /* Status summary grid */
    .row.g-2 > .col-6 {
        margin-bottom: 0.5rem;
    }
    
    .icon-circle {
        width: 28px;
        height: 28px;
        font-size: 12px;
    }
    
    .h6.mb-0 {
        font-size: 0.9rem;
    }
    
    /* Quick actions */
    .d-grid.gap-2 .btn {
        padding: 0.6rem;
        font-size: 0.9rem;
    }
    
    /* System info */
    .card-body .small {
        font-size: 0.8rem;
    }
    
    /* Badge adjustments */
    .badge {
        font-size: 0.65em;
        padding: 0.35em 0.55em;
    }
}

/* Extra small screens */
@media (max-width: 575.98px) {
    .main-content {
        padding: 0.5rem;
    }
    
    /* Further reduce spacing */
    .mb-4 {
        margin-bottom: 1rem !important;
    }
    
    .py-3 {
        padding-top: 0.75rem !important;
        padding-bottom: 0.75rem !important;
    }
    
    /* Stack all columns */
    .col-xl-8,
    .col-xl-4 {
        margin-bottom: 1rem;
    }
    
    /* Reduce card padding */
    .card-body {
        padding: 0.75rem;
    }
    
    .card-header {
        padding: 0.5rem 0.75rem;
    }
    
    /* Header title */
    .h6.m-0 {
        font-size: 0.9rem;
    }
    
    /* Today's stats */
    .border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        padding-bottom: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .row.text-center > .col-6 {
        margin-bottom: 0.5rem;
    }
    
    /* No gutters fix */
    .no-gutters {
        margin: 0;
    }
    
    .no-gutters > .col,
    .no-gutters > [class*="col-"] {
        padding: 0;
    }
}

/* ✅ Print Styles */
@media print {
    .sidebar,
    .btn,
    .pagination {
        display: none !important;
    }
    
    .main-content {
        margin-left: 0 !important;
    }
    
    .card {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
}

/* ✅ Focus and Accessibility */
.btn:focus,
.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    border-color: #667eea;
}

/* ✅ Loading States */
.card.loading {
    opacity: 0.7;
    pointer-events: none;
}

.card.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush
@endsection