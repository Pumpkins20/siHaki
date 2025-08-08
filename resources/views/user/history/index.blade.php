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
                                        <th width="5%">No</th>
<<<<<<< Updated upstream
                                        <th width="25%">Judul & Jenis</th>
                                        <th width="15%">Status</th>
                                        <th width="15%">Tanggal</th>
                                        <th width="15%">Reviewer</th>
                                        <th width="10%">Anggota</th>
=======
                                        <th width="30%">Judul</th>
                                        <th width="12%">Jumlah Anggota</th>
                                        <th width="12%">Jenis Ciptaan</th>
                                        <th width="12%">Status</th>
                                        <th width="12%">Tanggal</th>
                                        <!-- <th width="12%">Reviewer</th> -->
                                       
>>>>>>> Stashed changes
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
                                                <strong>{{ Str::limit($submission->title, 50) }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($submission->description, 80) }}</small>
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
                                        <!-- <td>
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
                                        </td> -->
                                        
                                        <td>
                                            <div class="btn-group-vertical" role="group">
                                                <a href="{{ route('user.submissions.show', $submission) }}" 
                                                   class="btn btn-sm btn-outline-primary mb-1" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i> Detail
                                                </a>
                                                
                                                @if($submission->status === 'approved')
                                                    @php
                                                        $certificate = $submission->documents()->where('document_type', 'certificate')->first();
                                                        $hasCertificate = $certificate !== null;
                                                    @endphp
                                                    
                                                    @if($hasCertificate)
                                                        <a href="{{ route('user.submissions.documents.download', $certificate) }}" 
                                                           class="btn btn-sm btn-success mb-1" 
                                                           title="Download Sertifikat">
                                                            <i class="bi bi-download"></i> Sertifikat
                                                        </a>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary mb-1" 
                                                                title="Sertifikat sedang diproses"
                                                                disabled>
                                                            <i class="bi bi-hourglass-split"></i> Proses
                                                        </button>
                                                    @endif
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
@endsection