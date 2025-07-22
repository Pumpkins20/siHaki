@extends('layouts.user')

@section('title', 'Riwayat Pengajuan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Riwayat Pengajuan</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Riwayat Pengajuan HKI</h1>
                    <p class="text-muted mb-0">Lihat semua pengajuan HKI yang pernah Anda submit</p>
                </div>
                <div>
                    <a href="{{ route('user.history.export') . '?' . http_build_query(request()->query()) }}" 
                       class="btn btn-success me-2">
                        <i class="bi bi-download"></i> Export CSV
                    </a>
                    <a href="{{ route('user.submissions.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Buat Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-success border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-warning border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-hourglass-split fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-danger border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Revision</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['revision'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-arrow-clockwise fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-funnel me-2"></i>Filter & Pencarian
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('user.history.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="year" class="form-label">Tahun</label>
                                <select name="year" id="year" class="form-select">
                                    <option value="">Semua Tahun</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    @foreach($statuses as $key => $value)
                                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="creation_type" class="form-label">Jenis Ciptaan</label>
                                <select name="creation_type" id="creation_type" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    @foreach($creationTypes as $key => $value)
                                        <option value="{{ $key }}" {{ request('creation_type') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Cari Judul</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Masukkan judul pengajuan..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="sort_by" class="form-label">Urutkan</label>
                                <select name="sort_by" id="sort_by" class="form-select">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Tanggal Buat</option>
                                    <option value="submission_date" {{ request('sort_by') == 'submission_date' ? 'selected' : '' }}>Tanggal Submit</option>
                                    <option value="title" {{ request('sort_by') == 'title' ? 'selected' : '' }}>Judul</option>
                                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label for="sort_order" class="form-label">Order</label>
                                <select name="sort_order" id="sort_order" class="form-select">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>↓</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>↑</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                                <a href="{{ route('user.history.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-list-ul me-2"></i>Daftar Pengajuan
                    </h6>
                    <small class="text-muted">
                        Menampilkan {{ $submissions->firstItem() }} - {{ $submissions->lastItem() }} 
                        dari {{ $submissions->total() }} hasil
                    </small>
                </div>
                <div class="card-body p-0">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Judul & Jenis</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="15%">Reviewer</th>
                                        <th width="10%">Anggota</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $index => $submission)
                                    <tr>
                                        <td>{{ $submissions->firstItem() + $index }}</td>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($submission->title, 40) }}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-tag me-1"></i>
                                                    {{ $submission->creation_type_name ?? $submission->type }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $submission->status === 'approved' ? 'success' : 
                                                ($submission->status === 'rejected' ? 'danger' : 
                                                ($submission->status === 'revision_needed' ? 'warning' : 
                                                ($submission->status === 'draft' ? 'secondary' : 'info'))) 
                                            }}">
                                                {{ $submission->status_name }}
                                            </span>
                                            @if($submission->status === 'approved')
                                                <br><small class="text-success"></small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>Dibuat:</strong><br>
                                                {{ $submission->created_at->format('d M Y') }}
                                                @if($submission->submission_date)
                                                    <br><strong>Submit:</strong><br>
                                                    {{ $submission->submission_date->format('d M Y') }}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($submission->reviewer)
                                                <div class="small">
                                                    <strong>{{ $submission->reviewer->nama }}</strong>
                                                    @if($submission->reviewed_at)
                                                        <br><small class="text-muted">{{ $submission->reviewed_at->format('d M Y') }}</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($submission->members->count() > 0)
                                                <span class="badge bg-info">{{ $submission->members->count() }} orang</span>
                                                <div class="small mt-1">
                                                    <strong>{{ $submission->members->where('is_leader', true)->first()->name ?? 'N/A' }}</strong>
                                                    @if($submission->members->count() > 1)
                                                        <br><small class="text-muted">+{{ $submission->members->count() - 1 }} lainnya</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical" role="group">
                                                <a href="{{ route('user.submissions.show', $submission) }}" 
                                                   class="btn btn-sm btn-outline-primary mb-1" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                                
                                                @if($submission->status === 'approved')
                                                    <a href="{{ route('user.history.certificate', $submission) }}" 
                                                       class="btn btn-sm btn-success mb-1" title="Download Sertifikat">
                                                        <i class="bi bi-download"></i> Sertifikat
                                                    </a>
                                                @endif
                                                
                                                @if($submission->documents->count() > 0)
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                type="button" data-bs-toggle="dropdown">
                                                            <i class="bi bi-file-earmark-arrow-down"></i> Dokumen
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @foreach($submission->documents as $document)
                                                                <li>
                                                                    <a class="dropdown-item" 
                                                                       href="{{ route('user.history.document', [$submission, $document]) }}">
                                                                        <i class="bi bi-file-earmark"></i> 
                                                                        {{ Str::limit($document->file_name, 25) }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $submissions->appends(request()->query())->links('custom.pagination') }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <h5 class="mt-2 text-muted">Tidak ada data ditemukan</h5>
                            <p class="text-muted">Coba ubah filter pencarian atau buat pengajuan baru</p>
                            <a href="{{ route('user.submissions.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Buat Pengajuan Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when select changes
    const selects = document.querySelectorAll('#year, #status, #creation_type, #sort_by, #sort_order');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Search input with delay
    const searchInput = document.getElementById('search');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 1000); // 1 second delay
    });

    // Prevent form submission on Enter key in search
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(searchTimeout);
            document.getElementById('filterForm').submit();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.table th {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    font-weight: 600;
    color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,.075);
}

.btn-group-vertical .btn {
    border-radius: 0.25rem !important;
    margin-bottom: 2px;
}

.dropdown-menu {
    max-height: 200px;
    overflow-y: auto;
}

.badge {
    font-size: 0.75em;
}

@media (max-width: 768px) {
    .btn-group-vertical .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .table-responsive {
        font-size: 0.875rem;
    }
}
</style>
@endpush
@endsection