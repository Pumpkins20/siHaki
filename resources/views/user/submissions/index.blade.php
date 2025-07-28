@extends('layouts.user')

@section('title', 'Pengajuan HKI')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Pengajuan HKI</h1>
                    <p class="text-muted">Kelola pengajuan HKI yang sedang dalam proses</p>
                </div>
                <a href="{{ route('user.submissions.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Pengajuan Baru
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" action="{{ route('user.submissions.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    {{-- ✅ UPDATED: Hanya tampilkan status aktif --}}
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                    <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Revision Needed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="creation_type" class="form-label">Jenis Ciptaan</label>
                                <select name="creation_type" id="creation_type" class="form-select">
                                    <option value="">Semua Jenis</option>
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
                            <div class="col-md-4">
                                <label for="search" class="form-label">Pencarian</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Cari judul..." value="{{ request('search') }}">
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Pengajuan Aktif ({{ $submissions->total() }} pengajuan)
                    </h6>
                </div>
                <div class="card-body">
                    @if($submissions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="35%">Judul & Deskripsi</th>
                                        <th width="15%">Jenis Ciptaan</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Tanggal Submit</th>
                                        <th width="15%">Aksi</th>
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
                                                <strong>{{ Str::limit($submission->title, 40) }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($submission->description, 60) }}</small>
                                                @if($submission->member_count > 0)
                                                    <br><span class="badge bg-info badge-sm">{{ $submission->member_count }} anggota</span>
                                                @endif
                                            </div>
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
                                            @if($submission->status === 'revision_needed')
                                                <br><small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Perlu revisi</small>
                                            @elseif($submission->status === 'draft')
                                                <br><small class="text-muted"><i class="bi bi-clock"></i> Belum submit</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small">
                                                @if($submission->submission_date)
                                                    <strong>{{ $submission->submission_date->format('d M Y') }}</strong>
                                                    <br><small class="text-muted">{{ $submission->submission_date->format('H:i') }} WIB</small>
                                                @else
                                                    <span class="text-muted">Belum submit</span>
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
                        <div class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada pengajuan aktif</h5>
                            <p class="text-muted mb-4">Mulai ajukan HKI pertama Anda sekarang</p>
                            <a href="{{ route('user.submissions.create') }}" class="btn btn-success btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>Buat Pengajuan Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection