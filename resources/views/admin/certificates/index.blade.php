
@extends('layouts.admin')

@section('title', 'Kirim Sertifikat')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Kirim Sertifikat</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Kelola Sertifikat HKI</h1>
                    <p class="text-muted mb-0">Kirim sertifikat untuk submission yang sudah approved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_approved'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Sertifikat Terkirim
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['certificates_sent'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-award fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Sertifikat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['certificates_pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fs-2 text-gray-300"></i>
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
                    <form method="GET" action="{{ route('admin.certificates.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="certificate_status" class="form-label">Status Sertifikat</label>
                                <select name="certificate_status" id="certificate_status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('certificate_status') == 'pending' ? 'selected' : '' }}>Belum Dikirim</option>
                                    <option value="sent" {{ request('certificate_status') == 'sent' ? 'selected' : '' }}>Sudah Dikirim</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="search" class="form-label">Cari</label>
                                <input type="text" name="search" id="search" class="form-control" 
                                       placeholder="Cari berdasarkan judul atau nama user..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search"></i> Cari
                                </button>
                                <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Reset
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Submission Approved ({{ $submissions->total() }} items)</h6>
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
                                        <th width="12%">Tanggal Approved</th>
                                        <th width="10%">Status Sertifikat</th>
                                        <th width="23%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submissions as $index => $submission)
                                    <tr>
                                        <td>{{ $submissions->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ Str::limit($submission->title, 50) }}</strong>
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
                                        <td>{{ $submission->reviewed_at->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $hasCertificate = $submission->documents()->where('document_type', 'certificate')->exists();
                                            @endphp
                                            @if($hasCertificate)
                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Terkirim</span>
                                            @else
                                                <span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.certificates.show', $submission) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                @if(!$hasCertificate)
                                                    <a href="{{ route('admin.certificates.show', $submission) }}" 
                                                       class="btn btn-sm btn-success" title="Kirim Sertifikat">
                                                        <i class="bi bi-send"></i> Kirim Sertifikat
                                                    </a>
                                                @else
                                                    @php
                                                        $certificate = $submission->documents()->where('document_type', 'certificate')->first();
                                                    @endphp
                                                    <a href="{{ route('admin.certificates.document-download', [$submission, $certificate]) }}" 
                                                       class="btn btn-sm btn-outline-success" title="Unduh Sertifikat">
                                                        <i class="bi bi-download"></i> Unduh
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
                            {{ $submissions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <h5 class="mt-2 text-muted">Tidak ada submission approved ditemukan</h5>
                            <p class="text-muted">Coba ubah filter pencarian atau tunggu submission yang sudah diapprove</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection