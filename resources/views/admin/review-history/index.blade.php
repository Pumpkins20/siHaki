<<<<<<< HEAD

=======
>>>>>>> backend
@extends('layouts.admin')

@section('title', 'Riwayat Peninjauan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Riwayat Peninjauan</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Riwayat Peninjauan</h1>
                    <p class="text-muted mb-0">Lihat semua riwayat peninjauan submission yang telah selesai</p>
                </div>
                <div>
                    <a href="{{ route('admin.review-history.export', request()->query()) }}" class="btn btn-success">
                        <i class="bi bi-download"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Reviewed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_reviewed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-eye fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                My Reviews
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['my_reviews'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.review-history.index') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="reviewer_id" class="form-label">Reviewer</label>
                                <select name="reviewer_id" id="reviewer_id" class="form-select">
                                    <option value="">Semua Reviewer</option>
                                    @foreach($reviewers as $reviewer)
                                        <option value="{{ $reviewer->id }}" {{ request('reviewer_id') == $reviewer->id ? 'selected' : '' }}>
                                            {{ $reviewer->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">Dari Tanggal</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">Sampai Tanggal</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Cari</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Cari judul atau nama user..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Review History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Peninjauan ({{ $submissions->total() }} items)</h6>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Judul HKI</th>
                                        <th width="15%">User</th>
                                        <th width="10%">Jenis</th>
                                        <th width="10%">Status</th>
                                        <th width="15%">Reviewer</th>
                                        <th width="12%">Tanggal Review</th>
                                        <th width="8%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $index => $submission)
<<<<<<< HEAD
=======
                                    @php
                                        $statusColor = StatusHelper::getStatusColor($submission->status);
                                        $statusIcon = StatusHelper::getStatusIcon($submission->status);
                                        $statusName = StatusHelper::getStatusName($submission->status);
                                    @endphp
>>>>>>> backend
                                    <tr>
                                        <td>{{ $submissions->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ Str::limit($submission->title, 40) }}</strong>
                                            <br><small class="text-muted">ID: #{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}</small>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $submission->user->nama }}</strong>
                                                <br><small class="text-muted">{{ $submission->user->nidn }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $submission->creation_type)) }}</span>
                                        </td>
                                        <td>
<<<<<<< HEAD
                                            @if($submission->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
=======
                                            <span class="badge bg-{{ $statusColor }}">
                                                <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusName }}
                                            </span>
>>>>>>> backend
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $submission->reviewer->nama }}</strong>
                                                @if($submission->reviewer_id === Auth::id())
                                                    <br><span class="badge bg-primary">Anda</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $submission->reviewed_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.submissions.show', $submission) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $submissions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <h5 class="mt-2 text-muted">Tidak ada riwayat peninjauan ditemukan</h5>
                            <p class="text-muted">Coba ubah filter pencarian</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection