@extends('layouts.user')

@section('title', 'Detail Submission')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.submissions.index') }}">Pengajuan HKI</a></li>
                    <li class="breadcrumb-item active">Detail</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">{{ $submission->title }}</h1>
                    <p class="text-muted">ID: #{{ str_pad($submission->id, 6, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    @if(in_array($submission->status, ['draft', 'revision_needed']))
                        <a href="{{ route('user.submissions.edit', $submission) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    @endif
                    <a href="{{ route('user.submissions.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Submission Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengajuan</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Judul:</strong></td>
                                    <td>{{ $submission->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis HKI:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($submission->type) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Ciptaan:</strong></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $submission->creation_type)) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Publikasi:</strong></td>
                                    <td>
                                        <i class="bi bi-calendar-event text-primary me-1"></i>
                                        {{-- ✅ TIMEZONE: Format dengan WIB --}}
                                        {{ $submission->first_publication_date ? $submission->first_publication_date->setTimezone('Asia/Jakarta')->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @php
                                            // ✅ UNIFIED: Use StatusHelper for consistent colors
                                            $statusColor = \App\Helpers\StatusHelper::getStatusColor($submission->status);
                                            $statusIcon = \App\Helpers\StatusHelper::getStatusIcon($submission->status);
                                            $statusName = \App\Helpers\StatusHelper::getStatusName($submission->status);
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }} fs-6 px-3 py-2">
                                            <i class="bi bi-{{ $statusIcon }} me-2"></i>{{ $statusName }}
                                        </span>

                                        @if($submission->status === 'revision_needed')
                                            <br><small class="text-warning mt-1">
                                                <i class="bi bi-exclamation-triangle"></i>
                                                Perlu perbaikan sesuai catatan
                                            </small>
                                        @elseif($submission->status === 'approved')
                                            <br><small class="text-success mt-1">
                                                <i class="bi bi-check-circle"></i>
                                                Selamat! Pengajuan Anda telah disetujui
                                            </small>
                                        @elseif($submission->status === 'rejected')
                                            <br><small class="text-danger mt-1">
                                                <i class="bi bi-x-circle"></i>
                                                Pengajuan tidak dapat diproses lebih lanjut
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Alamat Lengkap:</strong></td>
                                    <td>
                                        @php
                                            // ✅ FIXED: Get address from first member (leader)
                                            $leader = $submission->members->where('is_leader', true)->first() ?? $submission->members->sortBy('position')->first();
                                        @endphp
                                        @if($leader && $leader->alamat)
                                            {{ $leader->alamat }}
                                            @if($leader->kode_pos)
                                                {{ $leader->kode_pos }}
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Kode Pos:</strong></td>
                                    <td>
                                        @if($leader && $leader->kode_pos)
                                            {{ $leader->kode_pos }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Submit:</strong></td>
                                    <td>
                                        @if($submission->submission_date)
                                            <strong>{{ $submission->submission_date->setTimezone('Asia/Jakarta')->format('d M Y') }}</strong>
                                            <br><small class="text-muted">{{ $submission->submission_date->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</small>
                                        @else
                                            <span class="text-muted">Belum submit</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status Peninjauan:</strong></td>
                                    <td>
                                        @if($submission->reviewed_at)
                                            <strong>{{ $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('d M Y') }}</strong>
                                            <br><small class="text-muted">{{ $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</small>
                                        @else
                                            <span class="text-muted">Belum direview</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-12">
                            <h6><strong>Deskripsi Ciptaan:</strong></h6>
                            <p class="text-justify">{{ $submission->description }}</p>
                        </div>
                    </div>

                    {{-- Additional Data based on creation type --}}
                    @if($submission->additional_data)
                        @php
                            $additionalData = $submission->additional_data;
                        @endphp
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6><strong>Informasi Tambahan:</strong></h6>
                                <div class="row">
                                    @if($submission->creation_type === 'program_komputer')
                                        @if(isset($additionalData['program_link']))
                                            <div class="col-md-6">
                                                <p><strong>Link Program:</strong>
                                                    <a href="{{ $additionalData['program_link'] }}" target="_blank" class="text-primary">
                                                        {{ $additionalData['program_link'] }}
                                                    </a>
                                                </p>
                                            </div>
                                        @endif
                                    @elseif($submission->creation_type === 'sinematografi')
                                        @if(isset($additionalData['video_link']))
                                            <div class="col-md-6">
                                                <p><strong>Link Video:</strong>
                                                    <a href="{{ $additionalData['video_link'] }}" target="_blank" class="text-primary">
                                                        {{ $additionalData['video_link'] }}
                                                    </a>
                                                </p>
                                            </div>
                                        @endif
                                    @elseif($submission->creation_type === 'buku')
                                        @if(isset($additionalData['isbn']))
                                            <div class="col-md-6">
                                                <p><strong>ISBN:</strong> {{ $additionalData['isbn'] }}</p>
                                            </div>
                                        @endif
                                        @if(isset($additionalData['page_count']))
                                            <div class="col-md-6">
                                                <p><strong>Jumlah Halaman:</strong> {{ $additionalData['page_count'] }}</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($submission->review_notes)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6><strong>Catatan Reviewer:</strong></h6>
                                <div class="p-3 border rounded 
                                    {{ $submission->status === 'approved' ? 'bg-success-subtle border-success text-success-emphasis' :
                                       ($submission->status === 'rejected' ? 'bg-danger-subtle border-danger text-danger-emphasis' :
                                        'bg-warning-subtle border-warning text-warning-emphasis') }}">
                                    <i class="bi bi-{{ $submission->status === 'approved' ? 'check-circle' :
                                                     ($submission->status === 'rejected' ? 'x-circle' : 'exclamation-triangle') }} me-2"></i>
                                    <strong>{{ $submission->review_notes }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Members -->
            @if($submission->members->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-people me-2"></i>Anggota Pencipta & Status KTP
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Nama</th>
                                        <th width="10%">Role</th>
                                        <th width="20%">Email</th>
                                        <th width="15%">WhatsApp</th>
                                        <th width="20%">Alamat</th>
                                        <th width="10%">Status KTP</th>
                                        <th width="5%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submission->members->sortBy(function($member) { return $member->is_leader ? 0 : $member->position; }) as $member)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $member->name }}</strong>
                                                @if($member->is_leader)
                                                    <br><span class="badge bg-success badge-sm">Ketua Tim</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $member->is_leader ? 'Ketua' : 'Anggota' }}
                                            </td>
                                            <td>
                                                <small>{{ $member->email }}</small>
                                            </td>
                                            <td>
                                                <a href="{{ $member->whatsapp_url }}" target="_blank" class="text-success">
                                                    <i class="bi bi-whatsapp me-1"></i>
                                                    {{ $member->whatsapp }}
                                                </a>
                                            </td>
                                            <td>
                                                {{-- ✅ NEW: Display member address --}}
                                                @if($member->alamat)
                                                    <small class="text-muted">
                                                        {{ Str::limit($member->alamat, 30) }}
                                                        @if($member->kode_pos)
                                                            <br><strong>{{ $member->kode_pos }}</strong>
                                                        @endif
                                                    </small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($member->ktp)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Sudah Upload
                                                    </span>
                                                    <br><small class="text-muted">{{ $member->updated_at->format('d M Y') }}</small>
                                                @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle me-1"></i>Belum Upload
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($member->ktp)
                                                    <a href="{{ route('user.submissions.member-ktp-preview', [$submission, $member]) }}" 
                                                       target="_blank" class="btn btn-sm btn-outline-info" title="Lihat KTP">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- KTP Edit Info Card --}}
                        @if(in_array($submission->status, ['submitted', 'under_review', 'revision_needed', 'approved']))
                            <div class="alert alert-info mt-3">
                                <h6 class="alert-heading">
                                    <i class="bi bi-info-circle me-2"></i>Edit KTP Setelah Submit
                                </h6>
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-2">
                                            Anda masih dapat mengedit foto KTP anggota bahkan setelah submission di-submit. 
                                            Perubahan KTP tidak memerlukan persetujuan ulang dari reviewer.
                                        </p>
                                        <ul class="mb-0 small">
                                            <li>KTP dapat diperbarui untuk memperbaiki kualitas foto</li>
                                            <li>File KTP baru akan langsung mengganti yang lama</li>
                                            <li>Tidak mempengaruhi status submission yang sudah approved</li>
                                            <li>Admin akan otomatis mendapat notifikasi perubahan KTP</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4 text-center">
                                        <button type="button" class="btn btn-warning btn-sm" 
                                                onclick="showKtpEditModal()" title="Edit KTP Anggota">
                                            <i class="bi bi-pencil me-1"></i>Edit KTP
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @elseif(in_array($submission->status, ['draft']))
                            <div class="alert alert-secondary mt-3">
                                <h6 class="alert-heading">
                                    <i class="bi bi-pencil me-2"></i>Edit KTP melalui Form Edit
                                </h6>
                                <p class="mb-0">
                                    Untuk submission dengan status draft, gunakan 
                                    <a href="{{ route('user.submissions.edit', $submission) }}" class="alert-link">form edit submission</a> 
                                    untuk mengubah data anggota dan KTP.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Documents -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>Dokumen
                    </h6>
                </div>
                <div class="card-body">
                    @if($submission->documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="30%">Nama File</th>
                                        <th width="20%">Jenis</th>
                                        <th width="15%">Ukuran</th>
                                        <th width="20%">Tanggal Upload</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $documentIndex = 1; @endphp
                                    @foreach($submission->documents as $document)
                                        @if(in_array($document->document_type, ['main_document', 'supporting_document']))
                                            <tr>
                                                <td>{{ $documentIndex++ }}</td>
                                                <td>
                                                    <i class="bi bi-file-earmark-{{ $document->mime_type === 'application/pdf' ? 'pdf' : 'text' }} text-danger me-1"></i>
                                                    {{ $document->file_name }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">Dokumen Utama</span>
                                                </td>
                                                <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                                <td>
                                                    <strong>{{ $document->uploaded_at->setTimezone('Asia/Jakarta')->format('d M Y') }}</strong>
                                                    <br><small class="text-muted">{{ $document->uploaded_at->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</small>
                                                </td>
                                                <td>
                                                    <a href="{{ route('user.submissions.documents.download', $document) }}" 
                                                       class="btn btn-sm btn-success" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">Belum ada dokumen yang diupload</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- History -->
            @if($submission->histories->count() > 0)
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($submission->histories->sortByDesc('created_at') as $history)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">{{ $history->action }}</h6>
                                        <p class="timeline-text text-muted">{{ $history->notes }}</p>
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> {{ $history->user->nama }} •
                                            <i class="bi bi-calendar"></i>
                                            {{ $history->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Tracking</h6>
                </div>
                <div class="card-body">
                    <div class="status-progress">
                        <div class="status-step {{ in_array($submission->status, ['draft', 'submitted', 'under_review', 'revision_needed', 'approved', 'rejected']) ? 'active' : '' }}">
                            <div class="status-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="status-text">
                                <strong>Pengajuan</strong>
                                <br><small>Pengajuan dibuat</small>
                            </div>
                        </div>

                        <div class="status-step {{ in_array($submission->status, ['under_review', 'revision_needed', 'approved', 'rejected']) ? 'active' : '' }}">
                            <div class="status-icon">
                                <i class="bi bi-eye"></i>
                            </div>
                            <div class="status-text">
                                <strong>Dalam Peninjauan</strong>
                                <br><small>Sedang ditinjau</small>
                            </div>
                        </div>

                        <div class="status-step {{ $submission->status === 'approved' ? 'active completed' : ($submission->status === 'rejected' ? 'active rejected' : ($submission->status === 'revision_needed' ? 'active revision' : '')) }}">
                            <div class="status-icon">
                                <i class="bi bi-{{ \App\Helpers\StatusHelper::getStatusIcon($submission->status) }}"></i>
                            </div>
                            <div class="status-text">
                                <strong>{{ \App\Helpers\StatusHelper::getStatusName($submission->status) }}</strong>
                                @if($submission->reviewed_at)
                                    <br><small>{{ $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @if(in_array($submission->status, ['draft', 'revision_needed']))
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('user.submissions.edit', $submission) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit Pengajuan
                            </a>
                            @if($submission->status === 'draft')
                                <form action="{{ route('user.submissions.destroy', $submission) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus submission ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i> Hapus Pengajuan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Certificate Download Card -->
            @if($submission->status === 'approved')
                @php
                    $certificate = $submission->documents()->where('document_type', 'certificate')->first();
                @endphp
                @if($certificate)
                    <div class="card shadow mb-4 border-success">
                        <div class="card-header py-3 bg-success text-white">
                            <h6 class="m-0 font-weight-bold">
                                <i class="bi bi-award me-2"></i>Sertifikat HKI
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <i class="bi bi-patch-check-fill text-success fs-1 mb-3"></i>
                                <h6 class="text-success">Selamat!</h6>
                                <p class="small text-muted mb-3">
                                    Sertifikat HKI Anda sudah tersedia dan dapat diunduh.
                                </p>
                                <a href="{{ route('user.submissions.documents.download', $certificate) }}" 
                                   class="btn btn-success">
                                    <i class="bi bi-download me-1"></i>Download Sertifikat
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Support -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Butuh Bantuan?</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p class="mb-2">
                            <i class="bi bi-envelope"></i>
                            Email: hki@amikom.ac.id
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-telephone"></i>
                            Telp: (0271) 7851507
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-clock"></i>
                            {{-- ✅ TIMEZONE: Jam WIB --}}
                            Senin-Jumat: 08:00-16:00 WIB
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KTP Edit Modal -->
<div class="modal fade" id="ktpEditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-credit-card-2-front me-2"></i>Edit KTP Anggota
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('user.submissions.update-ktp', $submission) }}" 
                  method="POST" enctype="multipart/form-data" id="ktpEditForm">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Catatan:</strong> Update KTP tidak mempengaruhi status submission. 
                        File baru akan langsung mengganti KTP yang lama.
                    </div>
                    
                    <div class="row g-3">
                        @foreach($submission->members as $index => $member)
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header py-2">
                                        <h6 class="mb-0">
                                            <i class="bi bi-person-circle text-success me-2"></i>
                                            {{ $member->name }}
                                            @if($member->is_leader)
                                                <span class="badge bg-success ms-2">Ketua</span>
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                @if($member->ktp)
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bi bi-check-circle text-success me-2"></i>
                                                        <small class="text-success">
                                                            <strong>KTP sudah diupload</strong><br>
                                                            Upload: {{ $member->updated_at->format('d M Y H:i') }} WIB
                                                        </small>
                                                        <a href="{{ route('user.submissions.member-ktp-preview', [$submission, $member]) }}" 
                                                           target="_blank" class="btn btn-sm btn-outline-info ms-auto" 
                                                           title="Lihat KTP">
                                                            <i class="bi bi-eye"></i> Lihat
                                                        </a>
                                                    </div>
                                                @else
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bi bi-x-circle text-danger me-2"></i>
                                                        <small class="text-danger"><strong>KTP belum diupload</strong></small>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small">
                                                    {{ $member->ktp ? 'Upload KTP Baru:' : 'Upload KTP:' }}
                                                </label>
                                                <input type="file" 
                                                       class="form-control form-control-sm" 
                                                       name="ktp_files[{{ $member->id }}]" 
                                                       accept=".jpg,.jpeg" 
                                                       onchange="validateKtpFileInModal(this, {{ $member->id }})">
                                                <input type="hidden" name="member_ids[]" value="{{ $member->id }}">
                                                <div class="form-text">
                                                    JPG/JPEG, maksimal 2MB
                                                    @if($member->ktp)
                                                        <br><small class="text-warning">
                                                            <i class="bi bi-exclamation-triangle"></i> 
                                                            File baru akan mengganti KTP yang ada
                                                        </small>
                                                    @endif
                                                </div>
                                                
                                                <div class="ktp-preview-modal mt-2" 
                                                     id="ktpPreviewModal_{{ $member->id }}" 
                                                     style="display: none;">
                                                    <div class="alert alert-success py-2">
                                                        <small>
                                                            <i class="bi bi-image me-1"></i>
                                                            <span id="ktpFileNameModal_{{ $member->id }}"></span>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger ms-2" 
                                                                    onclick="clearKtpFileInModal({{ $member->id }})" 
                                                                    title="Hapus file">
                                                                <i class="bi bi-x"></i>
                                                            </button>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-warning" id="ktpEditSubmitBtn">
                        <i class="bi bi-upload"></i> Update KTP
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Show KTP Edit Modal
function showKtpEditModal() {
    const modal = new bootstrap.Modal(document.getElementById('ktpEditModal'));
    modal.show();
}

// Validate KTP file in modal
function validateKtpFileInModal(input, memberId) {
    const file = input.files[0];
    
    if (!file) {
        clearKtpFileInModal(memberId);
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
        alert('Format file KTP harus JPG atau JPEG.');
        input.value = '';
        clearKtpFileInModal(memberId);
        return;
    }
    
    // Validate file size (max 2MB)
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
        alert('Ukuran file KTP maksimal 2MB.');
        input.value = '';
        clearKtpFileInModal(memberId);
        return;
    }
    
    // Show preview
    showKtpPreviewInModal(memberId, file.name);
}

// Show KTP preview in modal
function showKtpPreviewInModal(memberId, fileName) {
    const preview = document.getElementById(`ktpPreviewModal_${memberId}`);
    const fileNameSpan = document.getElementById(`ktpFileNameModal_${memberId}`);
    
    if (preview && fileNameSpan) {
        fileNameSpan.textContent = fileName;
        preview.style.display = 'block';
    }
}

// Clear KTP file in modal
function clearKtpFileInModal(memberId) {
    const fileInput = document.querySelector(`input[name="ktp_files[${memberId}]"]`);
    const preview = document.getElementById(`ktpPreviewModal_${memberId}`);
    
    if (fileInput) {
        fileInput.value = '';
    }
    
    if (preview) {
        preview.style.display = 'none';
    }
}

// Form submission handling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('ktpEditForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('ktpEditSubmitBtn');
            
            // Check if any files are selected
            const fileInputs = form.querySelectorAll('input[type="file"]');
            let hasFiles = false;
            
            fileInputs.forEach(input => {
                if (input.files.length > 0) {
                    hasFiles = true;
                }
            });
            
            if (!hasFiles) {
                e.preventDefault();
                alert('Pilih setidaknya satu file KTP untuk diupdate.');
                return false;
            }
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';
            
            // Re-enable after timeout
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-upload"></i> Update KTP';
            }, 10000);
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline-content {
    padding-left: 15px;
}

.timeline-title {
    margin-bottom: 5px;
    font-size: 14px;
}

.timeline-text {
    margin-bottom: 5px;
    font-size: 13px;
}

.status-progress {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.status-step {
    display: flex;
    align-items: center;
    opacity: 0.5;
    transition: opacity 0.3s;
}

.status-step.active {
    opacity: 1;
}

.status-step.completed .status-icon {
    background-color: #28a745;
    color: white;
}

.status-step.rejected .status-icon {
    background-color: #dc3545;
    color: white;
}

.status-step.revision .status-icon {
    background-color: #ffc107;
    color: white;
}

.status-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 18px;
}

.status-text {
    flex: 1;
}

/* KTP Modal Styles */
.ktp-preview-modal .alert {
    padding: 0.5rem;
    margin-bottom: 0;
}

.modal-lg {
    max-width: 800px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .timeline {
        padding-left: 20px;
    }
    
    .timeline::before {
        left: 10px;
    }
    
    .timeline-marker {
        left: -17px;
    }
    
    .status-progress {
        gap: 15px;
    }
    
    .status-icon {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }
}
</style>
@endpush