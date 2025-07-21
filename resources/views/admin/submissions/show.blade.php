{{-- filepath: resources/views/admin/submissions/show.blade.php --}}

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
                    <li class="breadcrumb-item"><a href="{{ route('admin.submissions.index') }}" class="text-decoration-none">Kelola Submission</a></li>
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
                        <i class="bi bi-info-circle me-2"></i>Informasi Submission
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
                                            $statusColors = [
                                                'submitted' => 'warning',
                                                'under_review' => 'info',
                                                'revision_needed' => 'secondary',
                                                'approved' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$submission->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Publikasi Pertama:</strong></td>
                                    <td>
                                        <i class="bi bi-calendar-event text-primary me-1"></i>
                                        {{ $submission->first_publication_date_formatted }}
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
                                    <td width="35%"><strong>User:</strong></td>
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
                                <tr>
                                    <td><strong>Departemen:</strong></td>
                                    <td>{{ $submission->user->department->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Submit:</strong></td>
                                    <td>{{ $submission->submission_date ? $submission->submission_date->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Reviewer:</strong></td>
                                    <td>
                                        @if($submission->reviewer)
                                            <div>
                                                <strong>{{ $submission->reviewer->nama }}</strong>
                                                @if($submission->reviewer_id === Auth::id())
                                                    <br><span class="badge bg-primary">Anda</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Belum di-assign</span>
                                        @endif
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
                            <div class="alert alert-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                {{ $submission->review_notes }}
                                @if($submission->reviewed_at)
                                    <br><small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $submission->reviewed_at->format('d M Y H:i') }}
                                        oleh {{ $submission->reviewer->nama }}
                                    </small>
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
                                @php $additionalData = is_string($submission->additional_data) ? json_decode($submission->additional_data, true) : $submission->additional_data; @endphp
                                
                                @if($submission->creation_type === 'program_komputer' && isset($additionalData['program_link']))
                                    <p><strong>Link Program:</strong> 
                                        <a href="{{ $additionalData['program_link'] }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-link-45deg"></i> Akses Program
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

                                @if($submission->creation_type === 'poster_fotografi')
                                    @if(isset($additionalData['image_type']))
                                        <p><strong>Jenis Gambar:</strong> {{ ucfirst($additionalData['image_type']) }}</p>
                                    @endif
                                    @if(isset($additionalData['width']) && isset($additionalData['height']))
                                        <p><strong>Dimensi:</strong> {{ $additionalData['width'] }} x {{ $additionalData['height'] }} px</p>
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
                                    <td>{{ $member->position }}</td>
                                    <td>
                                        <strong>{{ $member->name }}</strong>
                                        @if($member->is_leader)
                                            <br><span class="badge bg-success">Ketua</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $member->email }}" class="text-decoration-none">
                                            {{ $member->email }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/62{{ ltrim($member->whatsapp, '0') }}" 
                                           target="_blank" class="text-decoration-none">
                                            {{ $member->whatsapp }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($member->is_leader)
                                            <span class="badge bg-success">Ketua</span>
                                        @else
                                            <span class="badge bg-secondary">Anggota {{ $member->position }}</span>
                                        @endif
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
                                        @else
                                            <span class="badge bg-secondary">{{ $document->getFileDisplayNameAttribute() }}</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                    <td>{{ $document->uploaded_at->format('d M Y H:i') }}</td>
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
                        <i class="bi bi-clock-history me-2"></i>Riwayat Activity
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
                                    <i class="bi bi-calendar"></i> {{ $history->created_at->format('d M Y H:i') }}
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
                        <i class="bi bi-lightning me-2"></i>Quick Actions
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
                            <i class="bi bi-person"></i> Lihat Profile User
                        </a>
                        
                        <button type="button" class="btn btn-outline-secondary" onclick="printSubmission()">
                            <i class="bi bi-printer"></i> Print Detail
                        </button>
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
                    use App\Helpers\StatusHelper;
                    $statusColor = StatusHelper::getStatusColor($submission->status);
                    $statusIcon = StatusHelper::getStatusIcon($submission->status);
                    $statusName = StatusHelper::getStatusName($submission->status);
                    @endphp

                    <!-- Status Badge -->
                    <span class="badge bg-{{ $statusColor }} fs-6 px-3 py-2">
                        <i class="bi bi-{{ $statusIcon }} me-2"></i>{{ $statusName }}
                    </span>

                    <!-- Status Timeline -->
                    <div class="status-timeline">
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
                                <i class="bi bi-{{ StatusHelper::getStatusIcon($submission->status) }}"></i>
                            </div>
                            <div class="status-text">
                                <strong>{{ StatusHelper::getStatusName($submission->status) }}</strong>
                                @if($submission->reviewed_at)
                                    <br><small>{{ $submission->reviewed_at->format('d M Y H:i') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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

<!-- Document Preview Modal -->
<div class="modal fade" id="documentPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="previewContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="downloadLink" class="btn btn-success">
                    <i class="bi bi-download"></i> Download
                </a>
            </div>
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
    const printWindow = window.open('', '_blank');
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
        status: "{{ ucfirst(str_replace('_', ' ', $submission->status)) }}",
        member_count: "{{ $submission->member_count }}",
        submission_date: "{{ $submission->submission_date ? $submission->submission_date->format('d M Y H:i') : '-' }}",
        description: "{{ $submission->description }}",
        reviewer: "{{ $submission->reviewer->nama ?? 'Belum di-assign' }}",
        review_notes: "{{ $submission->review_notes ?? '' }}",
        reviewed_at: "{{ $submission->reviewed_at ? $submission->reviewed_at->format('d M Y H:i') : '' }}"
    };
    
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
                .status-approved { background: #d4edda; color: #155724; }
                .status-rejected { background: #f8d7da; color: #721c24; }
                .status-pending { background: #fff3cd; color: #856404; }
                .members-table, .documents-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                .members-table th, .members-table td, .documents-table th, .documents-table td { 
                    border: 1px solid #ddd; padding: 8px; text-align: left; 
                }
                .members-table th, .documents-table th { background-color: #f2f2f2; }
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
                <div><span class="label">Status:</span> <span class="value status">${submissionData.status}</span></div>
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
                <p><strong>Reviewer:</strong> ${submissionData.reviewer}</p>
                <p><strong>Tanggal Review:</strong> ${submissionData.reviewed_at}</p>
                <p><strong>Catatan:</strong> ${submissionData.review_notes}</p>
            </div>
            ` : ''}
            
            <div class="section">
                <p style="text-align: center; margin-top: 30px; font-size: 12px; color: #666;">
                    Dicetak pada: ${new Date().toLocaleString('id-ID')}
                    <br>SiHaki - Sistem Informasi Hak Kekayaan Intelektual
                </p>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

// Document preview function
function previewDocument(submissionId, documentId, fileName, downloadUrl) {
    const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
    const previewContent = document.getElementById('previewContent');
    const downloadLink = document.getElementById('downloadLink');
    
    // Update modal title
    document.querySelector('#documentPreviewModal .modal-title').textContent = 'Preview: ' + fileName;
    
    // Update download link
    downloadLink.href = downloadUrl;
    
    // Show loading
    previewContent.innerHTML = `
        <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    // Get file extension
    const extension = fileName.split('.').pop().toLowerCase();
    const previewUrl = `/admin/submissions/${submissionId}/documents/${documentId}/preview`;
    
    if (['pdf'].includes(extension)) {
        previewContent.innerHTML = `
            <iframe src="${previewUrl}" width="100%" height="600px" class="border"></iframe>
        `;
    } else if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
        previewContent.innerHTML = `
            <img src="${previewUrl}" class="img-fluid" style="max-height: 600px;">
        `;
    } else {
        previewContent.innerHTML = `
            <div class="alert alert-info">
                <i class="bi bi-info-circle fs-1 mb-3"></i>
                <h5>Preview Not Available</h5>
                <p>This file type cannot be previewed. Please download to view.</p>
            </div>
        `;
    }
    
    modal.show();
}

function previewKtp(submissionId, memberId, memberName) {
    const modal = new bootstrap.Modal(document.getElementById('documentPreviewModal'));
    const previewContent = document.getElementById('previewContent');
    const downloadLink = document.getElementById('downloadLink');
    
    // Update modal title
    document.querySelector('#documentPreviewModal .modal-title').textContent = 'KTP: ' + memberName;
    
    // Update download link
    downloadLink.href = `/admin/submissions/${submissionId}/members/${memberId}/ktp`;
    
    // Show KTP image
    const previewUrl = `/admin/submissions/${submissionId}/members/${memberId}/ktp/preview`;
    previewContent.innerHTML = `
        <img src="${previewUrl}" class="img-fluid border rounded" style="max-height: 600px;" alt="KTP ${memberName}">
    `;
    
    modal.show();
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
    
    // Auto-resize textareas
    document.querySelectorAll('textarea').forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
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

/* Table Responsive */
.table-responsive {
    border-radius: 0.375rem;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fc;
}

/* Modal Styles */
.modal-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.modal-footer {
    background-color: #f8f9fc;
    border-top: 1px solid #e3e6f0;
}

/* Form Styles */
.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Alert Styles */
.alert {
    border: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

/* Print Styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
    
    .btn {
        display: none !important;
    }
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

/* Custom Scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Loading States */
.btn.loading {
    pointer-events: none;
    opacity: 0.6;
}

.btn.loading::after {
    content: '';
    width: 16px;
    height: 16px;
    margin-left: 5px;
    border: 2px solid transparent;
    border-top: 2px solid currentColor;
    border-radius: 50%;
    display: inline-block;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush
@endsection