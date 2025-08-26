@extends('layouts.admin')

@section('title', 'Kirim Sertifikat - ' . $submission->title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.certificates.index') }}" class="text-decoration-none">Kirim Sertifikat</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($submission->title, 30) }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Kelola Sertifikat</h1>
                    <p class="text-muted mb-0">ID: #{{ str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
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
                                    <th width="20%">Nama</th>
                                    <th width="8%">Role</th>
                                    <th width="18%">Email</th>
                                    <th width="12%">WhatsApp</th>
                                    <th width="22%">Alamat</th>
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
                                        @if($member->is_leader)
                                            <span class="badge bg-success">Pencipta Utama</span>
                                        @else
                                            <span class="badge bg-secondary">Anggota {{ $loop->iteration - 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $member->email }}" class="text-decoration-none">
                                            <small>{{ $member->email }}</small>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="https://wa.me/62{{ ltrim($member->whatsapp, '0') }}" 
                                        target="_blank" class="text-decoration-none text-success">
                                            <i class="bi bi-whatsapp"></i> {{ $member->whatsapp }}
                                        </a>
                                    </td>
                                    <td>
                                        {{-- ✅ NEW: Display member address like in user show --}}
                                        @if($member->alamat)
                                            <small class="text-muted">
                                                {{ Str::limit($member->alamat, 40) }}
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
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.submissions.member-ktp-preview', [$submission, $member]) }}" 
                                                class="btn btn-sm btn-outline-primary" target="_blank" title="Preview KTP">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
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
        <div class="col-lg-4">
            <!-- Status Certificate -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Sertifikat</h6>
                </div>
                <div class="card-body text-center">
                    @if($certificateSent)
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-success">Sertifikat Sudah Dikirim</h5>
                        <p class="text-muted">Sertifikat telah berhasil dikirim ke user</p>
                        @php
                            $certificate = $submission->documents()->where('document_type', 'certificate')->first();
                        @endphp
                        <a href="{{ route('admin.certificates.document-download', [$submission, $certificate]) }}" 
                           class="btn btn-success">
                            <i class="bi bi-download"></i> Unduh Sertifikat
                        </a>
                    @else
                        <div class="mb-3">
                            <i class="bi bi-clock-fill text-warning" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-warning">Menunggu Pengiriman</h5>
                        <p class="text-muted">Sertifikat belum dikirim ke user</p>
                    @endif
                </div>
            </div>

            <!-- Form Kirim Sertifikat -->
            @if(!$certificateSent)
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-send me-2"></i>Kirim Sertifikat
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.certificates.send', $submission) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="certificate_file" class="form-label">File Sertifikat <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('certificate_file') is-invalid @enderror" 
                                   id="certificate_file" name="certificate_file" accept=".pdf" required>
                            @error('certificate_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Upload file sertifikat dalam format PDF. Maksimal 10MB.</div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Tambahkan catatan untuk user...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>Setelah sertifikat dikirim, user akan menerima notifikasi dan dapat mengunduh sertifikat melalui sistem.</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-send"></i> Kirim Sertifikat
                            </button>
                            <a href="{{ route('admin.certificates.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // File validation
    const fileInput = document.getElementById('certificate_file');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (10MB max)
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 10MB.');
                    this.value = '';
                    return;
                }
                
                // Check file type
                if (file.type !== 'application/pdf') {
                    alert('File harus dalam format PDF.');
                    this.value = '';
                    return;
                }
            }
        });
    }
});
</script>
@endpush