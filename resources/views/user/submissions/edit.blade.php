{{-- filepath: resources/views/user/submissions/edit.blade.php --}}

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

                        {{-- ✅ REMOVED: Jenis HKI field - tidak perlu diedit --}}
                        {{-- Show current creation type as read-only info --}}
                        <div class="mb-3">
                            <label class="form-label">Jenis Pengajuan</label>
                            <div class="alert alert-light border">
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                <strong>{{ ucfirst(str_replace('_', ' ', $submission->creation_type)) }}</strong>
                                <br><small class="text-muted">Jenis pengajuan tidak dapat diubah setelah submission dibuat</small>
                            </div>
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
                            <div class="form-text">Maksimal 1000 karakter</div>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                    id="alamat" name="alamat" rows="3" 
                                    placeholder="Masukkan alamat lengkap" required>{{ old('alamat', $submission->alamat) }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Alamat lengkap untuk surat pengalihan</div>
                        </div>

                        <!-- Kode Pos -->
                        <div class="mb-3">
                            <label for="kode_pos" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" 
                                   id="kode_pos" name="kode_pos" value="{{ old('kode_pos', $submission->kode_pos) }}" 
                                   placeholder="Masukkan kode pos" maxlength="10" pattern="[0-9]{5}" required>
                            @error('kode_pos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                        <!-- Document Upload Section - ✅ UPDATED: Sesuaikan dengan creation_type -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i>Update Dokumen
                            </h6>

                            <!-- Dynamic Document Fields berdasarkan creation_type -->
                            @if($submission->creation_type === 'program_komputer')
                                <!-- Manual Document -->
                                <div class="mb-3">
                                    <label for="manual_document" class="form-label">
                                        {{ $submission->documents->where('document_type', 'main_document')->count() > 0 ? 'Ganti Manual Penggunaan Program (PDF)' : 'Upload Manual Penggunaan Program (PDF)' }}
                                        @if($submission->documents->where('document_type', 'main_document')->count() == 0)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control @error('manual_document') is-invalid @enderror" 
                                           id="manual_document" name="manual_document" accept=".pdf"
                                           {{ $submission->documents->where('document_type', 'main_document')->count() == 0 ? 'required' : '' }}>
                                    @error('manual_document')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Format: PDF. Cover, screenshot, dan manual dalam 1 file PDF. Maksimal 20MB.
                                        @if($submission->documents->where('document_type', 'main_document')->count() > 0)
                                            <br><small class="text-info"><i class="bi bi-info-circle"></i> File baru akan mengganti file yang sudah ada.</small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Program Link -->
                                <div class="mb-3">
                                    <label for="program_link" class="form-label">Link Program <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control @error('program_link') is-invalid @enderror" 
                                           id="program_link" name="program_link" 
                                           value="{{ old('program_link', $submission->additional_data['program_link'] ?? '') }}" 
                                           placeholder="https://github.com/username/repository" required>
                                    @error('program_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Link GitHub, GitLab, atau repositori lainnya</div>
                                </div>

                            @elseif($submission->creation_type === 'sinematografi')
                                <!-- Metadata File -->
                                <div class="mb-3">
                                    <label for="metadata_file" class="form-label">
                                        {{ $submission->documents->where('document_type', 'main_document')->count() > 0 ? 'Ganti File Metadata Video (PDF)' : 'Upload File Metadata Video (PDF)' }}
                                        @if($submission->documents->where('document_type', 'main_document')->count() == 0)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control @error('metadata_file') is-invalid @enderror" 
                                           id="metadata_file" name="metadata_file" accept=".pdf"
                                           {{ $submission->documents->where('document_type', 'main_document')->count() == 0 ? 'required' : '' }}>
                                    @error('metadata_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Format: PDF. Metadata lengkap video. Maksimal 20MB.
                                        @if($submission->documents->where('document_type', 'main_document')->count() > 0)
                                            <br><small class="text-info"><i class="bi bi-info-circle"></i> File baru akan mengganti file yang sudah ada.</small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Video Link -->
                                <div class="mb-3">
                                    <label for="video_link" class="form-label">Link Video <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control @error('video_link') is-invalid @enderror" 
                                           id="video_link" name="video_link" 
                                           value="{{ old('video_link', $submission->additional_data['video_link'] ?? '') }}" 
                                           placeholder="https://youtube.com/watch?v=..." required>
                                    @error('video_link')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Link YouTube, Vimeo, atau platform video lainnya</div>
                                </div>

                            @elseif($submission->creation_type === 'buku')
                                <!-- E-book File -->
                                <div class="mb-3">
                                    <label for="ebook_file" class="form-label">
                                        {{ $submission->documents->where('document_type', 'main_document')->count() > 0 ? 'Ganti File E-book (PDF)' : 'Upload File E-book (PDF)' }}
                                        @if($submission->documents->where('document_type', 'main_document')->count() == 0)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control @error('ebook_file') is-invalid @enderror" 
                                           id="ebook_file" name="ebook_file" accept=".pdf"
                                           {{ $submission->documents->where('document_type', 'main_document')->count() == 0 ? 'required' : '' }}>
                                    @error('ebook_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        Format: PDF. File lengkap buku. Maksimal 20MB.
                                        @if($submission->documents->where('document_type', 'main_document')->count() > 0)
                                            <br><small class="text-info"><i class="bi bi-info-circle"></i> File baru akan mengganti file yang sudah ada.</small>
                                        @endif
                                    </div>
                                </div>

                                <!-- ISBN -->
                                <div class="mb-3">
                                    <label for="isbn" class="form-label">ISBN (Opsional)</label>
                                    <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                           id="isbn" name="isbn" 
                                           value="{{ old('isbn', $submission->additional_data['isbn'] ?? '') }}" 
                                           placeholder="978-3-16-148410-0">
                                    @error('isbn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Jika sudah memiliki ISBN</div>
                                </div>

                                <!-- Page Count -->
                                <div class="mb-3">
                                    <label for="page_count" class="form-label">Jumlah Halaman <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('page_count') is-invalid @enderror" 
                                           id="page_count" name="page_count" min="1"
                                           value="{{ old('page_count', $submission->additional_data['page_count'] ?? '') }}" required>
                                    @error('page_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            @elseif($submission->creation_type === 'poster_fotografi')
                                <!-- Image Files -->
                                <div class="mb-3">
                                    <label for="image_files" class="form-label">
                                        {{ $submission->documents->where('document_type', 'supporting_document')->count() > 0 ? 'Tambah/Ganti File Gambar (JPG/PNG)' : 'File Gambar (JPG/PNG)' }}
                                        @if($submission->documents->where('document_type', 'supporting_document')->count() == 0)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control @error('image_files.*') is-invalid @enderror" 
                                           id="image_files" name="image_files[]" accept=".jpg,.jpeg,.png" multiple
                                           {{ $submission->documents->where('document_type', 'supporting_document')->count() == 0 ? 'required' : '' }}>
                                    @error('image_files.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format: JPG, PNG. Minimal 1 file. Maksimal 1MB per file.</div>
                                </div>

                            @elseif($submission->creation_type === 'alat_peraga')
                                <!-- Photo Files -->
                                <div class="mb-3">
                                    <label for="photo_files" class="form-label">
                                        {{ $submission->documents->where('document_type', 'supporting_document')->count() > 0 ? 'Tambah/Ganti Foto Alat Peraga (JPG/PNG)' : 'Foto Alat Peraga (JPG/PNG)' }}
                                        @if($submission->documents->where('document_type', 'supporting_document')->count() == 0)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control @error('photo_files.*') is-invalid @enderror" 
                                           id="photo_files" name="photo_files[]" accept=".jpg,.jpeg,.png" multiple
                                           {{ $submission->documents->where('document_type', 'supporting_document')->count() == 0 ? 'required' : '' }}>
                                    @error('photo_files.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format: JPG, PNG. Minimal 1 file. Maksimal 1MB per file.</div>
                                </div>

                                <!-- Subject -->
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                                           id="subject" name="subject" 
                                           value="{{ old('subject', $submission->additional_data['subject'] ?? '') }}" 
                                           placeholder="Matematika, Fisika, etc." required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Education Level -->
                                <div class="mb-3">
                                    <label for="education_level" class="form-label">Tingkat Pendidikan <span class="text-danger">*</span></label>
                                    <select class="form-select @error('education_level') is-invalid @enderror" 
                                            id="education_level" name="education_level" required>
                                        <option value="">Pilih Tingkat</option>
                                        <option value="sd" {{ old('education_level', $submission->additional_data['education_level'] ?? '') == 'sd' ? 'selected' : '' }}>SD</option>
                                        <option value="smp" {{ old('education_level', $submission->additional_data['education_level'] ?? '') == 'smp' ? 'selected' : '' }}>SMP</option>
                                        <option value="sma" {{ old('education_level', $submission->additional_data['education_level'] ?? '') == 'sma' ? 'selected' : '' }}>SMA</option>
                                        <option value="kuliah" {{ old('education_level', $submission->additional_data['education_level'] ?? '') == 'kuliah' ? 'selected' : '' }}>Kuliah</option>
                                    </select>
                                    @error('education_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            @elseif($submission->creation_type === 'basis_data')
                                <!-- Documentation File -->
                                <div class="mb-3">
                                    <label for="documentation_file" class="form-label">
                                        {{ $submission->documents->where('document_type', 'main_document')->count() > 0 ? 'Ganti Dokumentasi Basis Data (PDF)' : 'Dokumentasi Basis Data (PDF)' }}
                                        @if($submission->documents->where('document_type', 'main_document')->count() == 0)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="file" class="form-control @error('documentation_file') is-invalid @enderror" 
                                           id="documentation_file" name="documentation_file" accept=".pdf"
                                           {{ $submission->documents->where('document_type', 'main_document')->count() == 0 ? 'required' : '' }}>
                                    @error('documentation_file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format: PDF. Dokumentasi lengkap basis data. Maksimal 20MB.</div>
                                </div>

                                <!-- Database Type -->
                                <div class="mb-3">
                                    <label for="database_type" class="form-label">Jenis Database <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('database_type') is-invalid @enderror" 
                                           id="database_type" name="database_type" 
                                           value="{{ old('database_type', $submission->additional_data['database_type'] ?? '') }}" 
                                           placeholder="MySQL, PostgreSQL, MongoDB, etc." required>
                                    @error('database_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Record Count -->
                                <div class="mb-3">
                                    <label for="record_count" class="form-label">Jumlah Record <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('record_count') is-invalid @enderror" 
                                           id="record_count" name="record_count" min="1"
                                           value="{{ old('record_count', $submission->additional_data['record_count'] ?? '') }}" required>
                                    @error('record_count')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            @else
                                <!-- Generic main document upload untuk creation_type lainnya -->
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
                                    <div class="form-text">Format: PDF, DOC, DOCX. Maksimal 10MB.</div>
                                </div>
                            @endif
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
                                {{ $submission->submission_date->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                            </div>
                        @endif
                        
                        @if($submission->reviewed_at)
                            <div class="small text-muted mt-2">
                                <strong>Tanggal Review:</strong><br>
                                {{ $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Creation Type Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengajuan</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p class="mb-2">
                            <strong>Jenis Pengajuan:</strong><br>
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $submission->creation_type)) }}</span>
                        </p>
                        <p class="mb-2">
                            <strong>Jenis HKI:</strong><br>
                            <span class="badge bg-info">{{ ucfirst($submission->type) }}</span>
                        </p>
                        @if($submission->member_count)
                            <p class="mb-0">
                                <strong>Jumlah Anggota:</strong><br>
                                {{ $submission->member_count }} orang
                            </p>
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
                            Senin-Jumat: 08:00-16:00 WIB
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($submission->members->count() > 0)
    <div class="mb-4">
        <h6 class="fw-bold text-secondary mb-3">
            <i class="bi bi-people me-2"></i>Update Data Anggota Pencipta
        </h6>
        
        @foreach($submission->members as $index => $member)
            <div class="member-section {{ !$loop->last ? 'border-bottom pb-4 mb-4' : '' }}">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-circle text-success fs-5 me-2"></i>
                    <h6 class="mb-0 fw-bold">{{ $member->is_leader ? 'Ketua' : 'Anggota' }} {{ $loop->iteration }}</h6>
                    @if($member->is_leader)
                        <span class="badge bg-success ms-2">Ketua Tim</span>
                    @endif
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Pencipta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" 
                               name="members[{{ $index }}][name]" 
                               value="{{ old('members.'.$index.'.name', $member->name) }}" 
                               placeholder="Nama lengkap" required>
                        <input type="hidden" name="members[{{ $index }}][id]" value="{{ $member->id }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" 
                               name="members[{{ $index }}][whatsapp]" 
                               value="{{ old('members.'.$index.'.whatsapp', $member->whatsapp) }}" 
                               placeholder="08xxxxxxxxxx" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" 
                               name="members[{{ $index }}][email]" 
                               value="{{ old('members.'.$index.'.email', $member->email) }}" 
                               placeholder="email@example.com" required>
                    </div>
                    
                    {{-- ✅ NEW: KTP Revision Section --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-credit-card-2-front me-1"></i>
                            Update Scan KTP (Opsional)
                        </label>
                        
                        {{-- Current KTP Status --}}
                        @if($member->ktp)
                            <div class="current-ktp-info mb-2">
                                <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        <small class="text-success fw-bold">KTP sudah diupload</small>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                onclick="viewKtp('{{ route('admin.submissions.preview-member-ktp', [$submission, $member]) }}')"
                                                title="Lihat KTP">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning" 
                                                onclick="toggleKtpUpload({{ $index }})"
                                                title="Ganti KTP">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    Upload tanggal: {{ $member->updated_at->format('d M Y H:i') }} WIB
                                </small>
                            </div>
                        @else
                            <div class="current-ktp-info mb-2">
                                <div class="alert alert-warning py-2">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <small><strong>KTP belum diupload</strong></small>
                                </div>
                            </div>
                        @endif
                        
                        {{-- KTP Upload Input --}}
                        <div class="ktp-upload-section" id="ktpUpload_{{ $index }}" 
                             style="display: {{ $member->ktp ? 'none' : 'block' }};">
                            <input type="file" 
                                   class="form-control @error('members.'.$index.'.ktp') is-invalid @enderror" 
                                   name="members[{{ $index }}][ktp]" 
                                   accept=".jpg,.jpeg" 
                                   id="ktp_{{ $index }}"
                                   onchange="validateKtpFile(this, {{ $index }})">
                            @error('members.'.$index.'.ktp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <div class="form-text">
                                <i class="bi bi-info-circle text-primary me-1"></i>
                                Upload file KTP baru (JPG/JPEG, maksimal 2MB)
                                @if($member->ktp)
                                    <br><small class="text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        File baru akan mengganti KTP yang sudah ada
                                    </small>
                                @endif
                            </div>
                            
                            {{-- Preview untuk file baru --}}
                            <div class="ktp-preview mt-2" id="ktpPreview_{{ $index }}" style="display: none;">
                                <div class="border rounded p-2 bg-light">
                                    <small class="text-info">
                                        <i class="bi bi-image me-1"></i>
                                        <span id="ktpFileName_{{ $index }}"></span>
                                    </small>
                                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                                            onclick="clearKtpFile({{ $index }})" title="Hapus file">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        @if($member->ktp)
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                        onclick="cancelKtpUpdate({{ $index }})" 
                                        id="cancelKtpBtn_{{ $index }}" 
                                        style="display: none;">
                                    <i class="bi bi-x-circle me-1"></i>Batal Ganti KTP
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                
                {{-- Member revision notes --}}
                @if($submission->status === 'revision_needed' && $submission->review_notes)
                    <div class="mt-3">
                        <div class="alert alert-warning">
                            <small>
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                <strong>Catatan Reviewer:</strong> Pastikan data anggota dan KTP sudah sesuai dengan catatan revisi.
                            </small>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
        
        <div class="alert alert-info mt-3">
            <h6 class="alert-heading">
                <i class="bi bi-lightbulb me-2"></i>Tips Revisi KTP:
            </h6>
            <ul class="mb-0 small">
                <li>Upload ulang KTP jika foto tidak jelas atau terpotong</li>
                <li>Pastikan semua informasi di KTP dapat dibaca dengan baik</li>
                <li>Format file harus JPG/JPEG dengan ukuran maksimal 2MB</li>
                <li>KTP harus asli dan masih berlaku</li>
            </ul>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for description
    const description = document.getElementById('description');
    if (description) {
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
    }

    // ✅ NEW: Alamat and Kode Pos validation (from previous implementation)
    const alamatTextarea = document.getElementById('alamat');
    if (alamatTextarea) {
        const alamatCounter = document.createElement('div');
        alamatCounter.className = 'form-text text-end';
        alamatTextarea.parentNode.appendChild(alamatCounter);
        
        function updateAlamatCounter() {
            const length = alamatTextarea.value.length;
            alamatCounter.textContent = `${length}/500 karakter`;
            
            if (length > 450) {
                alamatCounter.className = 'form-text text-end text-warning';
            } else if (length > 500) {
                alamatCounter.className = 'form-text text-end text-danger';
            } else {
                alamatCounter.className = 'form-text text-end text-muted';
            }
        }
        
        alamatTextarea.addEventListener('input', updateAlamatCounter);
        updateAlamatCounter();
    }

    const kodePosInput = document.getElementById('kode_pos');
    if (kodePosInput) {
        kodePosInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length > 5) {
                this.value = this.value.slice(0, 5);
            }
        });
    }

    // File size validation for different file types
    const fileInputs = {
        'manual_document': 20 * 1024 * 1024,
        'metadata_file': 20 * 1024 * 1024,
        'ebook_file': 20 * 1024 * 1024,
        'documentation_file': 20 * 1024 * 1024,
        'image_files': 1 * 1024 * 1024,
        'photo_files': 1 * 1024 * 1024,
        'main_document': 10 * 1024 * 1024
    };

    Object.keys(fileInputs).forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('change', function() {
                const maxSize = fileInputs[inputId];
                
                if (this.multiple) {
                    for (let file of this.files) {
                        if (file.size > maxSize) {
                            alert(`Ukuran file ${file.name} terlalu besar. Maksimal ${maxSize / (1024 * 1024)}MB per file.`);
                            this.value = '';
                            break;
                        }
                    }
                } else {
                    const file = this.files[0];
                    if (file && file.size > maxSize) {
                        alert(`Ukuran file terlalu besar. Maksimal ${maxSize / (1024 * 1024)}MB.`);
                        this.value = '';
                    }
                }
            });
        }
    });

    // Form submission handling
    document.getElementById('submissionEditForm').addEventListener('submit', function(e) {
        const submitButton = e.submitter;
        if (submitButton) {
            submitButton.disabled = true;
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
            
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }, 5000);
        }
    });
});

// ✅ NEW: KTP Management Functions
function toggleKtpUpload(index) {
    const uploadSection = document.getElementById(`ktpUpload_${index}`);
    const cancelBtn = document.getElementById(`cancelKtpBtn_${index}`);
    
    if (uploadSection.style.display === 'none') {
        uploadSection.style.display = 'block';
        if (cancelBtn) cancelBtn.style.display = 'inline-block';
    } else {
        uploadSection.style.display = 'none';
        if (cancelBtn) cancelBtn.style.display = 'none';
        clearKtpFile(index);
    }
}

function cancelKtpUpdate(index) {
    const uploadSection = document.getElementById(`ktpUpload_${index}`);
    const cancelBtn = document.getElementById(`cancelKtpBtn_${index}`);
    const fileInput = document.getElementById(`ktp_${index}`);
    
    uploadSection.style.display = 'none';
    cancelBtn.style.display = 'none';
    
    // Clear file input
    if (fileInput) {
        fileInput.value = '';
    }
    
    // Hide preview
    const preview = document.getElementById(`ktpPreview_${index}`);
    if (preview) {
        preview.style.display = 'none';
    }
}

function validateKtpFile(input, index) {
    const file = input.files[0];
    if (!file) {
        clearKtpFile(index);
        return;
    }
    
    // Validate file size (2MB max)
    if (file.size > 2 * 1024 * 1024) {
        alert('Ukuran file KTP terlalu besar. Maksimal 2MB.');
        input.value = '';
        clearKtpFile(index);
        return;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
        alert('Format file KTP harus JPG atau JPEG.');
        input.value = '';
        clearKtpFile(index);
        return;
    }
    
    // Show preview
    showKtpPreview(index, file.name);
}

function showKtpPreview(index, fileName) {
    const preview = document.getElementById(`ktpPreview_${index}`);
    const fileNameSpan = document.getElementById(`ktpFileName_${index}`);
    
    if (preview && fileNameSpan) {
        fileNameSpan.textContent = fileName;
        preview.style.display = 'block';
    }
}

function clearKtpFile(index) {
    const fileInput = document.getElementById(`ktp_${index}`);
    const preview = document.getElementById(`ktpPreview_${index}`);
    
    if (fileInput) {
        fileInput.value = '';
    }
    
    if (preview) {
        preview.style.display = 'none';
    }
}

function viewKtp(url) {
    // Open KTP in new window/tab
    window.open(url, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
}

// ✅ NEW: Enhanced form validation before submit
function validateFormBeforeSubmit() {
    let isValid = true;
    const errors = [];
    
    // Validate basic fields
    const title = document.getElementById('title');
    if (title && title.value.trim().length < 5) {
        isValid = false;
        errors.push('Judul minimal 5 karakter');
        title.classList.add('is-invalid');
    }
    
    const description = document.getElementById('description');
    if (description && description.value.trim().length < 20) {
        isValid = false;
        errors.push('Deskripsi minimal 20 karakter');
        description.classList.add('is-invalid');
    }
    
    const alamat = document.getElementById('alamat');
    if (alamat && alamat.value.trim().length < 10) {
        isValid = false;
        errors.push('Alamat minimal 10 karakter');
        alamat.classList.add('is-invalid');
    }
    
    const kodePos = document.getElementById('kode_pos');
    if (kodePos && kodePos.value.length !== 5) {
        isValid = false;
        errors.push('Kode pos harus 5 digit');
        kodePos.classList.add('is-invalid');
    }
    
    // Validate member data
    const memberNames = document.querySelectorAll('input[name*="[name]"]');
    memberNames.forEach((input, index) => {
        if (input.value.trim().length < 3) {
            isValid = false;
            errors.push(`Nama anggota ${index + 1} minimal 3 karakter`);
            input.classList.add('is-invalid');
        }
    });
    
    if (!isValid) {
        alert('Mohon perbaiki kesalahan berikut:\n' + errors.join('\n'));
        
        // Scroll to first error
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
        
        return false;
    }
    
    return true;
}

// Attach validation to form submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('submissionEditForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateFormBeforeSubmit()) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endpush
@endsection