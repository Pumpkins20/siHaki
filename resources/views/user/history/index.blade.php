@extends('layouts.user')

@section('title', 'Riwayat Pengajuan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Riwayat Pengajuan HKI</h1>
                    <p class="text-muted">Pengajuan yang sudah selesai diproses (Approved & Rejected)</p>
                </div>
                <a href="{{ route('user.submissions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Pengajuan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fs-2 text-green-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-x-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.history.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="year" class="form-label">Tahun</label>
                                <select name="year" id="year" class="form-select">
                                    <option value="">Semua Tahun</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="creation_type" class="form-label">Jenis Ciptaan</label>
                                <select name="creation_type" id="creation_type" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    @foreach($creationTypes as $type)
                                        <option value="{{ $type }}" {{ request('creation_type') == $type ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Pencarian</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Cari judul..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Riwayat Pengajuan ({{ $submissions->total() }} riwayat)
                    </h6>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">Judul & Deskripsi</th>
                                        <th width="12%">Anggota Pencipta</th>
                                        <th width="12%">Jenis Ciptaan</th>
                                        <th width="12%">Status</th>
                                        <th width="12%">Tanggal</th>
                                        <th width="17%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $index => $submission)
                                    @php
                                        $statusColor = \App\Helpers\StatusHelper::getStatusColor($submission->status);
                                        $statusIcon = \App\Helpers\StatusHelper::getStatusIcon($submission->status);
                                        $statusName = \App\Helpers\StatusHelper::getStatusName($submission->status);
                                    @endphp
                                    <tr>
                                        <td>{{ $submissions->firstItem() + $index }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ Str::limit($submission->title, 50) }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($submission->description, 80) }}</small>
                                            </div>
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
                                            <span class="badge bg-secondary">
                                                {{ ucfirst(str_replace('_', ' ', $submission->creation_type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $statusColor }}">
                                                <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusName }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>Submit:</strong><br>
                                                {{ $submission->submission_date->format('d M Y') }}
                                                @if($submission->reviewed_at)
                                                    <br><strong>Review:</strong><br>
                                                    {{ $submission->reviewed_at->format('d M Y') }}
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical d-grid gap-1" role="group">
                                                <!-- Detail Button -->
                                                <a href="{{ route('user.submissions.show', $submission) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                    <i class="bi bi-eye me-1"></i>Detail
                                                </a>
                                                
                                                <!-- Certificate Button -->
                                                @if($submission->status === 'approved')
                                                    @php
                                                        $certificate = $submission->documents()->where('document_type', 'certificate')->first();
                                                        $hasCertificate = $certificate !== null;
                                                    @endphp
                                                    
                                                    @if($hasCertificate)
                                                        <a href="{{ route('user.submissions.documents.download', $certificate) }}" 
                                                           class="btn btn-sm btn-success" 
                                                           title="Download Sertifikat">
                                                            <i class="bi bi-download me-1"></i>Sertifikat
                                                        </a>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary" 
                                                                title="Sertifikat sedang diproses"
                                                                disabled>
                                                            <i class="bi bi-hourglass-split me-1"></i>Proses
                                                        </button>
                                                    @endif
                                                @endif
                                                
                                                <!-- Print Summary Button -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info"
                                                        onclick="printSummary({{ $submission->id }})"
                                                        title="Print Ringkasan">
                                                    <i class="bi bi-printer me-1"></i>Print
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-3">
                            {{ $submissions->appends(request()->query())->links('custom.pagination') }}
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-5">
                            <i class="bi bi-archive fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada riwayat pengajuan</h5>
                            <p class="text-muted mb-4">Riwayat akan muncul setelah pengajuan selesai diproses</p>
                            <a href="{{ route('user.submissions.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle me-2"></i>Buat Pengajuan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Summary Modal -->
<div class="modal fade" id="printModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ringkasan Pengajuan HKI</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="printContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto submit form on filter change
    document.querySelectorAll('#status, #year, #creation_type').forEach(function(element) {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Search with delay
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            // Optional: Auto-submit search after delay
            // this.form.submit();
        }, 1000);
    });

    // Enhanced filter reset
    if (window.location.search) {
        const resetBtn = document.createElement('button');
        resetBtn.type = 'button';
        resetBtn.className = 'btn btn-outline-secondary btn-sm mt-2';
        resetBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Reset Filter';
        resetBtn.onclick = function() {
            window.location.href = '{{ route("user.history.index") }}';
        };
        
        const formElement = document.querySelector('form');
        if (formElement) {
            formElement.appendChild(resetBtn);
        }
    }
});

// Print summary function
function printSummary(submissionId) {
    // Show loading
    const modal = new bootstrap.Modal(document.getElementById('printModal'));
    const printContent = document.getElementById('printContent');
    
    printContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat data...</p>
        </div>
    `;
    
    modal.show();
    
    // Fetch submission data (you'll need to create this endpoint)
    fetch(`/user/submissions/${submissionId}/summary`)
        .then(response => response.json())
        .then(data => {
            printContent.innerHTML = `
                <div class="print-summary">
                    <div class="text-center mb-4">
                        <h4>RINGKASAN PENGAJUAN HKI</h4>
                        <p class="text-muted">STMIK AMIKOM Surakarta</p>
                    </div>
                    
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%"><strong>ID Pengajuan</strong></td>
                            <td>: #${data.id}</td>
                        </tr>
                        <tr>
                            <td><strong>Judul</strong></td>
                            <td>: ${data.title}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis Ciptaan</strong></td>
                            <td>: ${data.creation_type}</td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong></td>
                            <td>: <span class="badge bg-${data.status_color}">${data.status_name}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Submit</strong></td>
                            <td>: ${data.submission_date}</td>
                        </tr>
                        <tr>
                            <td><strong>Anggota Pencipta</strong></td>
                            <td>: ${data.members.join(', ')}</td>
                        </tr>
                    </table>
                    
                    <div class="mt-4">
                        <p class="small text-muted text-center">
                            Dicetak pada: ${new Date().toLocaleString('id-ID')} WIB
                        </p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            printContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Gagal memuat data. Silakan coba lagi.
                </div>
            `;
        });
}
</script>
@endpush

@push('styles')
<style>
/* Table enhancements */
.table th {
    background-color: #f8f9fc;
    border-top: none;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    color: #5a5c69;
}

.table td {
    vertical-align: middle;
    padding: 1rem 0.75rem;
}

/* Button group improvements */
.btn-group-vertical .btn {
    margin-bottom: 0;
}

/* Badge improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}

/* Print styles */
@media print {
    .modal-header,
    .modal-footer {
        display: none !important;
    }
    
    .print-summary {
        font-size: 12px;
    }
    
    .print-summary h4 {
        font-size: 16px;
        margin-bottom: 20px;
    }
    
    .table td,
    .table th {
        padding: 8px;
        border: 1px solid #ddd;
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .btn-group-vertical {
        width: 100%;
    }
    
    .btn-group-vertical .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .table td {
        padding: 0.5rem 0.25rem;
        font-size: 0.85rem;
    }
}

/* Statistics card improvements */
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}

.text-green-300 {
    color: #1cc88a !important;
}

/* Filter section improvements */
.card-body form .row {
    align-items: end;
}

/* Loading and empty states */
.spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.empty-state {
    padding: 3rem 1rem;
}
</style>
@endpush