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
                        <i class="bi bi-file-earmark-plus me-2"></i>Informasi Submission
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
                                <label for="creation_type" class="form-label">Jenis Ciptaan <span class="text-danger">*</span></label>
                                <select class="form-select @error('creation_type') is-invalid @enderror" 
                                        id="creation_type" name="creation_type" required onchange="updateFormFields()">
                                    <option value="">Pilih Jenis Ciptaan</option>
                                    <option value="program_komputer" {{ old('creation_type') == 'program_komputer' ? 'selected' : '' }}>Program Komputer</option>
                                    <option value="sinematografi" {{ old('creation_type') == 'sinematografi' ? 'selected' : '' }}>Sinematografi</option>
                                    <option value="buku" {{ old('creation_type') == 'buku' ? 'selected' : '' }}>Buku</option>
                                    <option value="poster_fotografi" {{ old('creation_type') == 'poster_fotografi' ? 'selected' : '' }}>Poster / Fotografi / Seni Gambar / Karakter Animasi</option>
                                    <option value="alat_peraga" {{ old('creation_type') == 'alat_peraga' ? 'selected' : '' }}>Alat Peraga</option>
                                    <option value="basis_data" {{ old('creation_type') == 'basis_data' ? 'selected' : '' }}>Basis Data</option>
                                </select>
                                @error('creation_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                <div class="form-text">Minimal 50 karakter, maksimal 1000 karakter</div>
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
                                </select>
                                @error('member_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Minimal 2 orang, maksimal 5 orang. 
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
                                <button type="submit" name="save_as_draft" class="btn btn-outline-primary me-2">
                                    <i class="bi bi-save"></i> Simpan Draft
                                </button>
                                <button type="submit" class="btn btn-success">
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
                        <i class="bi bi-book me-2"></i>Panduan Submission
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small" id="guidelines-content">
                        <div class="text-center py-3">
                            <i class="bi bi-file-earmark-text fs-3 text-muted mb-2"></i>
                            <h6>Pilih Jenis Ciptaan</h6>
                            <p class="text-muted mb-0">Silakan pilih jenis ciptaan terlebih dahulu untuk melihat persyaratan dokumen yang diperlukan.</p>
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
                            <li>Maksimal 5 orang pencipta</li>
                            <li>Semua data harus diisi lengkap</li>
                            <li>Nomor WhatsApp aktif untuk komunikasi</li>
                            <li>Email yang valid</li>
                            <li>Nomor KTP sesuai identitas</li>
                        </ul>
                        
                        <div class="alert alert-info">
                            <small>
                                <i class="bi bi-info-circle"></i>
                                <strong>Info:</strong> Jika membutuhkan lebih dari 5 anggota pencipta, hubungi LPPM di hki@amikom.ac.id
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-headset me-2"></i>Butuh Bantuan?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-envelope text-primary me-2"></i>
                            <span>hki@amikom.ac.id</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-telephone text-primary me-2"></i>
                            <span>(0271) 7851507</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock text-primary me-2"></i>
                            <span>Senin-Jumat: 08:00-16:00</span>
                        </div>
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
    counter.className = 'form-text text-end mt-1';
    description.parentNode.appendChild(counter);
    
    function updateCounter() {
        const length = description.value.length;
        counter.textContent = `${length}/1000 karakter`;
        counter.className = `form-text text-end mt-1 ${length > 1000 ? 'text-danger' : 'text-muted'}`;
    }
    
    description.addEventListener('input', updateCounter);
    updateCounter();

    // Form submission handling
    document.getElementById('submissionForm').addEventListener('submit', function(e) {
        const submitButton = e.submitter;
        if (submitButton) {
            submitButton.disabled = true;
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
            
            // Re-enable after 10 seconds if still processing
            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }, 10000);
        }
    });

    // Initialize if there's old input
    const memberCount = document.getElementById('member_count').value;
    if (memberCount) {
        updateMemberFields();
    }

    const creationType = document.getElementById('creation_type').value;
    if (creationType) {
        updateFormFields();
    }
});

function updateMemberFields() {
    const memberCount = parseInt(document.getElementById('member_count').value);
    const membersSection = document.getElementById('members-section');
    
    if (!memberCount || memberCount < 2 || memberCount > 5) {
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

    for (let i = 1; i <= memberCount; i++) {
        const oldName = document.querySelector(`input[name="members[${i}][name]"]`)?.value || '';
        const oldWhatsapp = document.querySelector(`input[name="members[${i}][whatsapp]"]`)?.value || '';
        const oldEmail = document.querySelector(`input[name="members[${i}][email]"]`)?.value || '';
        const oldKtp = document.querySelector(`input[name="members[${i}][ktp]"]`)?.value || '';

        html += `
            <div class="member-section ${i !== memberCount ? 'border-bottom pb-4 mb-4' : ''}">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-person-circle text-success fs-5 me-2"></i>
                    <h6 class="mb-0 fw-bold">Anggota Pencipta ${i}</h6>
                    ${i === 1 ? '<span class="badge bg-success ms-2">Ketua</span>' : ''}
                </div>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="member_${i}_name" class="form-label">Nama Pencipta <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="member_${i}_name" 
                               name="members[${i}][name]" value="${oldName}" 
                               placeholder="Masukkan nama lengkap" required>
                        <div class="form-text">Nama sesuai identitas resmi</div>
                    </div>
                    <div class="col-md-6">
                        <label for="member_${i}_whatsapp" class="form-label">No. WhatsApp <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="member_${i}_whatsapp" 
                               name="members[${i}][whatsapp]" value="${oldWhatsapp}" 
                               placeholder="08xxxxxxxxxx" required 
                               pattern="[0-9]{10,13}" 
                               title="Nomor WhatsApp harus 10-13 digit">
                        <div class="form-text">Nomor WhatsApp aktif untuk komunikasi</div>
                    </div>
                    <div class="col-md-6">
                        <label for="member_${i}_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="member_${i}_email" 
                               name="members[${i}][email]" value="${oldEmail}" 
                               placeholder="email@example.com" required>
                        <div class="form-text">Email yang valid dan aktif</div>
                    </div>
                    <div class="col-md-6">
                        <label for="member_${i}_ktp" class="form-label">No. KTP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="member_${i}_ktp" 
                               name="members[${i}][ktp]" value="${oldKtp}" 
                               placeholder="16 digit nomor KTP" required 
                               pattern="[0-9]{16}" 
                               maxlength="16"
                               title="Nomor KTP harus 16 digit">
                        <div class="form-text">Nomor KTP sesuai identitas</div>
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

    // Add validation for KTP and WhatsApp
    addMemberValidation();
}

function addMemberValidation() {
    // KTP validation - only numbers and 16 digits
    document.querySelectorAll('input[name*="[ktp]"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').substring(0, 16);
        });
    });

    // WhatsApp validation - only numbers
    document.querySelectorAll('input[name*="[whatsapp]"]').forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
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
                <h6>Pilih Jenis Ciptaan</h6>
                <p class="text-muted mb-0">Silakan pilih jenis ciptaan terlebih dahulu untuk melihat persyaratan dokumen yang diperlukan.</p>
            </div>
        `;
        return;
    }
    
    switch(creationType) {
        case 'program_komputer':
            dynamicFields.innerHTML = `
                <!-- Cover Document -->
                <div class="mb-3">
                    <label for="cover_document" class="form-label">Cover <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="cover_document" name="cover_document" 
                           accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="form-text">Format: PDF, JPG, PNG. Maksimal 5MB.</div>
                </div>

                <!-- Screenshot -->
                <div class="mb-3">
                    <label for="screenshot_document" class="form-label">Screenshot Program <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="screenshot_document" name="screenshot_document" 
                           accept=".jpg,.jpeg,.png" required>
                    <div class="form-text">Format: JPG, PNG. Maksimal 5MB.</div>
                </div>

                <!-- Manual Penggunaan -->
                <div class="mb-3">
                    <label for="manual_document" class="form-label">Manual Penggunaan Program (PDF) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="manual_document" name="manual_document" 
                           accept=".pdf" required>
                    <div class="form-text">Format: PDF. Cover, screenshot, dan manual dalam 1 file PDF. Maksimal 20MB.</div>
                </div>

                <!-- Link Program -->
                <div class="mb-3">
                    <label for="program_link" class="form-label">Link Program <span class="text-danger">*</span></label>
                    <input type="url" class="form-control" id="program_link" name="program_link" 
                           placeholder="https://example.com" required>
                    <div class="form-text">Link akses ke program/aplikasi yang dapat diakses online.</div>
                </div>
            `;

            guidelinesContent.innerHTML = `
                <h6>Program Komputer</h6>
                <p><small>Meliputi: Web aplikasi, media pembelajaran berbentuk aplikasi, dll</small></p>
                <h6>Dokumen yang Diperlukan:</h6>
                <ul class="mb-3">
                    <li>Cover</li>
                    <li>Screenshot program</li>
                    <li>Manual penggunaan program (PDF)</li>
                    <li>Link program yang dapat diakses</li>
                </ul>
                <h6>Persyaratan:</h6>
                <ul class="mb-3">
                    <li>Manual penggunaan maksimal 20MB dalam PDF (gabungan cover, screenshot, manual)</li>
                    <li>Link program harus dapat diakses untuk verifikasi</li>
                </ul>
            `;
            break;

        case 'sinematografi':
            dynamicFields.innerHTML = `
                <!-- Video File -->
                <div class="mb-3">
                    <label for="video_file" class="form-label">File Video <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="video_file" name="video_file" 
                           accept=".mp4" required>
                    <div class="form-text">Format: MP4. Maksimal 20MB.</div>
                </div>

                <!-- Video Description -->
                <div class="mb-3">
                    <label for="video_description" class="form-label">Deskripsi Video</label>
                    <textarea class="form-control" id="video_description" name="video_description" rows="3" 
                              placeholder="Jelaskan konten video, durasi, dan tujuan pembuatan"></textarea>
                    <div class="form-text">Opsional: Penjelasan tambahan tentang video.</div>
                </div>
            `;

            guidelinesContent.innerHTML = `
                <h6>Sinematografi</h6>
                <p><small>Meliputi: Film, Video, animasi video, dll</small></p>
                <h6>Dokumen yang Diperlukan:</h6>
                <ul class="mb-3">
                    <li>File video dalam format MP4</li>
                    <li>Deskripsi konten video (opsional)</li>
                </ul>
                <h6>Persyaratan:</h6>
                <ul class="mb-3">
                    <li>Format video: MP4</li>
                    <li>Ukuran maksimal: 20MB</li>
                    <li>Kualitas video yang baik dan jelas</li>
                </ul>
            `;
            break;

        case 'buku':
            dynamicFields.innerHTML = `
                <!-- E-book File -->
                <div class="mb-3">
                    <label for="ebook_file" class="form-label">File E-book <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="ebook_file" name="ebook_file" 
                           accept=".pdf" required>
                    <div class="form-text">Format: PDF. Maksimal 20MB.</div>
                </div>

                <!-- ISBN (if any) -->
                <div class="mb-3">
                    <label for="isbn" class="form-label">ISBN</label>
                    <input type="text" class="form-control" id="isbn" name="isbn" 
                           placeholder="Masukkan ISBN jika ada">
                    <div class="form-text">Opsional: Nomor ISBN jika buku sudah terdaftar.</div>
                </div>

                <!-- Number of Pages -->
                <div class="mb-3">
                    <label for="page_count" class="form-label">Jumlah Halaman</label>
                    <input type="number" class="form-control" id="page_count" name="page_count" 
                           placeholder="Contoh: 150">
                    <div class="form-text">Jumlah halaman dalam buku.</div>
                </div>
            `;

            guidelinesContent.innerHTML = `
                <h6>Buku</h6>
                <p><small>E-book dalam format digital</small></p>
                <h6>Dokumen yang Diperlukan:</h6>
                <ul class="mb-3">
                    <li>File e-book dalam format PDF</li>
                    <li>ISBN (jika ada)</li>
                    <li>Informasi jumlah halaman</li>
                </ul>
                <h6>Persyaratan:</h6>
                <ul class="mb-3">
                    <li>Format: PDF</li>
                    <li>Ukuran maksimal: 20MB</li>
                    <li>Kualitas teks yang jelas dan dapat dibaca</li>
                </ul>
            `;
            break;

        case 'poster_fotografi':
            dynamicFields.innerHTML = `
                <!-- Image File -->
                <div class="mb-3">
                    <label for="image_file" class="form-label">File Gambar/Foto <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="image_file" name="image_file" 
                           accept=".jpg,.jpeg,.png" required>
                    <div class="form-text">Format: JPG, PNG. Maksimal 1MB.</div>
                </div>

                <!-- Image Type -->
                <div class="mb-3">
                    <label for="image_type" class="form-label">Jenis Karya <span class="text-danger">*</span></label>
                    <select class="form-select" id="image_type" name="image_type" required>
                        <option value="">Pilih Jenis Karya</option>
                        <option value="poster">Poster</option>
                        <option value="fotografi">Fotografi</option>
                        <option value="seni_gambar">Seni Gambar</option>
                        <option value="karakter_animasi">Karakter Animasi</option>
                    </select>
                </div>

                <!-- Dimensions -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="width" class="form-label">Lebar (px)</label>
                            <input type="number" class="form-control" id="width" name="width" 
                                   placeholder="Contoh: 1920">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="height" class="form-label">Tinggi (px)</label>
                            <input type="number" class="form-control" id="height" name="height" 
                                   placeholder="Contoh: 1080">
                        </div>
                    </div>
                </div>
            `;

            guidelinesContent.innerHTML = `
                <h6>Poster / Fotografi / Seni Gambar / Karakter Animasi</h6>
                <p><small>Karya visual dalam bentuk gambar atau foto</small></p>
                <h6>Dokumen yang Diperlukan:</h6>
                <ul class="mb-3">
                    <li>File gambar/foto</li>
                    <li>Spesifikasi jenis karya</li>
                    <li>Dimensi gambar</li>
                </ul>
                <h6>Persyaratan:</h6>
                <ul class="mb-3">
                    <li>Format: JPG, PNG</li>
                    <li>Ukuran maksimal: 1MB</li>
                    <li>Resolusi yang baik dan jelas</li>
                </ul>
            `;
            break;

        case 'alat_peraga':
            dynamicFields.innerHTML = `
                <!-- Photo of Teaching Aid -->
                <div class="mb-3">
                    <label for="tool_photo" class="form-label">Foto Alat Peraga <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="tool_photo" name="tool_photo" 
                           accept=".jpg,.jpeg,.png" required>
                    <div class="form-text">Format: JPG, PNG. Maksimal 1MB.</div>
                </div>

                <!-- Multiple Photos (Optional) -->
                <div class="mb-3">
                    <label for="additional_photos" class="form-label">Foto Tambahan</label>
                    <input type="file" class="form-control" id="additional_photos" name="additional_photos[]" 
                           accept=".jpg,.jpeg,.png" multiple>
                    <div class="form-text">Opsional: Foto dari berbagai sudut. Format: JPG, PNG. Maksimal 1MB per file.</div>
                </div>

                <!-- Materials Used -->
                <div class="mb-3">
                    <label for="materials" class="form-label">Bahan yang Digunakan</label>
                    <textarea class="form-control" id="materials" name="materials" rows="3" 
                              placeholder="Sebutkan bahan-bahan yang digunakan untuk membuat alat peraga"></textarea>
                </div>

                <!-- Usage Instructions -->
                <div class="mb-3">
                    <label for="usage_instructions" class="form-label">Cara Penggunaan</label>
                    <textarea class="form-control" id="usage_instructions" name="usage_instructions" rows="3" 
                              placeholder="Jelaskan cara menggunakan alat peraga ini"></textarea>
                </div>
            `;

            guidelinesContent.innerHTML = `
                <h6>Alat Peraga</h6>
                <p><small>Alat bantu pembelajaran atau demonstrasi</small></p>
                <h6>Dokumen yang Diperlukan:</h6>
                <ul class="mb-3">
                    <li>Foto alat peraga (wajib)</li>
                    <li>Foto tambahan dari berbagai sudut (opsional)</li>
                    <li>Deskripsi bahan yang digunakan</li>
                    <li>Cara penggunaan</li>
                </ul>
                <h6>Persyaratan:</h6>
                <ul class="mb-3">
                    <li>Format foto: JPG, PNG</li>
                    <li>Ukuran maksimal: 1MB per file</li>
                    <li>Foto yang jelas menampilkan alat peraga</li>
                </ul>
            `;
            break;

        case 'basis_data':
            dynamicFields.innerHTML = `
                <!-- Metadata File -->
                <div class="mb-3">
                    <label for="metadata_file" class="form-label">File Metadata <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="metadata_file" name="metadata_file" 
                           accept=".pdf" required>
                    <div class="form-text">Format: PDF. Maksimal 20MB.</div>
                </div>

                <!-- Database Type -->
                <div class="mb-3">
                    <label for="database_type" class="form-label">Jenis Basis Data <span class="text-danger">*</span></label>
                    <select class="form-select" id="database_type" name="database_type" required>
                        <option value="">Pilih Jenis Basis Data</option>
                        <option value="relational">Relational Database</option>
                        <option value="nosql">NoSQL Database</option>
                        <option value="research_data">Research Dataset</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <!-- Number of Records -->
                <div class="mb-3">
                    <label for="record_count" class="form-label">Jumlah Record/Data</label>
                    <input type="number" class="form-control" id="record_count" name="record_count" 
                           placeholder="Contoh: 10000">
                    <div class="form-text">Perkiraan jumlah record atau data dalam basis data.</div>
                </div>

                <!-- Database Purpose -->
                <div class="mb-3">
                    <label for="database_purpose" class="form-label">Tujuan/Kegunaan Basis Data</label>
                    <textarea class="form-control" id="database_purpose" name="database_purpose" rows="3" 
                              placeholder="Jelaskan tujuan dan kegunaan basis data ini"></textarea>
                </div>
            `;

            guidelinesContent.innerHTML = `
                <h6>Basis Data</h6>
                <p><small>Kumpulan data terstruktur dengan metadata</small></p>
                <h6>Dokumen yang Diperlukan:</h6>
                <ul class="mb-3">
                    <li>File metadata dalam PDF</li>
                    <li>Spesifikasi jenis basis data</li>
                    <li>Informasi jumlah record</li>
                    <li>Deskripsi tujuan dan kegunaan</li>
                </ul>
                <h6>Persyaratan:</h6>
                <ul class="mb-3">
                    <li>Format metadata: PDF</li>
                    <li>Ukuran maksimal: 20MB</li>
                    <li>Metadata harus lengkap dan terstruktur</li>
                </ul>
            `;
            break;

        default:
            guidelinesContent.innerHTML = `
                <div class="text-center py-3">
                    <i class="bi bi-file-earmark-text fs-3 text-muted mb-2"></i>
                    <h6>Pilih Jenis Ciptaan</h6>
                    <p class="text-muted mb-0">Silakan pilih jenis ciptaan terlebih dahulu untuk melihat persyaratan dokumen yang diperlukan.</p>
                </div>
            `;
    }
}

function addFileValidation() {
    // File size validation for different types
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const maxSize = input.accept.includes('video') || input.id === 'metadata_file' || input.id === 'manual_document' || input.id === 'ebook_file' ? 20 * 1024 * 1024 : // 20MB for videos, PDFs, ebooks
                            input.accept.includes('image') || input.id.includes('photo') || input.id.includes('image') ? 1 * 1024 * 1024 : // 1MB for images
                            5 * 1024 * 1024; // 5MB for others
            
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
</style>
@endpush
@endsection