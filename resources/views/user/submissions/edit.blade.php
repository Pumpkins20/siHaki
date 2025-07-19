@extends('layouts.user')

@section('title', 'Edit Submission')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.submissions.index') }}">Submissions</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.submissions.show', $submission) }}">{{ Str::limit($submission->title, 30) }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Edit Submission</h1>
            <p class="text-muted">Update informasi submission HKI Anda</p>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Informasi Submission</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.submissions.update', $submission) }}" method="POST" enctype="multipart/form-data" id="submissionEditForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Current Status Info -->
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle"></i>
                            <strong>Status saat ini:</strong> 
                            <span class="badge bg-{{ $submission->status === 'draft' ? 'secondary' : 'info' }}">
                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                            </span>
                            @if($submission->status === 'revision_needed')
                                <br><small class="mt-1 d-block">Lakukan perubahan sesuai catatan reviewer dan submit ulang.</small>
                            @endif
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul HKI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $submission->title) }}" 
                                   placeholder="Masukkan judul HKI Anda" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Gunakan judul yang jelas dan deskriptif</div>
                        </div>

                        <!-- Type -->
                        <div class="mb-3">
                            <label for="type" class="form-label">Jenis HKI <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Pilih Jenis HKI</option>
                                <option value="copyright" {{ old('type', $submission->type) == 'copyright' ? 'selected' : '' }}>Hak Cipta (Copyright)</option>
                                <option value="patent" {{ old('type', $submission->type) == 'patent' ? 'selected' : '' }}>Paten</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tanggal publish -->
                        <div class="mb-3">
                            <label for="first_publication_date" class="form-label">
                                Tanggal Pertama Kali Diumumkan/Digunakan/Dipublikasikan <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('first_publication_date') is-invalid @enderror" 
                                id="first_publication_date" name="first_publication_date" 
                                value="{{ old('first_publication_date', $submission->first_publication_date?->format('Y-m-d')) }}" required>
                            @error('first_publication_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                Tanggal pertama kali karya ini diumumkan, digunakan, atau dipublikasikan secara umum
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" 
                                      placeholder="Jelaskan secara detail tentang HKI yang akan diajukan" required>{{ old('description', $submission->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimal 50 karakter, maksimal 1000 karakter</div>
                        </div>

                        <!-- Reviewer Notes (if any) -->
                        @if($submission->review_notes && $submission->status === 'revision_needed')
                        <div class="mb-3">
                            <label class="form-label">Catatan Reviewer</label>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                {{ $submission->review_notes }}
                            </div>
                        </div>
                        @endif

                        <!-- Current Documents -->
                        @if($submission->documents->count() > 0)
                        <div class="mb-3">
                            <label class="form-label">Dokumen Saat Ini</label>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Jenis</th>
                                            <th>Nama File</th>
                                            <th>Ukuran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($submission->documents as $document)
                                        <tr>
                                            <td>
                                                @if($document->document_type === 'main_document')
                                                    <span class="badge bg-primary">Dokumen Utama</span>
                                                @else
                                                    <span class="badge bg-secondary">Dokumen Pendukung</span>
                                                @endif
                                            </td>
                                            <td>{{ $document->file_name }}</td>
                                            <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                            <td>
                                                <a href="{{ route('user.submissions.documents.download', $document) }}" 
                                                   class="btn btn-sm btn-outline-success" title="Download">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                @if($document->document_type !== 'main_document')
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
                        </div>
                        @endif

                        <!-- Replace Main Document -->
                        <div class="mb-3">
                            <label for="main_document" class="form-label">
                                {{ $submission->documents->where('document_type', 'main_document')->count() > 0 ? 'Ganti Dokumen Utama' : 'Dokumen Utama' }}
                                @if($submission->documents->where('document_type', 'main_document')->count() == 0)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <input type="file" class="form-control @error('main_document') is-invalid @enderror" 
                                   id="main_document" name="main_document" accept=".pdf,.doc,.docx"
                                   {{ $submission->documents->where('document_type', 'main_document')->count() == 0 ? 'required' : '' }}>
                            @error('main_document')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Format yang diterima: PDF, DOC, DOCX. Maksimal 10MB.
                                @if($submission->documents->where('document_type', 'main_document')->count() > 0)
                                    <br><strong>Catatan:</strong> Jika Anda upload file baru, dokumen lama akan diganti.
                                @endif
                            </div>
                        </div>

                        <!-- Additional Supporting Documents -->
                        <div class="mb-3">
                            <label for="supporting_documents" class="form-label">Tambah Dokumen Pendukung (Opsional)</label>
                            <input type="file" class="form-control @error('supporting_documents.*') is-invalid @enderror" 
                                   id="supporting_documents" name="supporting_documents[]" 
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" multiple>
                            @error('supporting_documents.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Anda dapat mengupload beberapa file sekaligus. Format: PDF, DOC, DOCX, JPG, PNG. Maksimal 5MB per file.
                                <br><strong>Catatan:</strong> File ini akan ditambahkan ke dokumen yang sudah ada.
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('user.submissions.show', $submission) }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                    <div>
                                        @if($submission->status === 'draft')
                                            <button type="submit" name="save_as_draft" class="btn btn-outline-primary me-2">
                                                <i class="bi bi-save"></i> Simpan Draft
                                            </button>
                                        @endif
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-send"></i> 
                                            {{ $submission->status === 'revision_needed' ? 'Submit Revisi' : 'Update & Submit' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Current Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Submission</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="status-indicator mb-3">
                            @if($submission->status === 'draft')
                                <i class="bi bi-file-earmark-text fs-1 text-secondary"></i>
                                <h5 class="mt-2">Draft</h5>
                                <p class="text-muted">Submission dalam bentuk draft</p>
                            @elseif($submission->status === 'revision_needed')
                                <i class="bi bi-arrow-clockwise fs-1 text-warning"></i>
                                <h5 class="mt-2">Perlu Revisi</h5>
                                <p class="text-muted">Lakukan perubahan sesuai catatan reviewer</p>
                            @endif
                        </div>
                        
                        @if($submission->submission_date)
                            <div class="small text-muted">
                                <strong>Tanggal Submit:</strong><br>
                                {{ $submission->submission_date->format('d M Y H:i') }}
                            </div>
                        @endif
                        
                        @if($submission->reviewed_at)
                            <div class="small text-muted mt-2">
                                <strong>Tanggal Review:</strong><br>
                                {{ $submission->reviewed_at->format('d M Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Guidelines for Revision -->
            @if($submission->status === 'revision_needed')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Panduan Revisi</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <h6>Langkah-langkah Revisi:</h6>
                        <ol class="mb-3">
                            <li>Baca catatan reviewer dengan teliti</li>
                            <li>Lakukan perubahan sesuai saran</li>
                            <li>Update dokumen jika diperlukan</li>
                            <li>Submit ulang untuk review</li>
                        </ol>
                        
                        <div class="alert alert-warning">
                            <small>
                                <i class="bi bi-exclamation-triangle"></i>
                                Pastikan semua saran reviewer telah diterapkan sebelum submit ulang.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Help Section -->
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
                            Senin-Jumat: 08:00-16:00
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for description
    const description = document.getElementById('description');
    const counter = document.createElement('div');
    counter.className = 'form-text text-end';
    description.parentNode.appendChild(counter);
    
    function updateCounter() {
        const length = description.value.length;
        counter.textContent = `${length}/1000 karakter`;
        counter.className = `form-text text-end ${length > 1000 ? 'text-danger' : 'text-muted'}`;
    }
    
    description.addEventListener('input', updateCounter);
    updateCounter();

    // File size validation
    document.getElementById('main_document').addEventListener('change', function() {
        const file = this.files[0];
        if (file && file.size > 10 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 10MB.');
            this.value = '';
        }
    });

    document.getElementById('supporting_documents').addEventListener('change', function() {
        for (let file of this.files) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file ' + file.name + ' terlalu besar. Maksimal 5MB per file.');
                this.value = '';
                break;
            }
        }
    });

    // Form submission handling
    document.getElementById('submissionEditForm').addEventListener('submit', function(e) {
        const submitButton = e.submitter;
        if (submitButton) {
            submitButton.disabled = true;
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
            
            // Re-enable after 5 seconds if still processing
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }, 5000);
        }
    });
});
</script>
@endpush
@endsection