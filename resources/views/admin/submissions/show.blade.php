@extends('layouts.admin')

@section('title', 'Detail Submission')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.submissions.index') }}" class="text-decoration-none">Review Pengajuan</a></li>
                    <li class="breadcrumb-item active">Detail #{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">{{ $submission->title }}</h1>
                    <p class="text-muted mb-0">
                        ID: #{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }} • 
                        Submit: {{ $submission->submission_date ? $submission->submission_date->format('d M Y H:i') : '-' }}
                    </p>
                </div>
                <div>
                    @if($submission->status === 'submitted')
                        <form action="{{ route('admin.submissions.assign-to-self', $submission) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success me-2">
                                <i class="bi bi-person-check"></i> Assign untuk Review
                            </button>
                        </form>
                    @endif
                    
                    @if($submission->status === 'under_review' && $submission->reviewer_id === Auth::id())
                        <div class="btn-group me-2" role="group">
                            <button type="button" class="btn btn-success" onclick="showApproveModal()">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                            <button type="button" class="btn btn-warning" onclick="showRevisionModal()">
                                <i class="bi bi-arrow-clockwise"></i> Revision
                            </button>
                            <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </div>
                    @endif
                    
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-xl-8 col-lg-7">
            <!-- Submission Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-info-circle me-2"></i>Informasi Pengajuan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%"><strong>Judul:</strong></td>
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
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @php
                                            $statusColor = \App\Helpers\StatusHelper::getStatusColor($submission->status);
                                            $statusIcon = \App\Helpers\StatusHelper::getStatusIcon($submission->status);
                                            $statusName = \App\Helpers\StatusHelper::getStatusName($submission->status);
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }} fs-6 px-3 py-2">
                                            <i class="bi bi-{{ $statusIcon }} me-2"></i>{{ $statusName }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Publikasi Pertama:</strong></td>
                                    <td>
                                        <i class="bi bi-calendar-event text-primary me-1"></i>
                                        {{ $submission->first_publication_date ? $submission->first_publication_date->format('d M Y') : '-' }}
                                        <br><small class="text-muted">Pertama kali diumumkan/digunakan/dipublikasikan</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Jumlah Anggota:</strong></td>
                                    <td>{{ $submission->member_count }} orang</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%"><strong>Pengusul:</strong></td>
                                    <td>
                                        <div>
                                            <strong>{{ $submission->user->nama }}</strong>
                                            <br><small class="text-muted">{{ $submission->user->nidn }}</small>
                                            <br><small class="text-muted">{{ $submission->user->email }}</small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Program Studi:</strong></td>
                                    <td>{{ $submission->user->program_studi }}</td>
                                </tr>
                                <!-- <tr>
                                    <td><strong>Departemen:</strong></td>
                                    <td>{{ $submission->user->department->name ?? 'N/A' }}</td>
                                </tr> -->
                                <tr>
                                    <td><strong>Tanggal Submit:</strong></td>
                                    <td>{{ $submission->submission_date ? $submission->submission_date->setTimezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB' : '-' }}</td>
                                </tr>
                                @if($submission->reviewed_at)
                                <tr>
                                    <td><strong>Tanggal Review:</strong></td>
                                    <td>{{ $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Dibuat Pada:</strong></td>
                                    <td>
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

                    @if($submission->review_notes)
    <hr>
    <div class="row">
        <div class="col-12">
            <h6><strong>Catatan Review:</strong></h6>
            <div class="p-3 border rounded 
                        {{ $submission->status === 'approved' ? 'bg-success-subtle border-success' : 
                           ($submission->status === 'rejected' ? 'bg-danger-subtle border-danger' : 
                            'bg-warning-subtle border-warning') }}">
                <div class="{{ $submission->status === 'approved' ? 'text-success-emphasis' : 
                               ($submission->status === 'rejected' ? 'text-danger-emphasis' : 
                                'text-warning-emphasis') }}">
                    <i class="bi bi-{{ $submission->status === 'approved' ? 'check-circle' : 
                                       ($submission->status === 'rejected' ? 'x-circle' : 'exclamation-triangle') }} me-2"></i>
                    <strong>{{ $submission->review_notes }}</strong>
                </div>
                @if($submission->reviewed_at)
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i> 
                            {{ $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

                    <!-- Additional Data berdasarkan creation_type -->
                    @if($submission->additional_data)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6><strong>Informasi Tambahan:</strong></h6>
                                @php 
                                    $additionalData = is_string($submission->additional_data) ? 
                                                    json_decode($submission->additional_data, true) : 
                                                    $submission->additional_data; 
                                @endphp
                                
                                @if($submission->creation_type === 'program_komputer' && isset($additionalData['program_link']))
                                    <p><strong>Link Program:</strong> 
                                        <a href="{{ $additionalData['program_link'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-link-45deg"></i> Akses Program
                                        </a>
                                    </p>
                                @endif

                                @if($submission->creation_type === 'sinematografi' && isset($additionalData['video_link']))
                                    <p><strong>Link Video:</strong> 
                                        <a href="{{ $additionalData['video_link'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-play-circle"></i> Tonton Video
                                        </a>
                                    </p>
                                @endif

                                @if($submission->creation_type === 'buku')
                                    @if(isset($additionalData['isbn']))
                                        <p><strong>ISBN:</strong> {{ $additionalData['isbn'] }}</p>
                                    @endif
                                    @if(isset($additionalData['page_count']))
                                        <p><strong>Jumlah Halaman:</strong> {{ $additionalData['page_count'] }}</p>
                                    @endif
                                @endif

                                @if($submission->creation_type === 'alat_peraga')
                                    @if(isset($additionalData['subject']))
                                        <p><strong>Mata Pelajaran:</strong> {{ $additionalData['subject'] }}</p>
                                    @endif
                                    @if(isset($additionalData['education_level']))
                                        <p><strong>Tingkat Pendidikan:</strong> {{ strtoupper($additionalData['education_level']) }}</p>
                                    @endif
                                @endif

                                @if($submission->creation_type === 'basis_data')
                                    @if(isset($additionalData['database_type']))
                                        <p><strong>Jenis Database:</strong> {{ $additionalData['database_type'] }}</p>
                                    @endif
                                    @if(isset($additionalData['record_count']))
                                        <p><strong>Jumlah Record:</strong> {{ number_format($additionalData['record_count']) }}</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Anggota Pencipta -->
            @if($submission->members->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
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
                                @foreach($submission->members->sortBy('position') as $index => $member)
                                <tr>
                                    <td>{{ $member->position - 1}}</td>
                                    <td>
                                        <strong>{{ $member->name }}</strong>
                                       <!-- @if($member->is_leader)
                                            <br><span class="badge bg-success">Ketua</span>
                                        @endif -->
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $member->email }}" class="text-decoration-none">
                                            {{ $member->email }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/62{{ ltrim($member->whatsapp, '0') }}" 
                                           target="_blank" class="text-decoration-none text-success">
                                            <i class="bi bi-whatsapp"></i> {{ $member->whatsapp }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($member->ktp)
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.submissions.member-ktp-preview', [$submission, $member]) }}" 
                                                   class="btn btn-sm btn-outline-primary" target="_blank" title="Preview KTP">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-muted">No KTP</span>
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
            @if($submission->documents->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>Dokumen ({{ $submission->documents->count() }} file)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="35%">Nama File</th>
                                    <th width="15%">Jenis</th>
                                    <th width="10%">Ukuran</th>
                                    <th width="15%">Upload Date</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submission->documents as $index => $document)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <i class="bi bi-file-earmark-{{ pathinfo($document->file_name, PATHINFO_EXTENSION) === 'pdf' ? 'pdf' : 'text' }} me-2"></i>
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
                                    <td>{{ $document->uploaded_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.submissions.document-download', [$submission, $document]) }}" 
                                               class="btn btn-sm btn-outline-success" title="Download">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            
                                            @php
                                                $extension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));
                                                $previewableTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
                                            @endphp
                                            
                                            @if(in_array($extension, $previewableTypes))
                                                <a href="{{ route('admin.submissions.document-preview', [$submission, $document]) }}" 
                                                   class="btn btn-sm btn-outline-primary" target="_blank" title="Preview">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                        title="Preview not available for this file type" disabled>
                                                    <i class="bi bi-eye-slash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- History -->
            @if($submission->histories->count() > 0)
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @foreach($submission->histories->sortByDesc('created_at') as $history)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $history->action === 'Approved' ? 'success' : ($history->action === 'Rejected' ? 'danger' : 'primary') }}"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">{{ $history->action }}</h6>
                                <p class="timeline-text text-muted">
                                    {{ $history->notes }}
                                </p>
                                <small class="text-muted">
                                    <i class="bi bi-person"></i> {{ $history->user->nama }} • 
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
        <div class="col-xl-4 col-lg-5">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-lightning me-2"></i>Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($submission->status === 'submitted')
                            <form action="{{ route('admin.submissions.assign-to-self', $submission) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-person-check"></i> Assign untuk Review
                                </button>
                            </form>
                        @endif
                        
                        @if($submission->status === 'under_review' && $submission->reviewer_id === Auth::id())
                            <button type="button" class="btn btn-success" onclick="showApproveModal()">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                            <button type="button" class="btn btn-warning" onclick="showRevisionModal()">
                                <i class="bi bi-arrow-clockwise"></i> Request Revision
                            </button>
                            <button type="button" class="btn btn-danger" onclick="showRejectModal()">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        @endif
                        
                        <a href="{{ route('admin.users.show', $submission->user) }}" class="btn btn-outline-info">
                            <i class="bi bi-person"></i> Lihat Profile Pengusul
                        </a>
                        
                        <!--<button type="button" class="btn btn-outline-secondary" onclick="printSubmission()">
                            <i class="bi bi-printer"></i> Print Detail
                        </button> -->
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-diagram-3 me-2"></i>Status Progress
                    </h6>
                </div>
                <div class="card-body">
                    @php
                    $statusColor = \App\Helpers\StatusHelper::getStatusColor($submission->status);
                    $statusIcon = \App\Helpers\StatusHelper::getStatusIcon($submission->status);
                    $statusName = \App\Helpers\StatusHelper::getStatusName($submission->status);
                    @endphp

                    <!-- Status Badge -->
                    <span class="badge bg-{{ $statusColor }} fs-6 px-3 py-2 mb-3">
                        <i class="bi bi-{{ $statusIcon }} me-2"></i>{{ $statusName }}
                    </span>

                    <!-- Status Timeline -->
                    <div class="status-timeline mt-3">
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
                                    <br><small>{{ $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Templates Card (Only show for approved submissions) -->
            @if($submission->status === 'approved')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-file-earmark-word me-2"></i>Generate Dokumen Template
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small mb-3">
                        <p class="text-muted mb-2">
                            <i class="bi bi-info-circle"></i> 
                            Generate dokumen template otomatis berdasarkan data submission yang sudah approved.
                        </p>
                    </div>
                    
                    <div class="row g-2">

                        <!-- Surat KTP -->
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-1">
                                                <i class="bi bi-file-earmark-person text-primary"></i> Dokumen KTP Lengkap
                                            </h6>
                                            <small class="text-muted">
                                                Dokumen lengkap dengan foto KTP semua anggota yang terpasang otomatis
                                                <br><span class="badge bg-success">Auto-Insert KTP</span>
                                            </small>
                                        </div>
                                        <form action="{{ route('admin.submissions.generate-template', $submission) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="template_type" value="surat_ktp">
                                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-download"></i> Generate & Auto-Insert
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Surat Pengalihan -->
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-1">
                                                <i class="bi bi-file-earmark-arrow-right text-success"></i> Surat Pengalihan Hak
                                            </h6>
                                            <small class="text-muted">Surat pengalihan hak cipta ke institusi</small>
                                        </div>
                                        <form action="{{ route('admin.submissions.generate-template', $submission) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="template_type" value="surat_pengalihan">
                                            <button type="submit" class="btn btn-outline-success btn-sm">
                                                <i class="bi bi-download"></i> Generate
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Surat Pernyataan -->
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="card-title mb-1">
                                                <i class="bi bi-file-earmark-check text-warning"></i> Surat Pernyataan
                                            </h6>
                                            <small class="text-muted">Surat pernyataan keaslian karya</small>
                                        </div>
                                        <form action="{{ route('admin.submissions.generate-template', $submission) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="template_type" value="surat_pernyataan">
                                            <button type="submit" class="btn btn-outline-warning btn-sm">
                                                <i class="bi bi-download"></i> Generate
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-lightbulb"></i>
                                <strong>Tips:</strong> Dokumen yang dihasilkan dalam format Word (.docx) dan dapat diedit sesuai kebutuhan sebelum dicetak atau dikirim.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Review Guidelines -->
            @if($submission->status === 'under_review' && $submission->reviewer_id === Auth::id())
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-info-circle me-2"></i>Panduan Review
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <h6 class="fw-bold">Hal yang Perlu Diperiksa:</h6>
                        <ul class="mb-3">
                            <li>Kelengkapan dokumen pendukung</li>
                            <li>Kualitas dan kejelasan dokumen</li>
                            <li>Kesesuaian dengan jenis HKI</li>
                            <li>Data anggota pencipta</li>
                            <li>Originalitas karya</li>
                        </ul>
                        
                        <h6 class="fw-bold">Keputusan Review:</h6>
                        <ul class="mb-3">
                            <li><strong>Approve:</strong> Jika semua persyaratan terpenuhi</li>
                            <li><strong>Revision:</strong> Jika ada yang perlu diperbaiki</li>
                            <li><strong>Reject:</strong> Jika tidak memenuhi syarat</li>
                        </ul>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-lightbulb me-2"></i>
                            <strong>Tips:</strong> Berikan catatan yang konstruktif dan spesifik untuk membantu user.
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Actions (Approve, Revision, Reject) -->
<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Approve Submission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.submissions.approve', $submission) }}" method="POST">
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
                        <strong>Submission "{{ $submission->title }}" akan diapprove.</strong>
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
            <form action="{{ route('admin.submissions.revision', $submission) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="revision_notes" class="form-label">Catatan Revisi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="revision_notes" name="review_notes" rows="4" 
                                  placeholder="Jelaskan apa yang perlu diperbaiki..." required></textarea>
                        <div class="form-text">Berikan panduan yang jelas untuk user melakukan perbaikan</div>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>User akan dapat mengedit dan resubmit submission ini.</strong>
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
            <form action="{{ route('admin.submissions.reject', $submission) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject_notes" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_notes" name="review_notes" rows="4" 
                                  placeholder="Jelaskan alasan penolakan..." required></textarea>
                        <div class="form-text">Berikan alasan yang jelas dan konstruktif untuk user</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="reject_confirm" required>
                            <label class="form-check-label" for="reject_confirm">
                                Saya yakin ingin menolak submission ini
                            </label>
                        </div>
                    </div>
                    
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Submission "{{ $submission->title }}" akan ditolak secara permanen.</strong>
                        <br><small>User tidak dapat mengedit submission yang sudah ditolak.</small>
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
// Modal functions
function showApproveModal() {
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function showRevisionModal() {
    new bootstrap.Modal(document.getElementById('revisionModal')).show();
}

function showRejectModal() {
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

// Print function
function printSubmission() {
    const submissionData = {
        title: "{{ $submission->title }}",
        id: "{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}",
        user: "{{ $submission->user->nama }}",
        nidn: "{{ $submission->user->nidn }}",
        email: "{{ $submission->user->email }}",
        program_studi: "{{ $submission->user->program_studi }}",
        department: "{{ $submission->user->department->name ?? 'N/A' }}",
        type: "{{ ucfirst($submission->type) }}",
        creation_type: "{{ ucfirst(str_replace('_', ' ', $submission->creation_type)) }}",
        status: "{{ \App\Helpers\StatusHelper::getStatusName($submission->status) }}",
        member_count: "{{ $submission->member_count }}",
        submission_date: "{{ $submission->submission_date ? $submission->submission_date->setTimezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB' : '-' }}",
        description: "{{ $submission->description }}",
        review_notes: "{{ $submission->review_notes ?? '' }}",
        reviewed_at: "{{ $submission->reviewed_at ? $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') . ' WIB' : '' }}",
        alamat: "{{ $submission->alamat ?? '-' }}",
        kode_pos: "{{ $submission->kode_pos ?? '-' }}",
        formatted_address: "{{ $submission->formatted_address }}",
    };
    
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Detail Submission - ${submissionData.title}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
                .section { margin-bottom: 15px; }
                .label { font-weight: bold; display: inline-block; width: 150px; }
                .value { display: inline-block; }
                .status { padding: 3px 8px; border-radius: 3px; font-size: 12px; }
                @media print { body { margin: 0; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>DETAIL SUBMISSION HKI</h2>
                <p>SiHaki - STMIK AMIKOM Surakarta</p>
            </div>
            
            <div class="section">
                <h3>Informasi Dasar</h3>
                <div><span class="label">ID Submission:</span> <span class="value">#${submissionData.id}</span></div>
                <div><span class="label">Judul:</span> <span class="value">${submissionData.title}</span></div>
                <div><span class="label">Jenis HKI:</span> <span class="value">${submissionData.type}</span></div>
                <div><span class="label">Jenis Ciptaan:</span> <span class="value">${submissionData.creation_type}</span></div>
                <div><span class="label">Status:</span> <span class="value">${submissionData.status}</span></div>
                <div><span class="label">Tanggal Submit:</span> <span class="value">${submissionData.submission_date}</span></div>
            </div>
            
            <div class="section">
                <h3>Informasi User</h3>
                <div><span class="label">Nama:</span> <span class="value">${submissionData.user}</span></div>
                <div><span class="label">NIDN:</span> <span class="value">${submissionData.nidn}</span></div>
                <div><span class="label">Email:</span> <span class="value">${submissionData.email}</span></div>
                <div><span class="label">Program Studi:</span> <span class="value">${submissionData.program_studi}</span></div>
                <div><span class="label">Departemen:</span> <span class="value">${submissionData.department}</span></div>
            </div>
            
            <div class="section">
                <h3>Deskripsi</h3>
                <p>${submissionData.description}</p>
            </div>
            
            ${submissionData.review_notes ? `
            <div class="section">
                <h3>Catatan Review</h3>
                <p><strong>Tanggal Review:</strong> ${submissionData.reviewed_at}</p>
                <p><strong>Catatan:</strong> ${submissionData.review_notes}</p>
            </div>
            ` : ''}
            
            ${submissionData.alamat !== '-' ? `
            <div class="section">
                <h3>Alamat Pengalihan</h3>
                <div><span class="label">Alamat:</span> <span class="value">${submissionData.alamat}</span></div>
                <div><span class="label">Kode Pos:</span> <span class="value">${submissionData.kode_pos}</span></div>
                <div style="margin-top: 10px; padding: 10px; background: #f5f5f5; border-radius: 5px;">
                    <strong>Alamat Lengkap untuk Surat:</strong><br>
                    ${submissionData.formatted_address}
                </div>
            </div>
            ` : ''}
            
            <div class="section">
                <p style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
                    Dicetak pada: ${new Date().toLocaleString('id-ID', {timeZone: 'Asia/Jakarta'})} WIB
                    <br>SiHaki - Sistem Informasi Hak Kekayaan Intelektual
                </p>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Approve form validation
    const approveForm = document.querySelector('#approveModal form');
    if (approveForm) {
        approveForm.addEventListener('submit', function(e) {
            const notes = document.getElementById('approve_notes').value.trim();
            if (notes.length < 10) {
                e.preventDefault();
                alert('Catatan approval harus minimal 10 karakter');
                return false;
            }
        });
    }
    
    // Revision form validation
    const revisionForm = document.querySelector('#revisionModal form');
    if (revisionForm) {
        revisionForm.addEventListener('submit', function(e) {
            const notes = document.getElementById('revision_notes').value.trim();
            if (notes.length < 20) {
                e.preventDefault();
                alert('Catatan revisi harus minimal 20 karakter untuk memberikan panduan yang jelas');
                return false;
            }
        });
    }
    
    // Reject form validation
    const rejectForm = document.querySelector('#rejectModal form');
    if (rejectForm) {
        rejectForm.addEventListener('submit', function(e) {
            const notes = document.getElementById('reject_notes').value.trim();
            const confirm = document.getElementById('reject_confirm').checked;
            
            if (notes.length < 20) {
                e.preventDefault();
                alert('Alasan penolakan harus minimal 20 karakter');
                return false;
            }
            
            if (!confirm) {
                e.preventDefault();
                alert('Mohon centang konfirmasi untuk melanjutkan penolakan');
                return false;
            }
            
            // Final confirmation
            if (!window.confirm('Apakah Anda yakin ingin menolak submission ini? Tindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endpush

@push('styles')
<style>
/* Timeline Styles */
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
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e3e6f0;
}

.timeline-content {
    background: #f8f9fc;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #e3e6f0;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: 600;
}

.timeline-text {
    margin: 0 0 10px 0;
    font-size: 13px;
}

/* Status Timeline Styles */
.status-timeline {
    position: relative;
}

.status-step {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    opacity: 0.5;
    transition: all 0.3s ease;
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

.status-step.active .status-icon {
    background-color: #17a2b8;
    color: white;
}

.status-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e3e6f0;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 16px;
}

.status-text {
    flex: 1;
}

/* Card Hover Effects */
.card:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

/* Badge Styles */
.badge {
    font-size: 0.75em;
}

/* Responsive Design */
@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .timeline {
        padding-left: 20px;
    }
    
    .timeline-marker {
        left: -15px;
    }
    
    .status-step {
        flex-direction: column;
        text-align: center;
    }
    
    .status-icon {
        margin-right: 0;
        margin-bottom: 10px;
    }
}
</style>
@endpush
@endsection