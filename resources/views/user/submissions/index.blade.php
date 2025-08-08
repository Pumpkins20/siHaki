@extends('layouts.user')

@section('title', 'My Submissions')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">My Submissions</h1>
                    <p class="text-muted">Kelola semua submission HKI Anda</p>
                </div>
                <a href="{{ route('user.submissions.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i>Pengajuan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.submissions.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    {{-- ✅ UPDATED: Hanya tampilkan status aktif --}}
                                    <!-- <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option> -->
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Revision Needed</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            {{-- ✅ UPDATE: Replace type filter with creation_type filter --}}
                            <div class="col-md-3">
                                <label for="creation_type" class="form-label">Jenis Ciptaan</label>
                                <select name="creation_type" id="creation_type" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="program_komputer" {{ request('creation_type') == 'program_komputer' ? 'selected' : '' }}>Program Komputer</option>
                                    <option value="sinematografi" {{ request('creation_type') == 'sinematografi' ? 'selected' : '' }}>Sinematografi</option>
                                    <option value="buku" {{ request('creation_type') == 'buku' ? 'selected' : '' }}>Buku</option>
<<<<<<< Updated upstream
                                    <option value="poster_fotografi" {{ request('creation_type') == 'poster_fotografi' ? 'selected' : '' }}>Poster/Fotografi</option>
=======
                                    <option value="poster" {{ request('creation_type') == 'poster' ? 'selected' : '' }}>Poster</option>
                                    <option value="fotografi" {{ request('creation_type') == 'fotografi' ? 'selected' : '' }}>Fotografi</option>
                                    <option value="seni_gambar" {{ request('creation_type') == 'seni_gambar' ? 'selected' : '' }}>Seni Gambar</option>
                                    <!-- <option value="karakter_animasi" {{ request('creation_type') == 'karakter_animasi' ? 'selected' : '' }}>Karakter Animasi</option> -->
>>>>>>> Stashed changes
                                    <option value="alat_peraga" {{ request('creation_type') == 'alat_peraga' ? 'selected' : '' }}>Alat Peraga</option>
                                    <option value="basis_data" {{ request('creation_type') == 'basis_data' ? 'selected' : '' }}>Basis Data</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="search" class="form-label">Cari</label>
                                <input type="text" name="search" id="search" class="form-control" placeholder="Cari berdasarkan judul..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Cari
                                </button>
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
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Daftar Submissions
                        @if($submissions->total() > 0)
                            <small class="text-muted">({{ $submissions->total() }} total)</small>
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
<<<<<<< Updated upstream
                                        <th width="5%">#</th>
                                        <th width="25%">Judul & Deskripsi</th>
=======
                                        <th width="5%">No</th>
                                        <th width="20%">Judul</th>
                                        <th width="10%">Jumlah Anggota</th>
>>>>>>> Stashed changes
                                        <th width="15%">Jenis Ciptaan</th>
                                        <th width="12%">Status</th>
                                        <th width="15%">Tanggal Submit</th>
                                        {{-- ✅ NEW: Replace Reviewer column with Publication Date --}}
                                        <th width="15%">Tanggal Publikasi</th>
                                        <th width="13%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $index => $submission)
                                    @php
                                        // ✅ UNIFIED: Use StatusHelper for consistent colors
                                        $statusColor = \App\Helpers\StatusHelper::getStatusColor($submission->status);
                                        $statusIcon = \App\Helpers\StatusHelper::getStatusIcon($submission->status);
                                        $statusName = \App\Helpers\StatusHelper::getStatusName($submission->status);
                                    @endphp
                                    <tr>
                                        <td>{{ $submissions->firstItem() + $index }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ Str::limit($submission->title, 40) }}</strong>
                                                <br>
                                                <!-- <small class="text-muted">{{ Str::limit($submission->description, 60) }}</small>
                                                @if($submission->member_count > 0)
                                                    <br><span class="badge bg-info badge-sm">{{ $submission->member_count }} anggota</span>
                                                @endif -->
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
                                            <br>
                                            <small class="text-muted">{{ ucfirst($submission->type) }}</small>
                                        </td>
                                        <td>
                                            {{-- ✅ UNIFIED: Status with consistent colors and icons --}}
                                            <span class="badge bg-{{ $statusColor }}">
                                                <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusName }}
                                            </span>
                                            {{-- Additional status info --}}
                                            @if($submission->status === 'revision_needed')
                                                <br><small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Perlu revisi</small>
                                            @elseif($submission->status === 'approved')
                                                <br><small class="text-success"></small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                @if($submission->submission_date)
                                                    <strong>{{ $submission->submission_date->format('d M Y') }}</strong>
                                                    <br><small class="text-muted">{{ $submission->submission_date->format('H:i') }}</small>
                                                @else
                                                    <span class="text-muted">Belum submit</span>
                                                @endif
                                            </div>
                                        </td>
                                        {{-- ✅ NEW: Publication Date column --}}
                                        <td>
                                            <div class="small">
                                                @if($submission->first_publication_date)
                                                    <i class="bi bi-calendar-event text-primary me-1"></i>
                                                    <strong>{{ $submission->first_publication_date->format('d M Y') }}</strong>
                                                    <br><small class="text-muted">Pertama dipublikasi</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group-vertical" role="group">
                                                <a href="{{ route('user.submissions.show', $submission) }}" 
                                                   class="btn btn-sm btn-outline-primary mb-1" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                                
                                                @if(in_array($submission->status, ['draft', 'revision_needed']))
                                                    <a href="{{ route('user.submissions.edit', $submission) }}" 
                                                       class="btn btn-sm btn-outline-warning mb-1" title="Edit">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                @endif
                                                
                                                @if($submission->status === 'draft')
                                                    <form action="{{ route('user.submissions.destroy', $submission) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Yakin ingin menghapus submission ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- ✅ NEW: Quick action for approved submissions --}}
                                                @if($submission->status === 'approved')
                                                    <a href="{{ route('user.history.certificate', $submission) }}" 
                                                       class="btn btn-sm btn-outline-success" title="Download Sertifikat">
                                                        <i class="bi bi-download"></i> Sertifikat
                                                    </a>
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
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada submission</h5>
                            <p class="text-muted mb-4">Mulai ajukan HKI pertama Anda sekarang</p>
                            <a href="{{ route('user.submissions.create') }}" class="btn btn-success btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>Buat Submission Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* ✅ UNIFIED: Consistent styling */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

.badge-sm {
    font-size: 0.7em;
    padding: 0.2em 0.4em;
}

.btn-group-vertical .btn {
    border-radius: 0.25rem !important;
    margin-bottom: 2px;
}

.btn-group-vertical .btn:last-child {
    margin-bottom: 0;
}

/* Status badge improvements */
.badge {
    font-weight: 500;
}

.badge i {
    font-size: 0.8em;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group-vertical .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endpush
@endsection