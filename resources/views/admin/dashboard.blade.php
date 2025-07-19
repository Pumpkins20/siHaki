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
                        <div class="col-md-8">
                            <h4 class="card-title">Selamat Datang, {{ Auth::user()->nama }}!</h4>
                            <p class="card-text">Dashboard Super Admin - Kelola sistem HKI dan review submission.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.submissions.index') }}" class="btn btn-light btn-lg">
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
        <div class="col-xl-3 col-md-6 mb-4">
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
        <div class="col-xl-3 col-md-6 mb-4">
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
        <div class="col-xl-3 col-md-6 mb-4">
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
        <div class="col-xl-3 col-md-6 mb-4">
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
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Antrian Tinjauan Terbaru</h6>
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($recent_submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_submissions as $submission)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($submission->title, 30) }}</strong>
                                        </td>
                                        <td>{{ $submission->user->nama }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($submission->creation_type) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $submission->status === 'submitted' ? 'warning' : ($submission->status === 'under_review' ? 'info' : 'success') }}">
                                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $submission->submission_date->format('d M Y') }}</td>
                                        <td>
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
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No recent submissions</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Stats -->
        <div class="col-xl-4 col-lg-5">
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

            <!-- System Info -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Information</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p class="mb-1"><strong>Laravel Version:</strong> {{ app()->version() }}</p>
                        <p class="mb-1"><strong>PHP Version:</strong> {{ phpversion() }}</p>
                        <p class="mb-1"><strong>Server Time:</strong> {{ now()->format('d M Y H:i:s') }}</p>
                        <p class="mb-0"><strong>Database:</strong> {{ config('database.default') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(87deg, #667eea 0%, #764ba2 100%);
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

.card:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
</style>
@endpush
@endsection