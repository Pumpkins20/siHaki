
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
                    <li class="breadcrumb-item"><a href="{{ route('user.submissions.index') }}">Submissions</a></li>
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
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Submission</h6>
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
                                    <td><strong>Jenis:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($submission->type) }}</span>
                                    </td>
                                </tr>
                                <tr>
<<<<<<< Updated upstream
=======
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
                                        <br><small class="text-muted">Pertama kali diumumkan/digunakan/dipublikasikan</small>
                                    </td>
                                </tr>
                                <tr>
>>>>>>> Stashed changes
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
                                                Perlu perbaikan sesuai catatan reviewer
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
                                <tr>
                                    <td><strong>Jumlah Anggota:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $submission->member_count }} orang</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Tanggal Submit:</strong></td>
                                    <td>
                                        {{-- ✅ TIMEZONE: Format dengan WIB --}}
                                        @if($submission->submission_date)
                                            <strong>{{ $submission->submission_date->setTimezone('Asia/Jakarta')->format('d M Y') }}</strong>
                                            <br><small class="text-muted">{{ $submission->submission_date->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</small>
                                        @else
                                            <span class="text-muted">Belum submit</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Review:</strong></td>
                                    <td>
                                        {{-- ✅ TIMEZONE: Format dengan WIB --}}
                                        @if($submission->reviewed_at)
                                            <strong>{{ $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('d M Y') }}</strong>
                                            <br><small class="text-muted">{{ $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</small>
                                        @else
                                            <span class="text-muted">Belum direview</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Pada:</strong></td>
                                    <td>
                                        {{-- ✅ TIMEZONE: Format dengan WIB --}}
                                        <small class="text-muted">
                                            {{ $submission->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                                        </small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-12">
                            <h6><strong>Deskripsi:</strong></h6>
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
                            <div class="alert alert-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                <i class="bi bi-{{ $submission->status === 'approved' ? 'check-circle' : ($submission->status === 'rejected' ? 'x-circle' : 'exclamation-triangle') }} me-2"></i>
                                {{ $submission->review_notes }}
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
                        <i class="bi bi-people me-2"></i>Anggota Pencipta ({{ $submission->members->count() }} orang)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Nama</th>
                                    <th width="20%">Email</th>
                                    <th width="15%">WhatsApp</th>
                                    <th width="10%">Posisi</th>
                                    <th width="10%">KTP</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submission->members as $index => $member)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $member->name }}</strong>
                                        @if($member->is_leader)
                                            <span class="badge bg-success ms-1">Ketua</span>
                                        @endif
                                    </td>
                                    <td>{{ $member->email }}</td>
                                    <td>
                                        <a href="https://wa.me/{{ $member->whatsapp }}" target="_blank" class="text-success">
                                            <i class="bi bi-whatsapp"></i> {{ $member->whatsapp }}
                                        </a>
                                    </td>
                                    <td>{{ $member->position }}</td>
                                    <td>
                                        @if($member->ktp)
                                            <a href="{{ Storage::url($member->ktp) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Lihat
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
                                        <th width="5%">#</th>
                                        <th width="30%">Nama File</th>
                                        <th width="20%">Jenis</th>
                                        <th width="15%">Ukuran</th>
                                        <th width="20%">Upload Date</th>
                                        <th width="10%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submission->documents as $index => $document)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <i class="bi bi-file-earmark-{{ $document->mime_type === 'application/pdf' ? 'pdf' : 'text' }} text-danger me-1"></i>
                                            {{ $document->file_name }}
                                        </td>
                                        <td>
                                            @if($document->document_type === 'main_document')
                                                <span class="badge bg-primary">Dokumen Utama</span>
                                            @elseif($document->document_type === 'certificate')
                                                <span class="badge bg-success">Sertifikat</span>
                                            @else
                                                <span class="badge bg-secondary">Dokumen Pendukung</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                        <td>
                                            {{-- ✅ TIMEZONE: Format dengan WIB --}}
                                            <strong>{{ $document->uploaded_at->setTimezone('Asia/Jakarta')->format('d M Y') }}</strong>
                                            <br><small class="text-muted">{{ $document->uploaded_at->setTimezone('Asia/Jakarta')->format('H:i') }} WIB</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.submissions.documents.download', $document) }}" 
                                               class="btn btn-sm btn-outline-success" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            @if(in_array($submission->status, ['draft', 'revision_needed']) && $document->document_type !== 'certificate')
                                                <form action="{{ route('user.submissions.documents.delete', $document) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
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
                        <i class="bi bi-clock-history me-2"></i>Riwayat Activity
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($submission->histories->sortByDesc('created_at') as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ $history->action }}</h6>
                                <p class="timeline-text text-muted">
                                    {{ $history->notes }}
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> {{ $history->user->nama }} • 
                                    {{-- ✅ TIMEZONE: Format dengan WIB --}}
                                    <i class="bi bi-calendar"></i> {{ $history->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
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
                                <strong>Draft/Submitted</strong>
                                <br><small>Submission dibuat</small>
                            </div>
                        </div>
                        
                        <div class="status-step {{ in_array($submission->status, ['under_review', 'revision_needed', 'approved', 'rejected']) ? 'active' : '' }}">
                            <div class="status-icon">
                                <i class="bi bi-eye"></i>
                            </div>
                            <div class="status-text">
                                <strong>Under Review</strong>
                                <br><small>Sedang direview</small>
                            </div>
                        </div>
                        
                        <div class="status-step {{ $submission->status === 'approved' ? 'active completed' : ($submission->status === 'rejected' ? 'active rejected' : ($submission->status === 'revision_needed' ? 'active revision' : '')) }}">
                            <div class="status-icon">
                                <i class="bi bi-{{ \App\Helpers\StatusHelper::getStatusIcon($submission->status) }}"></i>
                            </div>
                            <div class="status-text">
                                <strong>{{ \App\Helpers\StatusHelper::getStatusName($submission->status) }}</strong>
                                @if($submission->reviewed_at)
                                    {{-- ✅ TIMEZONE: Format dengan WIB --}}
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
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.submissions.edit', $submission) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit Submission
                        </a>
                        @if($submission->status === 'draft')
                            <form action="{{ route('user.submissions.destroy', $submission) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus submission ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-trash"></i> Hapus Submission
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Certificate Download Card --}}
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
</style>
@endpush
@endsection