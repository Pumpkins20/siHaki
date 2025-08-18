{{-- filepath: resources/views/user/submissions/edit.blade.php --}}

@extends('layouts.user')

@section('title', 'Edit Submission')

@section('content')
<div class="container-fluid">
    <!-- Success/Error Alerts - Persistent -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show sticky-alert" role="alert" id="successAlert">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show sticky-alert" role="alert" id="errorAlert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show sticky-alert" role="alert" id="validationAlert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- FIXED: Reviewer Notes sebagai Fixed Header Banner -->
    @if($submission->review_notes && $submission->status === 'revision_needed')
    <div class="reviewer-notes-banner" id="reviewerNotesBanner">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-1 text-center d-none d-md-block">
                    <i class="bi bi-megaphone fs-4 text-warning"></i>
                </div>
                <div class="col-md-10 col-11">
                    <div class="reviewer-notes-content">
                        <div class="d-flex align-items-start">
                            <div class="me-2 d-md-none">
                                <i class="bi bi-megaphone fs-5 text-warning"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-2 text-white fs-6">
                                    <i class="bi bi-clipboard-check me-1"></i>
                                    <strong>CATATAN REVIEWER</strong>
                                </h6>
                                <div class="reviewer-note-text bg-white p-2 rounded">
                                    <p class="mb-1 fw-bold text-dark small">{{ $submission->review_notes }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ $submission->reviewed_at ? $submission->reviewed_at->setTimezone('Asia/Jakarta')->format('d M Y H:i') : '-' }} WIB
                                        </small>
                                        <small class="text-warning fw-bold">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            Perbaiki sesuai catatan!
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 col-1 text-center">
                    <button type="button" class="btn btn-outline-light btn-sm" 
                            onclick="toggleReviewerNotes()" id="toggleReviewerNotesBtn"
                            title="Sembunyikan catatan">
                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Page Header dengan margin yang disesuaikan -->
    <div class="row mb-4" style="margin-top: {{ $submission->review_notes && $submission->status === 'revision_needed' ? '80px' : '0' }};">
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
                        <div class="p-3 mb-3 border rounded bg-info-subtle border-info position-relative">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            <strong class="text-info-emphasis">Status saat ini:</strong> 
                            <span class="badge bg-{{ $submission->status === 'draft' ? 'secondary' : 'info' }}">
                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                            </span>
                            @if($submission->status === 'revision_needed')
                                <br><small class="mt-1 d-block text-info-emphasis">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Lakukan perubahan sesuai catatan reviewer di atas dan submit ulang.
                                </small>
                            @endif
                            
                            <!-- 
                            <div class="mt-2">
                                <div class="alert alert-warning alert-sm mb-0" id="editWarningAlert">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    <small><strong>Perhatian:</strong> Anda sedang dalam mode edit. Pastikan untuk menyimpan perubahan sebelum meninggalkan halaman.</small>
                                </div>
                            </div> Persistent Edit Warning -->
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul HKI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $submission->title) }}" 
                                   placeholder="Masukkan judul HKI Anda" required
                                   onchange="trackFormChanges()">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Gunakan judul yang jelas dan deskriptif</div>
                        </div>

                        <!-- Creation Type - Read Only -->
                        <div class="mb-3">
                        <label class="form-label">Jenis Pengajuan</label>
                        <div class="p-3 border rounded bg-light">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            <strong>{{ ucfirst(str_replace('_', ' ', $submission->creation_type)) }}</strong>
                            <br>
                            <small class="text-muted">Jenis pengajuan tidak dapat diubah setelah submission dibuat</small>
                        </div>
                    </div>


                        <!-- Tanggal Publikasi -->
                        <div class="mb-3">
                            <label for="first_publication_date" class="form-label">
                                Tanggal Pertama Kali Diumumkan/Digunakan/Dipublikasikan <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('first_publication_date') is-invalid @enderror" 
                                id="first_publication_date" name="first_publication_date" 
                                value="{{ old('first_publication_date', $submission->first_publication_date?->format('Y-m-d')) }}" 
                                required onchange="trackFormChanges()">
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
                                      placeholder="Jelaskan secara detail tentang HKI yang akan diajukan" 
                                      required oninput="trackFormChanges()">{{ old('description', $submission->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Maksimal 1000 karakter</div>
                        </div>

                        <!-- Current Documents -->
                        @if($submission->documents->count() > 0)
                        <div class="mb-4">
                            <h6><strong>Dokumen Saat Ini</strong></h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama File</th>
                                            <th>Jenis</th>
                                            <th>Ukuran</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($submission->documents as $document)
                                            <tr>
                                                <td class="text-break">{{ $document->file_name }}</td>
                                                <td>
                                                    @if($document->document_type === 'main_document')
                                                        <span class="badge bg-primary">Dokumen Utama</span>
                                                    @else
                                                        <span class="badge bg-secondary">Dokumen Pendukung</span>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($document->file_size / 1024, 2) }} KB</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('user.submissions.documents.download', $document) }}" 
                                                        class="btn btn-sm btn-outline-success" title="Download">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                        @if($document->document_type !== 'main_document')
                                                            {{-- ✅ REPLACE: Hapus form, gunakan button AJAX --}}
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger" 
                                                                    title="Hapus"
                                                                    onclick="deleteDocument({{ $document->id }}, '{{ $document->file_name }}')"
                                                                    data-document-id="{{ $document->id }}">
                                                                <i class="bi bi-trash"></i>
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
                        @endif

                        <!-- ✅ FIXED: Members Section with Address & Postal Code like create form -->
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
                                    
                                    <!-- Personal Data -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Nama Pencipta <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('members.'.$index.'.name') is-invalid @enderror" 
                                                   name="members[{{ $index }}][name]" 
                                                   value="{{ old('members.'.$index.'.name', $member->name) }}" 
                                                   placeholder="Nama lengkap sesuai KTP" required
                                                   onchange="trackFormChanges()">
                                            <input type="hidden" name="members[{{ $index }}][id]" value="{{ $member->id }}">
                                            @error('members.'.$index.'.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Nama sesuai identitas resmi</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('members.'.$index.'.email') is-invalid @enderror" 
                                                   name="members[{{ $index }}][email]" 
                                                   value="{{ old('members.'.$index.'.email', $member->email) }}" 
                                                   placeholder="email@example.com" required
                                                   onchange="trackFormChanges()">
                                            @error('members.'.$index.'.email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Email yang valid dan aktif</div>
                                        </div>
                                    </div>

                                    <!-- WhatsApp field -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                                            <input type="tel" class="form-control @error('members.'.$index.'.whatsapp') is-invalid @enderror" 
                                                   name="members[{{ $index }}][whatsapp]" 
                                                   value="{{ old('members.'.$index.'.whatsapp', $member->whatsapp) }}" 
                                                   placeholder="08xxxxxxxxxx" required pattern="[0-9]{10,13}"
                                                   title="Nomor WhatsApp harus 10-13 digit"
                                                   onchange="trackFormChanges()">
                                            @error('members.'.$index.'.whatsapp')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Nomor WhatsApp aktif untuk komunikasi</div>
                                        </div>
                                    </div>

                                    <!-- ✅ NEW: Address Section with proper data display -->
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                            <textarea class="form-control @error('members.'.$index.'.alamat') is-invalid @enderror" 
                                                      name="members[{{ $index }}][alamat]" rows="3" 
                                                      placeholder="Masukkan alamat lengkap (Jalan, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi)" 
                                                      required oninput="trackFormChanges()">{{ old('members.'.$index.'.alamat', $member->alamat) }}</textarea>
                                            @error('members.'.$index.'.alamat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Contoh: Jl. Ring Road Utara, Condong Catur, Depok, Sleman, Yogyakarta</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('members.'.$index.'.kode_pos') is-invalid @enderror" 
                                                   name="members[{{ $index }}][kode_pos]" 
                                                   value="{{ old('members.'.$index.'.kode_pos', $member->kode_pos) }}" 
                                                   placeholder="12345" maxlength="5" pattern="[0-9]{5}" required
                                                   onchange="trackFormChanges()">
                                            @error('members.'.$index.'.kode_pos')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">5 digit kode pos sesuai alamat</div>
                                        </div>
                                    </div>

                                    <!-- 
                                    @if($member->alamat || $member->kode_pos)
                                        <div class="alert alert-info alert-sm mb-3">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <strong><i class="bi bi-info-circle text-info me-1"></i>Alamat Saat Ini:</strong>
                                                    <br><small class="text-muted">{{ $member->alamat ?: 'Belum diisi' }}</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Kode Pos:</strong>
                                                    <br><small class="text-muted">{{ $member->kode_pos ?: 'Belum diisi' }}</small>
                                                </div>
                                            </div>
                                            <small class="text-info">
                                                <i class="bi bi-pencil-square me-1"></i>
                                                Edit alamat dan kode pos di form di atas jika perlu diubah.
                                            </small>
                                        </div>Current Address Display for Reference -->
                                    @endif

                                    <!-- KTP Section -->
                                    <div class="row g-3">
                                        <div class="col-lg-12">
                                            <label class="form-label">
                                                <i class="bi bi-credit-card-2-front me-1"></i>
                                                Scan Foto KTP <span class="text-danger">*</span>
                                            </label>

                                            <!-- Current KTP Status -->
                                            @if($member->ktp)
                                                <div class="current-ktp-info mb-2">
                                                    <div class="d-flex align-items-center justify-content-between bg-light p-2 rounded">
                                                        <div class="d-flex align-items-center">
                                                            <i class="bi bi-check-circle text-success me-2"></i>
                                                            <small class="text-success fw-bold">KTP sudah diupload</small>
                                                            <small class="text-muted ms-2">{{ $member->updated_at->format('d M Y H:i') }} WIB</small>
                                                        </div>
                                                        <div class="btn-group" role="group">
                                                            <a href="{{ route('user.submissions.member-ktp-preview', [$submission, $member]) }}" 
                                                               target="_blank" class="btn btn-sm btn-outline-info" title="Lihat KTP">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                                    onclick="toggleKtpUpload({{ $index }})"
                                                                    title="Ganti KTP">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="current-ktp-info mb-2">
                                                    <div class="alert alert-warning py-2">
                                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                                        <small><strong>KTP belum diupload - wajib upload untuk melengkapi data</strong></small>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <!-- KTP Upload Input -->
                                            <div class="ktp-upload-section" id="ktpUpload_{{ $index }}" 
                                                 style="display: {{ $member->ktp ? 'none' : 'block' }};">
                                                <input type="file" 
                                                       class="form-control @error('members.'.$index.'.ktp') is-invalid @enderror" 
                                                       name="members[{{ $index }}][ktp]" 
                                                       accept=".jpg,.jpeg" 
                                                       id="ktp_{{ $index }}"
                                                       {{ !$member->ktp ? 'required' : '' }}
                                                       onchange="validateKtpFile(this, {{ $index }})">
                                                @error('members.'.$index.'.ktp')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                
                                                <div class="form-text">
                                                    <i class="bi bi-info-circle text-primary me-1"></i>
                                                    Upload file KTP (JPG/JPEG, maksimal 2MB)
                                                    @if($member->ktp)
                                                        <br><small class="text-warning">
                                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                                            File baru akan mengganti KTP yang sudah ada
                                                        </small>
                                                    @endif
                                                </div>
                                                
                                                <!-- Preview untuk file baru -->
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
                                    
                                    <!-- 
                                    @if($submission->status === 'revision_needed' && $submission->review_notes)
                                        <div class="mt-3">
                                            <div class="alert alert-warning">
                                                <small>
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    <strong>Catatan Reviewer:</strong> Pastikan data anggota, alamat, dan KTP sudah sesuai dengan catatan revisi.
                                                </small>
                                            </div>
                                        </div>
                                    @endif -->
                                </div>
                            @endforeach
                            
                            
                        </div>
                        @endif

                        <!-- Dynamic Document Fields based on creation_type -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i>Update Dokumen
                            </h6>

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

                            @elseif(in_array($submission->creation_type, ['poster', 'fotografi', 'seni_gambar', 'karakter_animasi']))
                                <!-- Image Files -->
                                <div class="mb-3">
                                    <label for="image_files" class="form-label">
                                        {{ $submission->documents->where('document_type', 'supporting_document')->count() > 0 ? 'Ganti File Gambar (JPG/PNG)' : 'Upload File Gambar (JPG/PNG)' }}
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
                                    <div class="form-text">Upload 1 atau lebih file gambar. Maksimal 2MB per file.</div>
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
                                    <div class="form-text">Format: JPG, PNG. Minimal 1 file. Maksimal 2MB per file.</div>
                                </div>

                            @elseif($submission->creation_type === 'basis_data')
                                <!-- Documentation File -->
                                <div class="mb-3">
                                    <label for="documentation_file" class="form-label">
                                        {{ $submission->documents->where('document_type', 'main_document')->count() > 0 ? 'Ganti Dokumentasi Basis Data (Meta Data dan Data Set)' : 'Dokumentasi Basis Data (Meta Data dan Data Set)' }}
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

                        <!-- Submit Buttons with Confirmation -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                                    <a href="{{ route('user.submissions.show', $submission) }}" 
                                       class="btn btn-secondary" onclick="return confirmLeave()">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                    <div class="d-flex flex-column flex-md-row gap-2">
                                        @if($submission->status === 'draft')
                                            <button type="submit" name="save_as_draft" class="btn btn-outline-primary" onclick="clearUnsavedChanges()">
                                                <i class="bi bi-save"></i> Simpan Draft
                                            </button>
                                        @endif
                                        <!-- ✅ NEW: Submit with confirmation -->
                                        <button type="button" class="btn btn-success" onclick="showSubmitConfirmation()">
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
            <!-- Reviewer Notes Reminder Card jika ada -->
            @if($submission->review_notes && $submission->status === 'revision_needed')
            <div class="card shadow mb-4 border-warning reviewer-reminder-card">
                <div class="card-header py-3 bg-warning-subtle">
                    <h6 class="m-0 font-weight-bold text-warning-emphasis">
                        <i class="bi bi-exclamation-triangle me-2"></i>Reminder Revisi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="bi bi-clipboard-check fs-1 text-warning mb-3"></i>
                        <h6 class="text-warning-emphasis">Catatan Reviewer Tersedia</h6>
                        <p class="small text-muted mb-3">
                            Pastikan Anda membaca catatan reviewer di bagian atas halaman sebelum melakukan edit.
                        </p>
                        
                    </div>
                </div>
            </div>
            @endif

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
                            @elseif($submission->status === 'submitted')
                                <i class="bi bi-file-earmark-check fs-1 text-primary"></i>
                                <h5 class="mt-2">Submitted</h5>
                                <p class="text-muted">Menunggu ditinjau</p>
                            @elseif($submission->status === 'under_review')
                                <i class="bi bi-eye fs-1 text-info"></i>
                                <h5 class="mt-2">Under Review</h5>
                                <p class="text-muted">Sedang ditinjau reviewer</p>
                            @elseif($submission->status === 'revision_needed')
                                <i class="bi bi-arrow-clockwise fs-1 text-warning"></i>
                                <h5 class="mt-2">Revision Needed</h5>
                                <p class="text-muted">Perlu perbaikan</p>
                            @elseif($submission->status === 'approved')
                                <i class="bi bi-check-circle fs-1 text-success"></i>
                                <h5 class="mt-2">Approved</h5>
                                <p class="text-muted">Submission disetujui</p>
                            @elseif($submission->status === 'rejected')
                                <i class="bi bi-x-circle fs-1 text-danger"></i>
                                <h5 class="mt-2">Rejected</h5>
                                <p class="text-muted">Submission ditolak</p>
                            @endif
                        </div>
                        
                        @if($submission->submission_date)
                            <div class="small text-muted mt-2">
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

            <!-- Guidelines for Revision - Enhanced jika ada reviewer notes -->
            @if($submission->status === 'revision_needed')
            <div class="card shadow mb-4 border-warning">
                <div class="card-header py-3 bg-warning-subtle">
                    <h6 class="m-0 font-weight-bold text-warning-emphasis">
                        <i class="bi bi-exclamation-triangle me-2"></i>Panduan Revisi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <h6 class="text-warning-emphasis">Langkah-langkah Revisi:</h6>
                        <ol class="mb-3">
                            <li><strong>Baca catatan reviewer dengan teliti</strong></li>
                            <li>Lakukan perubahan sesuai saran</li>
                            <li>Update dokumen jika diperlukan</li>
                            <li>Periksa data anggota dan alamat</li>
                            <li>Submit ulang untuk review</li>
                        </ol>
                        
                        <div class="mb-3 p-3 border-start border-4 border-warning bg-light rounded">
                        <small>
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                            <strong>Catatan:</strong> Pastikan data anggota, alamat, dan KTP sudah sesuai dengan catatan revisi.
                        </small>
                    </div>

                    @if($submission->review_notes)
                    <div class="mb-3 p-3 border-start border-4 border-info bg-light rounded">
                        <small>
                            <i class="bi bi-lightbulb text-info me-1"></i>
                            <strong>Tips:</strong> Catatan reviewer di atas berisi panduan spesifik untuk submission Anda. 
                            Ikuti setiap poin dengan cermat sebelum submit ulang.
                        </small>
                    </div>
                    @endif

                    </div>
                </div>
            </div>
            @endif

            <!-- Help Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-headset me-2"></i>Butuh Bantuan?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-envelope text-primary me-2"></i>
                    <a href="mailto:lppm@amikomsolo.ac.id" class="text-decoration-none text-dark">
                        lppm@amikomsolo.ac.id
                    </a>
                </div>

                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-instagram text-primary me-2"></i>
                    <a href="https://www.instagram.com/lppm_amikomsolo" target="_blank" class="text-decoration-none text-dark">
                        @lppm_amikomsolo
                    </a>
                </div>

                <div class="d-flex align-items-center">
                    <i class="bi bi-whatsapp text-primary me-2"></i>
                    <a href="https://wa.me/6289504696000" target="_blank" class="text-decoration-none text-dark">
                        089504696000
                    </a>
                </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ NEW: Submit Confirmation Modal -->
    <div class="modal fade" id="submitConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Submit
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="bi bi-question-circle fs-1 text-warning mb-3"></i>
                        <h5 class="text-warning">Apakah Anda yakin berkas sudah benar?</h5>
                        <p class="text-muted">
                            Pastikan semua data dan dokumen sudah sesuai sebelum melakukan submit.
                            Setelah disubmit, data hanya dapat diubah jika reviewer meminta revisi.
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border-warning h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-file-earmark-check text-warning fs-2 mb-2"></i>
                                    <h6 class="text-warning">Dokumen</h6>
                                    <ul class="small text-start mb-0">
                                        <li>Format file sudah benar</li>
                                        <li>Ukuran file sesuai ketentuan</li>
                                        <li>Dokumen jelas dan lengkap</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-warning h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-people text-warning fs-2 mb-2"></i>
                                    <h6 class="text-warning">Data Anggota</h6>
                                    <ul class="small text-start mb-0">
                                        <li>Nama sesuai identitas resmi</li>
                                        <li>Alamat lengkap dan benar</li>
                                        <li>No. WhatsApp dan email aktif</li>
                                        <li>KTP sudah diupload semua</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="confirmSubmission" required>
                        <label class="form-check-label fw-bold" for="confirmSubmission">
                            Saya menyatakan bahwa semua data dan dokumen yang saya upload sudah benar dan sesuai
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left"></i> Periksa Kembali
                    </button>
                    <button type="button" class="btn btn-success" id="finalSubmitBtn" disabled>
                        <i class="bi bi-send"></i> Ya, Submit Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Unsaved Changes Warning Modal -->
    <div class="modal fade" id="unsavedChangesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>Peringatan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Anda memiliki perubahan yang belum disimpan. Apakah Anda yakin ingin meninggalkan halaman ini?</p>
                    <div class="alert alert-warning">
                        <small><i class="bi bi-info-circle me-1"></i> Semua perubahan yang belum disimpan akan hilang.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left"></i> Tetap di Halaman
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmLeaveBtn">
                        <i class="bi bi-box-arrow-right"></i> Tinggalkan Halaman
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviewer Notes Close Confirmation Modal -->
    <div class="modal fade" id="reviewerNotesCloseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menutup catatan reviewer?</p>
                    <div class="alert alert-warning">
                        <small>
                            <i class="bi bi-info-circle me-1"></i> 
                            Pastikan Anda sudah membaca dan memahami semua catatan reviewer sebelum menutup pesan ini.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmCloseReviewerNotesBtn">
                        <i class="bi bi-check"></i> Ya, Sudah Dibaca
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Variables for tracking form changes
let hasUnsavedChanges = false;
let originalFormData = {};
let leaveCallback = null;
let reviewerNotesVisible = true;

document.addEventListener('DOMContentLoaded', function() {
    // Store original form data
    storeOriginalFormData();
    
    // Persistent alerts management
    managePersistentAlerts();
    
    // Track form changes
    trackAllFormInputs();
    
    // Warn before leaving page
    setupPageLeaveWarning();
    
    // Character counters
    setupCharacterCounters();
    
    // Form validation
    setupFormValidation();

    // Initialize reviewer notes banner
    initializeReviewerNotesBanner();

    // ✅ NEW: Setup submit confirmation
    setupSubmitConfirmation();
});

// ✅ NEW: Setup submit confirmation modal
function setupSubmitConfirmation() {
    const confirmCheckbox = document.getElementById('confirmSubmission');
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');
    
    if (confirmCheckbox && finalSubmitBtn) {
        confirmCheckbox.addEventListener('change', function() {
            finalSubmitBtn.disabled = !this.checked;
        });

        finalSubmitBtn.addEventListener('click', function() {
            clearUnsavedChanges();
            const form = document.getElementById('submissionEditForm');
            form.submit();
            
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
            
            const modal = bootstrap.Modal.getInstance(document.getElementById('submitConfirmationModal'));
            modal.hide();
        });
    }
}

// ✅ NEW: Show submit confirmation modal
function showSubmitConfirmation() {
    const modal = new bootstrap.Modal(document.getElementById('submitConfirmationModal'));
    modal.show();
    
    const confirmCheckbox = document.getElementById('confirmSubmission');
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');
    
    if (confirmCheckbox) confirmCheckbox.checked = false;
    if (finalSubmitBtn) finalSubmitBtn.disabled = true;
}

// Initialize reviewer notes banner
function initializeReviewerNotesBanner() {
    const banner = document.getElementById('reviewerNotesBanner');
    if (banner) {
        // Add scroll event to show/hide toggle button
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const toggleBtn = document.getElementById('toggleReviewerNotesBtn');
            
            if (scrollTop > 200) {
                toggleBtn.style.opacity = '1';
            } else {
                toggleBtn.style.opacity = '0.7';
            }
        });
        
        // Make banner persistent but collapsible
        console.log('Reviewer notes banner initialized - always visible until manually hidden');
    }
}

// Toggle reviewer notes visibility
function toggleReviewerNotes() {
    const banner = document.getElementById('reviewerNotesBanner');
    const content = banner.querySelector('.reviewer-notes-content');
    const toggleBtn = document.getElementById('toggleReviewerNotesBtn');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (reviewerNotesVisible) {
        // Hide content but keep minimal banner
        content.style.display = 'none';
        banner.style.height = '60px';
        banner.style.background = 'linear-gradient(135deg, #ff8c00 0%, #ffc107 100%)';
        toggleIcon.className = 'bi bi-eye';
        toggleBtn.innerHTML = '<i class="bi bi-eye"></i>';
        reviewerNotesVisible = false;
        
        // Add reminder text
        const col = banner.querySelector('.col-md-10');
        col.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle text-white me-2"></i>
                <span class="text-white fw-bold">Catatan Reviewer Disembunyikan - Klik untuk melihat kembali</span>
            </div>
        `;
    } else {
        // Show full content
        location.reload(); // Simple way to restore full content
    }
}

// Scroll to reviewer notes
function scrollToReviewerNotes() {
    const banner = document.getElementById('reviewerNotesBanner');
    if (banner) {
        banner.scrollIntoView({ behavior: 'smooth', block: 'start' });
        
        // Flash effect
        banner.style.animation = 'none';
        setTimeout(() => {
            banner.style.animation = 'pulseWarning 2s ease-in-out 3';
        }, 100);
    }
}

// Store original form data for comparison
function storeOriginalFormData() {
    const form = document.getElementById('submissionEditForm');
    const formData = new FormData(form);
    originalFormData = {};
    
    for (let [key, value] of formData.entries()) {
        originalFormData[key] = value;
    }
    
    // Store textarea values separately
    form.querySelectorAll('textarea').forEach(textarea => {
        originalFormData[textarea.name] = textarea.value;
    });
    
    console.log('Original form data stored:', originalFormData);
}

// Track form changes
function trackFormChanges() {
    hasUnsavedChanges = true;
    updateUnsavedChangesIndicator();
}

// Track all form inputs
function trackAllFormInputs() {
    const form = document.getElementById('submissionEditForm');
    
    // Track text inputs, textareas, selects
    form.querySelectorAll('input, textarea, select').forEach(element => {
        if (element.type !== 'file' && element.type !== 'submit' && element.type !== 'hidden') {
            element.addEventListener('input', trackFormChanges);
            element.addEventListener('change', trackFormChanges);
        }
    });
    
    // Track file inputs separately
    form.querySelectorAll('input[type="file"]').forEach(element => {
        element.addEventListener('change', function() {
            if (this.files.length > 0) {
                trackFormChanges();
            }
        });
    });
}

// Update unsaved changes indicator
function updateUnsavedChangesIndicator() {
    const editWarning = document.getElementById('editWarningAlert');
    if (editWarning && hasUnsavedChanges) {
        editWarning.innerHTML = `
            <i class="bi bi-exclamation-triangle me-1 text-warning"></i>
            <small><strong>Perhatian:</strong> Anda memiliki perubahan yang belum disimpan! 
            <span class="text-danger">Pastikan untuk menyimpan sebelum meninggalkan halaman.</span></small>
        `;
        editWarning.classList.remove('alert-warning');
        editWarning.classList.add('alert-danger');
    }
}

// Manage persistent alerts
function managePersistentAlerts() {
    // Auto-hide success alerts after 10 seconds but keep error alerts
    const successAlert = document.getElementById('successAlert');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.5s';
            successAlert.style.opacity = '0.7';
        }, 10000);
    }
    
    // Keep error and validation alerts persistent
    const errorAlert = document.getElementById('errorAlert');
    const validationAlert = document.getElementById('validationAlert');
    
    // FIXED: Do NOT auto-hide reviewer notes alert
    const reviewerNotesAlert = document.getElementById('reviewerNotesAlert');
    
    // Make alerts stick to top when scrolling (exclude reviewer notes)
    [errorAlert, validationAlert].forEach(alert => {
        if (alert) {
            alert.style.position = 'sticky';
            alert.style.top = '10px';
            alert.style.zIndex = '1050';
        }
    });

    // FIXED: Make reviewer notes alert sticky but never auto-hide
    if (reviewerNotesAlert) {
        reviewerNotesAlert.style.position = 'sticky';
        reviewerNotesAlert.style.top = '10px';
        reviewerNotesAlert.style.zIndex = '1060'; // Higher z-index for importance
        // Do NOT set any timeout or opacity change for reviewer notes
        console.log('Reviewer notes alert is persistent and will not auto-hide');
    }
}

// Setup page leave warning
function setupPageLeaveWarning() {
    // Warn when leaving page
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?';
            return e.returnValue;
        }
    });
    
    // Handle modal confirmation
    document.getElementById('confirmLeaveBtn').addEventListener('click', function() {
        hasUnsavedChanges = false; // Clear flag
        const modal = bootstrap.Modal.getInstance(document.getElementById('unsavedChangesModal'));
        modal.hide();
        
        if (leaveCallback) {
            leaveCallback();
        }
    });
}

// Confirm leave function
function confirmLeave(callback) {
    if (hasUnsavedChanges) {
        leaveCallback = callback || function() {
            window.location.href = document.querySelector('a[onclick="return confirmLeave()"]').href;
        };
        
        const modal = new bootstrap.Modal(document.getElementById('unsavedChangesModal'));
        modal.show();
        return false;
    }
    return true;
}

// Clear unsaved changes flag when saving
function clearUnsavedChanges() {
    hasUnsavedChanges = false;
}

// Setup character counters
function setupCharacterCounters() {
    // Description counter
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

    // Alamat counter for each member
    document.querySelectorAll('textarea[name*="[alamat]"]').forEach(textarea => {
        const counter = document.createElement('div');
        counter.className = 'form-text text-end text-muted';
        textarea.parentNode.appendChild(counter);
        
        function updateAlamatCounter() {
            const length = textarea.value.length;
            counter.textContent = `${length}/500 karakter`;
            
            if (length > 450) {
                counter.className = 'form-text text-end text-warning';
            } else if (length > 500) {
                counter.className = 'form-text text-end text-danger';
            } else {
                counter.className = 'form-text text-end text-muted';
            }
        }
        
        textarea.addEventListener('input', updateAlamatCounter);
        updateAlamatCounter();
    });
}

// Setup form validation
function setupFormValidation() {
    // Kode pos validation for each member
    document.querySelectorAll('input[name*="[kode_pos]"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length > 5) {
                this.value = this.value.slice(0, 5);
            }
        });
    });

    // WhatsApp validation for each member
    document.querySelectorAll('input[name*="[whatsapp]"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            if (this.value.length > 13) {
                this.value = this.value.slice(0, 13);
            }
        });
    });

    // Form submission handling
    document.getElementById('submissionEditForm').addEventListener('submit', function(e) {
        const submitButton = e.submitter;
        if (submitButton) {
            // Clear unsaved changes flag
            clearUnsavedChanges();
            
            submitButton.disabled = true;
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
            
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }, 5000);
        }
    });
}

// KTP Management Functions
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
    
    if (fileInput) {
        fileInput.value = '';
    }
    
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
    window.open(url, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
}
</script>
@endpush

@push('styles')
<style>
/* Fixed Reviewer Notes Banner */
/* Fixed Reviewer Notes Banner - Compact Version */
.reviewer-notes-banner {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1070;
    background: linear-gradient(135deg, #ff8c00 0%, #ffc107 50%, #ff8c00 100%);
    border-bottom: 2px solid #e5962e;
    box-shadow: 0 2px 8px rgba(255, 140, 0, 0.3);
    padding: 8px 0; /* Reduced padding */
    animation: slideDownBanner 0.3s ease-out;
    min-height: 60px; /* Set minimum height */
}

.reviewer-notes-content h6 {
    margin-bottom: 8px; /* Reduced margin */
    font-weight: bold;
    font-size: 0.9rem; /* Smaller font */
    text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}

.reviewer-note-text {
    background: rgba(255, 255, 255, 0.95);
    border: 1px solid #fff; /* Thinner border */
    border-radius: 6px; /* Smaller radius */
    box-shadow: 0 1px 4px rgba(0,0,0,0.1); /* Smaller shadow */
}

.reviewer-note-text p {
    font-size: 0.85rem; /* Smaller text */
    line-height: 1.3; /* Tighter line height */
}

.reviewer-note-text small {
    font-size: 0.75rem; /* Even smaller for meta info */
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .reviewer-notes-banner {
        padding: 6px 0;
        min-height: 50px;
    }
    
    .reviewer-notes-content h6 {
        font-size: 0.8rem;
        margin-bottom: 6px;
    }
    
    .reviewer-note-text p {
        font-size: 0.8rem;
    }
    
    .reviewer-note-text small {
        font-size: 0.7rem;
    }
}

/* Sticky Alert Styles - adjust position */
.sticky-alert {
    position: fixed;
    top: 80px; /* Adjusted for smaller banner */
    left: 15px;
    right: 15px;
    z-index: 1065;
    margin-bottom: 1rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .sticky-alert {
        top: 70px; /* Even smaller for mobile */
    }
}

/* Pulse animation - reduced intensity */
@keyframes pulseWarning {
    0%, 100% {
        box-shadow: 0 2px 8px rgba(255, 140, 0, 0.3);
    }
    50% {
        box-shadow: 0 4px 12px rgba(255, 140, 0, 0.5);
        transform: translateY(-1px);
    }
}

@keyframes slideDownBanner {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Reviewer reminder card */
.reviewer-reminder-card {
    border-left: 6px solid #ffc107;
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.2);
}

.reviewer-reminder-card .card-header {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
}

/* Sticky Alert Styles - adjust z-index */
.sticky-alert {
    position: fixed;
    top: 120px; /* Below reviewer banner */
    left: 15px;
    right: 15px;
    z-index: 1065;
    margin-bottom: 1rem;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Enhanced alert styles */
.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

/* Form enhancements */
.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

/* Enhanced card styling */
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    background-color: rgba(13, 110, 253, 0.1);
    border-bottom: 1px solid rgba(13, 110, 253, 0.2);
    border-radius: 10px 10px 0 0 !important;
}

/* Warning emphasis colors */
.text-warning-emphasis {
    color: #cc6600 !important;
}

.bg-warning-subtle {
    background-color: #fff3cd !important;
}

.border-warning {
    border-color: #ffc107 !important;
}

/* Button enhancements */
.btn {
    border-radius: 6px;
    font-weight: 500;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Loading states */
.btn-loading {
    position: relative;
    color: transparent;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush


