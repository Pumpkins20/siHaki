@extends('layouts.user')

@section('title', 'Buat Submission Baru')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.submissions.index') }}" class="text-decoration-none">Submissions</a></li>
                    <li class="breadcrumb-item active">Buat Baru</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Buat Submission HKI Baru</h1>
                    <p class="text-muted mb-0">Lengkapi formulir di bawah untuk mengajukan HKI</p>
                </div>
                <div>
                    <a href="{{ route('user.submissions.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Form -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-file-earmark-plus me-2"></i>Informasi Pengajuan
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.submissions.store') }}" method="POST" enctype="multipart/form-data" id="submissionForm">
                        @csrf
                        
                        <!-- Basic Information Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-info-circle me-2"></i>Informasi Dasar
                            </h6>
                            
                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul HKI <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                    id="title" name="title" value="{{ old('title') }}" 
                                    placeholder="Masukkan judul HKI Anda" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Gunakan judul yang jelas dan deskriptif</div>
                            </div>

                            <!-- Jenis Ciptaan -->
                            <div class="mb-3">
                                <label for="creation_type" class="form-label">Jenis Pengajuan <span class="text-danger">*</span></label>
                                <select class="form-select @error('creation_type') is-invalid @enderror" 
                                        id="creation_type" name="creation_type" required onchange="updateFormFields()">
                                    <option value="">Pilih Jenis Pengajuan</option>
                                    <option value="program_komputer" {{ old('creation_type') == 'program_komputer' ? 'selected' : '' }}>Program Komputer</option>
                                    <option value="sinematografi" {{ old('creation_type') == 'sinematografi' ? 'selected' : '' }}>Sinematografi</option>
                                    <option value="buku" {{ old('creation_type') == 'buku' ? 'selected' : '' }}>Buku</option>
                                    <option value="poster" {{ old('creation_type') == 'poster' ? 'selected' : '' }}>Poster</option>
                                    <option value="fotografi" {{ old('creation_type') == 'fotografi' ? 'selected' : '' }}>Fotografi</option>
                                    <option value="seni_gambar" {{ old('creation_type') == 'seni_gambar' ? 'selected' : '' }}>Seni Gambar</option>
                                    <option value="karakter_animasi" {{ old('creation_type') == 'karakter_animasi' ? 'selected' : '' }}>Karakter Animasi</option>
                                    <option value="alat_peraga" {{ old('creation_type') == 'alat_peraga' ? 'selected' : '' }}>Alat Peraga</option>
                                    <option value="basis_data" {{ old('creation_type') == 'basis_data' ? 'selected' : '' }}>Basis Data</option>
                                </select>
                                @error('creation_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Pilih jenis ciptaan sesuai dengan karya yang akan didaftarkan</div>
                            </div>

                            <!-- ✅ NEW: Tanggal Pertama Kali Diumumkan/Digunakan/Dipublikasikan -->
                            <div class="mb-3">
                                <label for="first_publication_date" class="form-label">
                                    Tanggal Pertama Kali Diumumkan/Digunakan/Dipublikasikan <span class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control @error('first_publication_date') is-invalid @enderror" 
                                    id="first_publication_date" name="first_publication_date" 
                                    value="{{ old('first_publication_date') }}" required>
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
                                        id="description" name="description" rows="4" 
                                        placeholder="Jelaskan secara detail tentang HKI yang akan diajukan" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Maksimal 1000 karakter</div>
                            </div>
                        </div>

                        <!-- Member Information Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-people me-2"></i>Informasi Pencipta
                            </h6>
                            
                            <!-- Jumlah Anggota Pencipta -->
                            <div class="mb-3">
                                <label for="member_count" class="form-label">Jumlah Anggota Pencipta <span class="text-danger">*</span></label>
                                <select class="form-select @error('member_count') is-invalid @enderror" 
                                        id="member_count" name="member_count" required onchange="updateMemberFields()">
                                    <option value="">Pilih Jumlah Anggota</option>
                                    <option value="2" {{ old('member_count') == '2' ? 'selected' : '' }}>2 Orang</option>
                                    <option value="3" {{ old('member_count') == '3' ? 'selected' : '' }}>3 Orang</option>
                                    <option value="4" {{ old('member_count') == '4' ? 'selected' : '' }}>4 Orang</option>
                                    <option value="5" {{ old('member_count') == '5' ? 'selected' : '' }}>5 Orang</option>
                                    <option value="6" {{ old('member_count') == '6' ? 'selected' : '' }}>6 Orang</option>
                                </select>
                                @error('member_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Minimal 2 orang, maksimal 6 orang. 
                                    <strong>Catatan:</strong> Apabila jumlah pencipta lebih dari yang disediakan, silakan menghubungi LPPM.
                                </div>
                            </div>

                            <!-- Anggota Pencipta Fields -->
                            <div id="members-section" class="mb-4">
                                <!-- Fields akan muncul berdasarkan jumlah anggota yang dipilih -->
                            </div>
                        </div>

                        <!-- Document Section -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-file-earmark-arrow-up me-2"></i>Dokumen Pendukung
                            </h6>
                            
                            <!-- Dynamic Fields based on Creation Type -->
                            <div id="dynamic-fields">
                                <!-- Fields akan muncul berdasarkan jenis ciptaan yang dipilih -->
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('user.submissions.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="button" id="resetBtn" class="btn btn-outline-warning me-2" onclick="resetForm()">
                                    <i class="bi bi-arrow-clockwise"></i> Reset Form
                                </button>
                                {{-- ✅ UPDATED: Changed to button instead of submit --}}
                                <button type="button" class="btn btn-success" onclick="showConfirmationModal()">
                                    <i class="bi bi-send"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
            <!-- Guidelines -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-book me-2"></i>Panduan Pengajuan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small" id="guidelines-content">
                        <div class="text-center py-3">
                            <i class="bi bi-file-earmark-text fs-3 text-muted mb-2"></i>
                            <h6>Pilih Jenis Pengajuan</h6>
                            <p class="text-muted mb-0">Silakan pilih jenis pengajuan terlebih dahulu untuk melihat persyaratan dokumen yang diperlukan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panduan Anggota Pencipta -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-people me-2"></i>Panduan Anggota Pencipta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <h6 class="fw-bold">Persyaratan Anggota:</h6>
                        <ul class="mb-3">
                            <li>Minimal 2 orang pencipta</li>
                            <li>Maksimal 6 orang pencipta</li>
                            <li>Semua data harus diisi lengkap</li>
                            <li>Nomor WhatsApp aktif untuk komunikasi</li>
                            <li>Email yang valid</li>
                            <li>Alamat lengkap dan kode pos setiap anggota</li>
                            <li><strong>Scan foto KTP dalam format JPG (maksimal 2MB)</strong></li>
                        </ul>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                <strong>Info KTP:</strong> Pastikan foto KTP jelas, tidak buram, dan semua informasi dapat dibaca dengan baik.
                            </small>
                        </div>
                        
                        <div class="alert alert-warning">
                            <small>
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Privasi:</strong> File KTP hanya akan digunakan untuk verifikasi identitas dan tidak akan disebarluaskan.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-headset me-2"></i> Butuh Bantuan?
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
</div>

{{-- ✅ NEW: Confirmation Modal --}}
<div class="modal fade" id="submissionConfirmModal" tabindex="-1" aria-labelledby="submissionConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title text-warning-emphasis" id="submissionConfirmModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi Pengajuan HKI
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Pastikan semua data sudah benar sebelum submit!</strong>
                </div>
                
                <h6 class="fw-bold mb-3">Ringkasan Pengajuan:</h6>
                
                <!-- Summary will be populated by JavaScript -->
                <div id="submission-summary">
                    <!-- Content will be populated dynamically -->
                </div>
                
                <hr>
                
                <h6 class="fw-bold text-danger mb-3">⚠️ Penting untuk Dipahami:</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-warning h-100">
                            <div class="card-body">
                                <h6 class="card-title text-warning">
                                    <i class="bi bi-file-earmark-check me-2"></i>Dokumen
                                </h6>
                                <ul class="small mb-0">
                                    <li>Semua file sudah sesuai format</li>
                                    <li>Ukuran file tidak melebihi batas</li>
                                    <li>Dokumen jelas dan dapat dibaca</li>
                                    <li>File KTP semua anggota sudah diupload</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-info h-100">
                            <div class="card-body">
                                <h6 class="card-title text-info">
                                    <i class="bi bi-people me-2"></i>Data Anggota
                                </h6>
                                <ul class="small mb-0">
                                    <li>Nama sesuai identitas resmi</li>
                                    <li>Alamat lengkap dan benar</li>
                                    <li>No. WhatsApp dan email aktif</li>
                                    <li>Data semua anggota sudah lengkap</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-3">
                    <h6 class="alert-heading">
                        <i class="bi bi-clock me-2"></i>Setelah Submit:
                    </h6>
                    <ul class="mb-0 small">
                        <li><strong>Data tidak dapat diubah</strong> kecuali jika reviewer meminta revisi</li>
                        <li>Proses review memakan waktu <strong>7-14 hari kerja</strong></li>
                        <li>Anda akan mendapat notifikasi via email untuk setiap update status</li>
                        <li>Pastikan email dan WhatsApp Anda aktif untuk komunikasi</li>
                    </ul>
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
                    <i class="bi bi-send"></i> Ya, Submit Pengajuan
                </button>
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
    counter.className = 'form-text text-end mt-1';
    description.parentNode.appendChild(counter);
    
    function updateCounter() {
        const length = description.value.length;
        counter.textContent = `${length}/1000 karakter`;
        counter.className = `form-text text-end mt-1 ${length > 1000 ? 'text-danger' : 'text-muted'}`;
    }
    
    description.addEventListener('input', updateCounter);
    updateCounter();

    // ✅ NEW: Initialize form with old values if available
    initializeFormWithOldValues();

    // ✅ NEW: Date validation for first publication date
    const firstPublicationDate = document.getElementById('first_publication_date');
    
    // Set max date to today
    const today = new Date().toISOString().split('T')[0];
    firstPublicationDate.setAttribute('max', today);
    
    // Validate date input
    firstPublicationDate.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const currentDate = new Date();
        
        if (selectedDate > currentDate) {
            alert('Tanggal tidak boleh lebih dari hari ini');
            this.value = '';
            this.focus();
        }
    });

    // ✅ NEW: Confirmation checkbox handler
    const confirmCheckbox = document.getElementById('confirmSubmission');
    const finalSubmitBtn = document.getElementById('finalSubmitBtn');
    
    confirmCheckbox.addEventListener('change', function() {
        finalSubmitBtn.disabled = !this.checked;
    });
    
    // ✅ NEW: Final submit handler
    finalSubmitBtn.addEventListener('click', function() {
        if (confirmCheckbox.checked) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('submissionConfirmModal'));
            modal.hide();
            
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
            
            // Submit the form
            document.getElementById('submissionForm').submit();
        }
    });
});

// ✅ NEW: Show confirmation modal function
function showConfirmationModal() {
    // Validate form first
    const form = document.getElementById('submissionForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return false;
    }
    
    // Generate summary
    generateSubmissionSummary();
    
    // Reset checkbox and button
    document.getElementById('confirmSubmission').checked = false;
    document.getElementById('finalSubmitBtn').disabled = true;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('submissionConfirmModal'));
    modal.show();
}

// ✅ NEW: Generate submission summary
function generateSubmissionSummary() {
    const summaryDiv = document.getElementById('submission-summary');
    
    // Get form data
    const title = document.getElementById('title').value;
    const creationType = document.getElementById('creation_type');
    const creationTypeText = creationType.options[creationType.selectedIndex].text;
    const memberCount = document.getElementById('member_count').value;
    const description = document.getElementById('description').value;
    const firstPublicationDate = document.getElementById('first_publication_date').value;
    
    // Count uploaded files
    let uploadedFiles = 0;
    let uploadedKtpFiles = 0;
    
    // Count regular files
    document.querySelectorAll('input[type="file"]:not([name*="ktp"])').forEach(input => {
        if (input.files.length > 0) {
            uploadedFiles += input.files.length;
        }
    });
    
    // Count KTP files
    document.querySelectorAll('input[name*="ktp"]').forEach(input => {
        if (input.files.length > 0) {
            uploadedKtpFiles++;
        }
    });
    
    // Generate summary HTML
    const summaryHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title text-primary">📋 Informasi Dasar</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="40%"><strong>Judul:</strong></td>
                                <td>${title}</td>
                            </tr>
                            <tr>
                                <td><strong>Jenis:</strong></td>
                                <td>${creationTypeText}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Publikasi:</strong></td>
                                <td>${new Date(firstPublicationDate).toLocaleDateString('id-ID')}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title text-success">👥 Anggota & Dokumen</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td width="50%"><strong>Jumlah Anggota:</strong></td>
                                <td>${memberCount} orang</td>
                            </tr>
                            <tr>
                                <td><strong>File KTP:</strong></td>
                                <td>${uploadedKtpFiles}/${memberCount} file</td>
                            </tr>
                            <tr>
                                <td><strong>Dokumen:</strong></td>
                                <td>${uploadedFiles} file</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <h6 class="fw-bold">📝 Deskripsi:</h6>
            <div class="p-3 bg-light rounded border">
                <small>${description.substring(0, 200)}${description.length > 200 ? '...' : ''}</small>
            </div>
        </div>
        
        ${uploadedKtpFiles < memberCount ? 
            `<div class="alert alert-danger mt-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Peringatan:</strong> File KTP belum lengkap! Anda baru mengupload ${uploadedKtpFiles} dari ${memberCount} file KTP yang diperlukan.
            </div>` : 
            `<div class="alert alert-success mt-3">
                <i class="bi bi-check-circle me-2"></i>
                <strong>Bagus!</strong> Semua file KTP sudah terupload (${uploadedKtpFiles}/${memberCount}).
            </div>`
        }
    `;
    
    summaryDiv.innerHTML = summaryHTML;
}

// ✅ NEW: Function to initialize form with old values
function initializeFormWithOldValues() {
    // Initialize member count if there's old input
    const memberCount = document.getElementById('member_count').value;
    if (memberCount) {
        updateMemberFields();
    }

    // Initialize creation type if there's old input
    const creationType = document.getElementById('creation_type').value;
    if (creationType) {
        updateFormFields();
    }

    // Restore member data from old input if available
    @if(old('members'))
        const oldMembers = @json(old('members'));
        if (oldMembers && Object.keys(oldMembers).length > 0) {
            console.log('Restoring old member data:', oldMembers);
            restoreMemberData(oldMembers);
        }
    @endif
}

// ✅ IMPROVED: Better function to restore member data
function restoreMemberData(oldMembers) {
    // This function will be called after the member fields are created
    setTimeout(() => {
        Object.keys(oldMembers).forEach(index => {
            const member = oldMembers[index];
            
            // Restore text inputs
            if (member.name) {
                const nameInput = document.querySelector(`input[name="members[${index}][name]"]`);
                if (nameInput) nameInput.value = member.name;
            }
            
            if (member.whatsapp) {
                const whatsappInput = document.querySelector(`input[name="members[${index}][whatsapp]"]`);
                if (whatsappInput) whatsappInput.value = member.whatsapp;
            }
            
            if (member.email) {
                const emailInput = document.querySelector(`input[name="members[${index}][email]"]`);
                if (emailInput) emailInput.value = member.email;
            }

            if (member.alamat) {
                const alamatInput = document.querySelector(`textarea[name="members[${index}][alamat]"]`);
                if (alamatInput) alamatInput.value = member.alamat;
            }

            if (member.kode_pos) {
                const kodePosInput = document.querySelector(`input[name="members[${index}][kode_pos]"]`);
                if (kodePosInput) kodePosInput.value = member.kode_pos;
            }
            
            // Note: File inputs cannot be restored for security reasons
            // Show notice that file needs to be re-uploaded
            if (member.ktp) {
                const ktpInput = document.querySelector(`input[name="members[${index}][ktp]"]`);
                if (ktpInput && !ktpInput.parentNode.querySelector('.text-warning')) {
                    const notice = document.createElement('small');
                    notice.className = 'text-warning d-block mt-1';
                    notice.innerHTML = '<i class="bi bi-exclamation-triangle"></i> File KTP perlu diupload ulang';
                    ktpInput.parentNode.appendChild(notice);
                }
            }
        });
        
        // Apply validation errors after restoring data
        showValidationErrors();
    }, 150);
}

// ✅ IMPROVED: Better function to show validation errors
function showValidationErrors() {
    // Get error data from server-side
    @if($errors->any())
        const errors = @json($errors->messages());
        
        // Apply error classes to fields with errors
        Object.keys(errors).forEach(fieldName => {
            // Convert Laravel error key format to HTML name attribute
            const inputName = fieldName.replace(/\./g, '][').replace(/^/, '').replace(/]$/, '');
            const fieldSelector = `input[name="${inputName}"], select[name="${inputName}"], textarea[name="${inputName}"]`;
            
            // For member fields, we need different approach since they're dynamically created
            if (fieldName.includes('members.')) {
                // Extract member index and field name
                const matches = fieldName.match(/members\.(\d+)\.(\w+)/);
                if (matches) {
                    const memberIndex = matches[1];
                    const fieldType = matches[2];
                    
                    setTimeout(() => {
                        const input = document.querySelector(`input[name="members[${memberIndex}][${fieldType}]"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            
                            // Add error message if not already exists
                            if (!input.nextElementSibling || !input.nextElementSibling.classList.contains('invalid-feedback')) {
                                const errorDiv = document.createElement('div');
                                errorDiv.className = 'invalid-feedback';
                                errorDiv.textContent = errors[fieldName][0];
                                input.parentNode.insertBefore(errorDiv, input.nextSibling);
                            }
                        }
                    }, 100);
                }
            } else {
                // For regular fields
                const field = document.querySelector(fieldSelector);
                if (field) {
                    field.classList.add('is-invalid');
                }
            }
        });
    @endif
}

    function resetForm() {
        if (confirm('Apakah Anda yakin ingin mereset form? Semua data yang telah diisi akan hilang.')) {
            document.getElementById('submissionForm').reset();
            
            // Clear dynamic sections
            document.getElementById('members-section').innerHTML = '';
            document.getElementById('dynamic-fields').innerHTML = '';
            
            // Reset guidelines content
            document.getElementById('guidelines-content').innerHTML = `
                <div class="text-center py-3">
                    <i class="bi bi-file-earmark-text fs-3 text-muted mb-2"></i>
                    <h6>Pilih Jenis Pengajuan</h6>
                    <p class="text-muted mb-0">Silakan pilih jenis pengajuan terlebih dahulu untuk melihat persyaratan dokumen yang diperlukan.</p>
                </div>
            `;
            
            // Reset character counter
            const description = document.getElementById('description');
            const counter = description.parentNode.querySelector('.form-text.text-end');
            if (counter) {
                counter.textContent = '0/1000 karakter';
                counter.className = 'form-text text-end mt-1 text-muted';
            }
            
            // Remove validation classes
            document.querySelectorAll('.is-invalid').forEach(element => {
                element.classList.remove('is-invalid');
            });
            
            // Reset button animation
            const resetBtn = document.getElementById('resetBtn');
            resetBtn.innerHTML = '<i class="bi bi-check-circle"></i> Form Direset';
            resetBtn.classList.remove('btn-outline-warning');
            resetBtn.classList.add('btn-outline-success');
            
            setTimeout(() => {
                resetBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Reset Form';
                resetBtn.classList.remove('btn-outline-success');
                resetBtn.classList.add('btn-outline-warning');
            }, 2000);
            
            document.getElementById('submissionForm').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
    }

function updateMemberFields() {
    const memberCount = parseInt(document.getElementById('member_count').value);
    const membersSection = document.getElementById('members-section');
    
    if (!memberCount || memberCount < 2 || memberCount > 6) {
        membersSection.innerHTML = '';
        return;
    }

    let html = `
        <div class="card border-success">
            <div class="card-header bg-success bg-gradient text-white">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-people me-2"></i>Data Anggota Pencipta (${memberCount} Orang)
                </h6>
            </div>
            <div class="card-body">
    `;

    // ✅ FIXED: Remove the problematic const declaration
    for (let i = 1; i <= memberCount; i++) {
        // ✅ FIXED: Get old values using different approach to avoid Blade issues in JS
        let oldName = '';
        let oldWhatsapp = '';
        let oldEmail = '';
        let oldAlamat = '';
        let oldKodePos = '';
        let oldKtp = '';
        
        // Check if old values exist from server-side
        @if(old('members'))
            const oldMembersData = @json(old('members'));
            if (oldMembersData && oldMembersData[i]) {
                oldName = oldMembersData[i].name || '';
                oldWhatsapp = oldMembersData[i].whatsapp || '';
                oldEmail = oldMembersData[i].email || '';
                oldAlamat = oldMembersData[i].alamat || '';
                oldKodePos = oldMembersData[i].kode_pos || '';
                oldKtp = oldMembersData[i].ktp ? true : false;
            }
        @endif

        html += `
            <div class="member-section ${i !== memberCount ? 'border-bottom pb-4 mb-4' : ''}">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-circle text-success fs-5 me-2"></i>
                    <h6 class="mb-0 fw-bold">Anggota Pencipta ${i}</h6>
                    ${i === 1 ? '<span class="badge bg-success ms-2">Pencipta Utama</span>' : ''}
                </div>
                
                <!-- Data Pribadi -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="member_${i}_name" class="form-label">Nama Pencipta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" 
                               id="member_${i}_name" name="members[${i}][name]" value="${oldName}" 
                               placeholder="Masukkan nama lengkap" required>
                        <div class="form-text">Nama sesuai identitas resmi</div>
                    </div>
                    <div class="col-md-6">
                        <label for="member_${i}_whatsapp" class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" 
                               id="member_${i}_whatsapp" name="members[${i}][whatsapp]" value="${oldWhatsapp}" 
                               placeholder="08xxxxxxxxxx" required pattern="[0-9]{10,13}" 
                               title="Nomor WhatsApp harus 10-13 digit">
                        <div class="form-text">Nomor WhatsApp aktif untuk komunikasi</div>
                    </div>
                    <div class="col-md-6">
                        <label for="member_${i}_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" 
                               id="member_${i}_email" name="members[${i}][email]" value="${oldEmail}" 
                               placeholder="email@example.com" required>
                        <div class="form-text">Email yang valid dan aktif</div>
                    </div>
                    <div class="col-md-6">
                        <label for="member_${i}_ktp" class="form-label">Scan Foto KTP <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" 
                               id="member_${i}_ktp" name="members[${i}][ktp]" accept=".jpg,.jpeg" required>
                        <div class="form-text">Upload scan KTP dalam format JPG. Maksimal 2MB. Pastikan foto jelas dan dapat dibaca.</div>
                        ${oldKtp ? '<small class="text-warning d-block mt-1"><i class="bi bi-exclamation-triangle"></i> File KTP perlu diupload ulang</small>' : ''}
                    </div>
                </div>

                <!-- Alamat -->
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="member_${i}_alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea class="form-control" 
                                  id="member_${i}_alamat" name="members[${i}][alamat]" rows="3" 
                                  placeholder="Masukkan alamat lengkap (Jalan, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi)" required>${oldAlamat}</textarea>
                        <div class="form-text">Contoh: Jl. Ring Road Utara, Condong Catur, Depok, Sleman, Yogyakarta</div>
                    </div>
                    <div class="col-md-4">
                        <label for="member_${i}_kode_pos" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" 
                               id="member_${i}_kode_pos" name="members[${i}][kode_pos]" value="${oldKodePos}" 
                               placeholder="12345" maxlength="5" pattern="[0-9]{5}" required>
                        <div class="form-text">5 digit kode pos sesuai alamat</div>
                    </div>
                </div>
            </div>
        `;
    }

    html += `
            </div>
        </div>
    `;

    membersSection.innerHTML = html;

    // Add validation for all member fields
    addMemberValidation();
    
    // ✅ NEW: Show error messages if there are validation errors
    showValidationErrors();
}

function addMemberValidation() {
    // WhatsApp validation - only numbers
    document.querySelectorAll('input[name*="[whatsapp]"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });

    // Kode Pos validation - only numbers, max 5 digits
    document.querySelectorAll('input[name*="[kode_pos]"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 5) {
                this.value = this.value.slice(0, 5);
            }
        });
    });

    // Email validation (real-time)
    document.querySelectorAll('input[name*="[email]"]').forEach(input => {
        input.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.setCustomValidity('Format email tidak valid');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });
    });

    // Alamat character counter
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

    // KTP file validation
    document.querySelectorAll('input[name*="[ktp]"]').forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file KTP terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file KTP harus JPG atau JPEG.');
                    this.value = '';
                    return;
                }
            }
        });
    });
}

function updateFormFields() {
    const creationType = document.getElementById('creation_type').value;
    const dynamicFields = document.getElementById('dynamic-fields');
    const guidelinesContent = document.getElementById('guidelines-content');
    
    // Clear existing fields
    dynamicFields.innerHTML = '';
    
    if (!creationType) {
        guidelinesContent.innerHTML = `
            <div class="text-center py-3">
                <i class="bi bi-file-earmark-text fs-3 text-muted mb-2"></i>
                <h6>Pilih Jenis Pengajuan</h6>
                <p class="text-muted mb-0">Silakan pilih jenis pengajuan terlebih dahulu untuk melihat persyaratan dokumen yang diperlukan.</p>
            </div>
        `;
        return;
    }
    
    switch(creationType) {
        case 'program_komputer':
            dynamicFields.innerHTML = `
                <!-- Manual Penggunaan -->
                <div class="mb-3">
                    <label for="manual_document" class="form-label">Manual Penggunaan Program (PDF) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('manual_document') is-invalid @enderror" 
                           id="manual_document" name="manual_document" accept=".pdf" required>
                    @error('manual_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Format: PDF. Cover, screenshot program, dan manual petunjuk penggunaan program dalam 1 file PDF. Maksimal 20MB.</div>
                </div>

                <!-- Link Program -->
                <div class="mb-3">
                    <label for="program_link" class="form-label">Link Program <span class="text-danger">*</span></label>
                    <input type="url" class="form-control @error('program_link') is-invalid @enderror" 
                           id="program_link" name="program_link" value="{{ old('program_link') }}" 
                           placeholder="https://github.com/username/repository" required>
                    @error('program_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Link Google Drive, GitHub, GitLab, atau repositori lainnya</div>
                </div>
            `;
            
            guidelinesContent.innerHTML = `
                <h6 class="fw-bold">Persyaratan Program Komputer:</h6>
                <ul class="mb-3">
                    <li>File PDF berisi: Cover, Screenshot Program dan Penjelasannya, Manual Penggunaan</li>
                    <li>Ukuran File PDF Maks 20MB</li>
                    <li>Link akses program (GDrive/GitHub/GitLab/dll)</li>
                </ul>
            `;
            break;

        case 'sinematografi':
            dynamicFields.innerHTML = `
                <!-- Video Link -->
                <div class="mb-3">
                    <label for="video_link" class="form-label">Link Video <span class="text-danger">*</span></label>
                    <input type="url" class="form-control @error('video_link') is-invalid @enderror" 
                           id="video_link" name="video_link" value="{{ old('video_link') }}" 
                           placeholder="https://drive.google.com/..." required>
                    @error('video_link')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Link Google Drive, YouTube, atau platform video lainnya. Pastikan file dapat diakses publik atau sudah dibagikan dengan admin.</div>
                </div>
            `;

            guidelinesContent.innerHTML = `
                <h6 class="fw-bold">Persyaratan Sinematografi:</h6>
                <ul class="mb-3">
                    <li>Link video yang dapat diakses (Google Drive/YouTube)</li>
                    <li> Pastikan file dapat diakses publik atau sudah dibagikan dengan admin.</li>
                </ul>
                <div class="alert alert-warning">
                    <small><i class="bi bi-exclamation-triangle"></i> <strong>Penting:</strong> Pastikan link video dapat diakses oleh admin untuk proses review.</small>
                </div>
            `;
            break;
        
        case 'buku':
            dynamicFields.innerHTML = `
                <!-- E-book File -->
                <div class="mb-3">
                    <label for="ebook_file" class="form-label">Upload File Buku (PDF) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('ebook_file') is-invalid @enderror" 
                           id="ebook_file" name="ebook_file" accept=".pdf" required>
                    @error('ebook_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Format: PDF. File lengkap buku yang akan didaftarkan. Maksimal 20MB.</div>
                </div>
            `;
            
            guidelinesContent.innerHTML = `
                <h6 class="fw-bold">Persyaratan Buku:</h6>
                <ul class="mb-3">
                    <li>File PDF lengkap buku</li>
                    <li>File PDF Maks. 20 MB</li>
                    <li>Kualitas file PDF yang baik dan dapat dibaca</li>
                </ul>
                <div class="alert alert-info">
                    <small><i class="bi bi-info-circle"></i> <strong>Tips:</strong> Pastikan file PDF tidak terlalu besar dan dapat dibuka dengan baik.</small>
                </div>
            `;
            break;

        case 'poster':
        case 'fotografi':
        case 'seni_gambar':
        case 'karakter_animasi':
            dynamicFields.innerHTML = `
                <!-- Image Files -->
                <div class="mb-3">
                    <label for="image_files" class="form-label">File Gambar (JPG/PNG) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('image_files.*') is-invalid @enderror" 
                           id="image_files" name="image_files[]" accept=".jpg,.jpeg,.png" multiple required>
                    @error('image_files.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Format: JPG, PNG. Minimal 1 file. Maksimal 2MB per file.</div>
                </div>
            `;

            const visualTypeNames = {
                'poster': 'Poster',
                'fotografi': 'Fotografi', 
                'seni_gambar': 'Seni Gambar',
                'karakter_animasi': 'Karakter Animasi'
            };

            guidelinesContent.innerHTML = `
                <h6 class="fw-bold">Persyaratan ${visualTypeNames[creationType]}:</h6>
                <ul class="mb-3">
                    <li>File gambar berkualitas tinggi (JPG/PNG)</li>
                    <li>Ukuran file maksimal 2MB per file</li>
                </ul>
       
            `;
            break;

        case 'alat_peraga':
            dynamicFields.innerHTML = `
                <!-- Photo of Teaching Aid -->
                <div class="mb-3">
                    <label for="photo_files" class="form-label">Foto Alat Peraga <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('photo_files.*') is-invalid @enderror" 
                           id="photo_files" name="photo_files[]" accept=".jpg,.jpeg,.png" multiple required>
                    @error('photo_files.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Format: JPG, PNG. Maksimal 1MB per file.</div>
                </div>
            `;

            guidelinesContent.innerHTML = `
                <h6 class="fw-bold">Persyaratan Alat Peraga:</h6>
                <ul class="mb-3">
                    <li>Resolusi yang cukup untuk melihat detail</li>
                    <li>Ukuran file maksimal 2MB </li>
                </ul>
            `;
            break;

        case 'basis_data':
            dynamicFields.innerHTML = `
                <!-- Documentation File -->
                <div class="mb-3">
                    <label for="documentation_file" class="form-label">Upload File PDF (Meta Set dan Data Set) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control @error('documentation_file') is-invalid @enderror" 
                        id="documentation_file" name="documentation_file" accept=".pdf,.docx" required>
                    @error('documentation_file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Format: PDF/DOCX. Maksimal 20MB.</div>
                </div>
            `;

            guidelinesContent.innerHTML = `
                <h6 class="fw-bold">Persyaratan Basis Data:</h6>
                <ul class="mb-3">
                    <li>Dokumentasi lengkap Meta Data dan Data Set</li>
                    <li>File PDF/DOCX Maksimal 20MB</li>
                </ul>
            `;
            break;

        default:
            guidelinesContent.innerHTML = `
                <div class="text-center py-3">
                    <i class="bi bi-file-earmark-text fs-3 text-muted mb-2"></i>
                    <h6>Pilih Jenis Pengajuan</h6>
                    <p class="text-muted mb-0">Silakan pilih jenis pengajuan terlebih dahulu untuk melihat persyaratan dokumen yang diperlukan.</p>
                </div>
            `;
    }
    
    // Add file validation after creating fields
    addFileValidation();
}

function addFileValidation() {
    const fileInputs = document.querySelectorAll('input[type="file"]:not([name*="[ktp]"])');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const maxSize = input.accept.includes('video') || input.id === 'metadata_file' || input.id === 'manual_document' || input.id === 'ebook_file' ? 20 * 1024 * 1024 : // 20MB
                            input.accept.includes('image') || input.id.includes('photo') || input.id.includes('image') ? 2 * 1024 * 1024 : // 2MB
                            5 * 1024 * 1024; // 5MB default
            
            if (this.files.length > 0) {
                for (let file of this.files) {
                    if (file.size > maxSize) {
                        const sizeMB = maxSize / (1024 * 1024);
                        alert(`Ukuran file "${file.name}" terlalu besar. Maksimal ${sizeMB}MB.`);
                        this.value = '';
                        break;
                    }
                }
            }
        });
    });
}
</script>
@endpush

@push('styles')
<style>
.member-section {
    background-color: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
    padding: 1rem;
}

.member-section:hover {
    background-color: #e9ecef;
}

.form-control:focus,
.form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

.border-success {
    border-color: #28a745 !important;
}

.bg-success.bg-gradient {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
}

.text-decoration-none:hover {
    text-decoration: underline !important;
}

/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.bi-hourglass-split {
    animation: spin 1s linear infinite;
}

/* Enhanced member section */
.member-section {
    border: 1px solid #e9ecef;
    margin-bottom: 1rem;
}

.member-section .row {
    margin-bottom: 0;
}

.member-section .form-text {
    font-size: 0.8rem;
}

/* Better spacing for member cards */
.card-body > .member-section:last-child {
    margin-bottom: 0;
    border-bottom: none;
    padding-bottom: 1rem;
}

/* ✅ NEW: Modal Styles */
.modal-lg {
    max-width: 900px;
}

.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.text-warning-emphasis {
    color: #664d03 !important;
}

.card.border-warning {
    border-color: #ffc107 !important;
}

.card.border-info {
    border-color: #0dcaf0 !important;
}

/* Smooth transitions for modal */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
}

/* Better spacing in modal */
.modal-body .row {
    margin-bottom: 1rem;
}

.modal-body .alert:last-child {
    margin-bottom: 0;
}

/* Form check styling */
.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-check-label {
    cursor: pointer;
}

/* Animation for confirmation */
@keyframes confirmPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.modal-content {
    animation: confirmPulse 0.6s ease-in-out;
}

/* Better table styling in modal */
.table-sm td {
    padding: 0.25rem 0.5rem;
    vertical-align: top;
}

.table-borderless td {
    border: none;
}

/* Responsive adjustments for modal */
@media (max-width: 767.98px) {
    .modal-lg {
        max-width: 95%;
        margin: 0.5rem;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .table-sm {
        font-size: 0.8rem;
    }
}
</style>
@endpush
@endsection