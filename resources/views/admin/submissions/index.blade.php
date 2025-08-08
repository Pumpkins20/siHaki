@extends('layouts.admin')

@section('title', 'Kelola Submission')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Tinjau Pengajuan</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Tinjau Pengajuan HKI</h1>
                    <p class="text-muted mb-0">Tinjau dan kelola pengajuan yang sedang dalam proses</p>
                </div>
                <div>
<<<<<<< Updated upstream
                   <!-- 
                    <div class="btn-group" role="group">
=======
                   <!--<div class="btn-group" role="group">
>>>>>>> Stashed changes
                        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.submissions.export', array_merge(request()->all(), ['format' => 'xlsx'])) }}">
                                    <i class="bi bi-file-earmark-excel"></i> Export Excel (.xlsx)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.submissions.export', array_merge(request()->all(), ['format' => 'csv'])) }}">
                                    <i class="bi bi-file-earmark-text"></i> Export CSV
                                </a>
                            </li>
                        </ul>
<<<<<<< Updated upstream
                    </div>-->
=======
                    </div> -->
>>>>>>> Stashed changes
                    <div class="btn-group" role="group">
                       
                        {{-- ✅ NEW: Add link to view all submissions (including completed) --}}
                        <a href="{{ route('admin.review-history.index') }}" class="btn btn-outline-info">
                            <i class="bi bi-clock-history"></i> Riwayat Tinjauan
                        </a>
                    </div>
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Pengajuan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] ?? 0 }}</div>
<<<<<<< Updated upstream
                            <div class="text-xs text-muted">Pengajuan dalam proses</div>
=======
                            <div class="text-xs text-muted">Pengajuan Masuk</div>
>>>>>>> Stashed changes
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-text fs-2 text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Perlu Ditunjau</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['need_review'] ?? 0 }}</div>
                            <div class="text-xs text-muted">Belum di-assign</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dalam Peninjauan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['under_review'] ?? 0 }}</div>
                            <div class="text-xs text-muted">Sedang ditinjau</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-eye fs-2 text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Selesai Ditinjau</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed'] ?? 0 }}</div>
                            <div class="text-xs text-muted">Sudah selesai ditinjau</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.submissions.index') }}">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    {{-- ✅ FIX: Only show allowed statuses --}}
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Revision Needed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="form-label">Jenis HKI</label>
                                <select name="type" id="type" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="copyright" {{ request('type') == 'copyright' ? 'selected' : '' }}>Copyright</option>
                                    <!-- <option value="patent" {{ request('type') == 'patent' ? 'selected' : '' }}>Patent</option> -->
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="creation_type" class="form-label">Jenis Ciptaan</label>
                                <select name="creation_type" id="creation_type" class="form-select">
                                    <option value="">Semua Ciptaan</option>
                                    <option value="program_komputer" {{ request('creation_type') == 'program_komputer' ? 'selected' : '' }}>Program Komputer</option>
                                    <option value="sinematografi" {{ request('creation_type') == 'sinematografi' ? 'selected' : '' }}>Sinematografi</option>
                                    <option value="buku" {{ request('creation_type') == 'buku' ? 'selected' : '' }}>Buku</option>
                                    <option value="poster" {{ request('creation_type') == 'poster' ? 'selected' : '' }}>Poster</option>
                                    <option value="fotografi" {{ request('creation_type') == 'fotografi' ? 'selected' : '' }}>Fotografi</option>
                                    <option value="seni_gambar" {{ request('creation_type') == 'seni_gambar' ? 'selected' : '' }}>Seni Gambar</option>
                                    <option value="karakter_animasi" {{ request('creation_type') == 'karakter_animasi' ? 'selected' : '' }}>Karakter Animasi</option>
                                    <option value="alat_peraga" {{ request('creation_type') == 'alat_peraga' ? 'selected' : '' }}>Alat Peraga</option>
                                    <option value="basis_data" {{ request('creation_type') == 'basis_data' ? 'selected' : '' }}>Basis Data</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="assignment" class="form-label">Assignment</label>
                                <select name="assignment" id="assignment" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="unassigned" {{ request('assignment') == 'unassigned' ? 'selected' : '' }}>Belum Di-assign</option>
                                    <option value="my_reviews" {{ request('assignment') == 'my_reviews' ? 'selected' : '' }}>Review Saya</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Cari</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Judul, pengusul, atau NIDN..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-list-ul me-2"></i>Daftar Pengajuan
                    </h6>
                    <div class="d-flex align-items-center">
                        <small class="text-muted me-3">
                            Menampilkan {{ $submissions->firstItem() }} - {{ $submissions->lastItem() }} 
                            dari {{ $submissions->total() }} pengajuan
                        </small>
                        <!--<div class="btn-group btn-group-sm" role="group">
                            <input type="checkbox" class="btn-check" id="selectAll" autocomplete="off">
                            <label class="btn btn-outline-primary" for="selectAll">
                                <i class="bi bi-check-square"></i> Pilih Semua
                            </label>
                        </div>-->
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="3%">
                                            <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                        </th>
                                        <th width="5%">ID</th>
                                        <th width="25%">Judul & Pengusul</th>
<<<<<<< Updated upstream
                                        <th width="12%">Jenis</th>
                                        <th width="12%">Status</th>
                                        <th width="12%">Tanggal</th>
=======
                                        <th width="15%">Jenis</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Tanggal</th>
                                        <!-- <th width="15%">Reviewer</th> -->
>>>>>>> Stashed changes
                                        <th width="16%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $index => $submission)
                                    <tr class="submission-row" data-id="{{ $submission->id }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input submission-checkbox" 
                                                   value="{{ $submission->id }}">
                                        </td>
                                        <td>
                                            <span class="font-monospace text-muted">#{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <h6 class="mb-1">{{ Str::limit($submission->title, 45) }}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-person me-1"></i>{{ $submission->user->nama }}
                                                    <br><i class="bi bi-card-text me-1"></i>{{ $submission->user->nidn }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="badge bg-info mb-1">{{ ucfirst($submission->type) }}</span>
                                                <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $submission->creation_type)) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusColor = App\Helpers\StatusHelper::getStatusColor($submission->status);
                                                $statusIcon = App\Helpers\StatusHelper::getStatusIcon($submission->status);
                                                $statusName = App\Helpers\StatusHelper::getStatusName($submission->status);
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusName }}
                                            </span>
                                            @if($submission->status === 'submitted')
                                                <br><small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Perlu action</small>
                                            @elseif($submission->status === 'revision_needed')
                                                <br><small class="text-warning"><i class="bi bi-clock"></i> Menunggu user</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                <strong>Submit:</strong><br>
                                                {{ $submission->submission_date ? $submission->submission_date->format('d M Y') : '-' }}
                                                @if($submission->reviewed_at)
                                                    <br><strong>Review:</strong><br>
                                                    {{ $submission->reviewed_at->format('d M Y') }}
                                                @endif
                                            </div>
                                        </td>
<<<<<<< Updated upstream
                                        <!--<td>
=======
                                       <!--<td>
>>>>>>> Stashed changes
                                            @if($submission->reviewer)
                                                <div class="small">
                                                    <strong>{{ $submission->reviewer->nama }}</strong>
                                                    @if($submission->reviewer_id === Auth::id())
                                                        <br><span class="badge bg-primary">Anda</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Belum di-assign</span>
                                            @endif
<<<<<<< Updated upstream
                                        </td>-->
=======
                                        </td> -->
>>>>>>> Stashed changes
                                        <td>
                                            <div class="btn-group-vertical" role="group">
                                                <a href="{{ route('admin.submissions.show', $submission) }}" 
                                                   class="btn btn-sm btn-outline-primary mb-1" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                                
                                                @if($submission->status === 'submitted')
                                                    <form action="{{ route('admin.submissions.assign-to-self', $submission) }}" 
                                                          method="POST" class="mb-1">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success w-100" 
                                                                title="Assign untuk Review">
                                                            <i class="bi bi-person-check"></i> Assign
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                @if($submission->status === 'under_review' && $submission->reviewer_id === Auth::id())
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                                onclick="showApproveModal({{ $submission->id }})" title="Approve">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                onclick="showRevisionModal({{ $submission->id }})" title="Revision">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="showRejectModal({{ $submission->id }})" title="Reject">
                                                            <i class="bi bi-x"></i>
                                                        </button>
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
                            <h5 class="mt-2 text-muted">Tidak ada submission ditemukan</h5>
                            <p class="text-muted">Coba ubah filter pencarian</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Actions Bar -->
<div id="bulkActionsBar" class="bulk-actions-bar" style="display: none;">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span id="selectedCount">0</span> item dipilih
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-success" onclick="bulkAssign()">
                    <i class="bi bi-person-check"></i> Assign ke Saya
                </button>
                <button type="button" class="btn btn-outline-info" onclick="bulkExport()">
                    <i class="bi bi-download"></i> Export
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                    <i class="bi bi-x"></i> Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="approve_notes" class="form-label">Catatan Approval <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="approve_notes" name="review_notes" rows="4" 
                                  placeholder="Berikan catatan untuk user..." required></textarea>
                        <div class="form-text">Catatan ini akan dikirim ke user sebagai konfirmasi approval</div>
                    </div>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Approval akan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Mengubah status menjadi "Approved"</li>
                            <li>Mengirim notifikasi ke user</li>
                            <li>Memungkinkan user download sertifikat</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Approve Submission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Revision Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Request Revision</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="revisionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="revision_notes" class="form-label">Catatan Revisi </label>
                        <textarea class="form-control" id="revision_notes" name="review_notes" rows="4" 
                                  placeholder="Jelaskan apa yang perlu diperbaiki..." ></textarea>
                        <div class="form-text">Berikan panduan yang jelas untuk user melakukan perbaikan</div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Request Revision akan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Mengubah status menjadi "Revision Needed"</li>
                            <li>Memungkinkan user edit submission</li>
                            <li>Mengirim notifikasi ke user</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-arrow-clockwise"></i> Request Revision
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject_notes" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_notes" name="review_notes" rows="4" 
                                  placeholder="Jelaskan alasan penolakan..." required></textarea>
                        <div class="form-text">Berikan alasan yang jelas mengapa submission ditolak</div>
                    </div>
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle me-2"></i>
                        <strong>Reject akan:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Mengubah status menjadi "Rejected"</li>
                            <li>Submission tidak bisa diedit lagi</li>
                            <li>Mengirim notifikasi ke user</li>
                            <li><strong>Tindakan ini tidak dapat dibatalkan</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Reject Submission
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Global variables
let selectedSubmissions = [];

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when select changes
    document.querySelectorAll('select[name="status"], select[name="type"], select[name="creation_type"], select[name="assignment"]').forEach(select => {
        select.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Checkbox handling
    const selectAllHeader = document.getElementById('selectAllHeader');
    const submissionCheckboxes = document.querySelectorAll('.submission-checkbox');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Select all functionality
    selectAllHeader.addEventListener('change', function() {
        submissionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedSubmissions();
    });

    // Individual checkbox handling
    submissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedSubmissions);
    });

    function updateSelectedSubmissions() {
        selectedSubmissions = Array.from(submissionCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        selectedCountSpan.textContent = selectedSubmissions.length;

        if (selectedSubmissions.length > 0) {
            bulkActionsBar.style.display = 'block';
        } else {
            bulkActionsBar.style.display = 'none';
        }

        // Update select all header state
        selectAllHeader.indeterminate = selectedSubmissions.length > 0 && selectedSubmissions.length < submissionCheckboxes.length;
        selectAllHeader.checked = selectedSubmissions.length === submissionCheckboxes.length;
    }
});

// Modal functions
function showApproveModal(submissionId) {
    document.getElementById('approveForm').action = `/admin/submissions/${submissionId}/approve`;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function showRevisionModal(submissionId) {
    document.getElementById('revisionForm').action = `/admin/submissions/${submissionId}/revision`;
    new bootstrap.Modal(document.getElementById('revisionModal')).show();
}

function showRejectModal(submissionId) {
    document.getElementById('rejectForm').action = `/admin/submissions/${submissionId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

// Bulk actions
function bulkAssign() {
    if (selectedSubmissions.length === 0) return;
    
    if (confirm(`Assign ${selectedSubmissions.length} submission ke diri Anda untuk review?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.submissions.bulk-assign") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        selectedSubmissions.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'submissions[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}

function bulkExport() {
    if (selectedSubmissions.length === 0) return;
    
    const url = new URL('{{ route("admin.submissions.export") }}');
    selectedSubmissions.forEach(id => {
        url.searchParams.append('ids[]', id);
    });
    
    window.open(url.toString(), '_blank');
}

function clearSelection() {
    document.querySelectorAll('.submission-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAllHeader').checked = false;
    selectedSubmissions = [];
    document.getElementById('bulkActionsBar').style.display = 'none';
}
</script>
@endpush

@push('styles')
<style>
.border-start {
    border-left-width: 4px !important;
}

.bulk-actions-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 2px solid #dee2e6;
    padding: 1rem 0;
    z-index: 1030;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

.submission-row:hover {
    background-color: #f8f9fa;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}

.badge {
    font-size: 0.75em;
}

.btn-group-vertical .btn {
    margin-bottom: 2px;
}

.btn-group-vertical .btn:last-child {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .btn-group-vertical {
        width: 100%;
    }
    
    .btn-group-vertical .btn {
        width: 100%;
    }
}
</style>
@endpush
@endsection