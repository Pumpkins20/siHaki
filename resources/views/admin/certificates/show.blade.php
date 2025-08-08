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
                                        <span class="badge bg-success">{{ ucfirst(str_replace('_', ' ', $submission->status)) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>User:</strong></td>
                                    <td>
                                        <div>
                                            <strong>{{ $submission->user->nama }}</strong>
                                            <br><small class="text-muted">{{ $submission->user->nidn }}</small>
                                            <br><small class="text-muted">{{ $submission->user->email }}</small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Approved:</strong></td>
                                    <td>{{ $submission->reviewed_at ? $submission->reviewed_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Reviewer:</strong></td>
                                    <td>{{ $submission->reviewer->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat Pengalihan:</strong></td>
                                    <td>
                                        @if($submission->alamat && $submission->kode_pos)
                                            <div class="alert alert-info">
                                                <strong>Alamat untuk Surat Pengalihan:</strong><br>
                                                <i class="bi bi-geo-alt me-1"></i>
                                                {{ $submission->formatted_address }}
                                            </div>
                                        @else
                                            <span class="text-muted">Alamat tidak tersedia</span>
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
                </div>
            </div>

            <!-- Dokumen Submission -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-files me-2"></i>Dokumen Submission
                    </h6>
                </div>
                <div class="card-body">
                    @if($submission->documents->where('document_type', '!=', 'certificate')->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="40%">Nama File</th>
                                        <th width="20%">Jenis</th>
                                        <th width="15%">Ukuran</th>
                                        <th width="20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submission->documents->where('document_type', '!=', 'certificate') as $index => $document)
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
                                                <span class="badge bg-secondary">Dokumen Pendukung</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                        <td>
                                            <a href="{{ route('admin.certificates.document-download', [$submission, $document]) }}" 
                                               class="btn btn-sm btn-outline-success" title="Unduh Dokumen">
                                                <i class="bi bi-download"></i> Unduh
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-file-earmark fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">Tidak ada dokumen</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- KTP Anggota Pencipta -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-credit-card me-2"></i>KTP Anggota Pencipta
                    </h6>
                </div>
                <div class="card-body">
                    @if($submission->members->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Nama</th>
                                        <th width="15%">Email</th>
                                        <th width="15%">WhatsApp</th>
                                        <th width="15%">Posisi</th>
                                        <th width="25%">KTP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($submission->members->sortBy('position') as $index => $member)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->email }}</td>
                                        <td>{{ $member->whatsapp }}</td>
                                        <td>
                                            @if($member->is_leader)
                                                <span class="badge bg-success">Ketua</span>
                                            @else
                                                <span class="badge bg-secondary">Anggota {{ $member->position }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($member->ktp)
                                                <a href="{{ route('admin.submissions.member-ktp', [$submission, $member]) }}" 
                                                   class="btn btn-sm btn-outline-primary" target="_blank" title="Lihat KTP">
                                                    <i class="bi bi-eye"></i> Lihat KTP
                                                </a>
                                            @else
                                                <span class="text-muted">No KTP</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-people fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">Tidak ada data anggota</p>
                        </div>
                    @endif
                </div>
            </div>
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