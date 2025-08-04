@extends('layouts.user')

@section('title', 'Profile')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Profil</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 text-gray-800">Profil Pengguna</h1>
            <p class="text-muted mb-0">Kelola informasi profil dan pengaturan akun Anda</p>
        </div>
    </div>

    <div class="row">
        <!-- Profile Photo Section -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-person-circle me-2"></i>Foto Profil
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="profile-photo-container mb-3">
                        @if($user->foto && $user->foto !== 'default.png')
                            <img src="{{ asset('storage/profile_photos/' . $user->foto) }}" 
                                 alt="Profile Photo" 
                                 class="profile-photo img-thumbnail"
                                 id="profilePreview">
                        @else
                            <div class="default-avatar" id="profilePreview">
                                <i class="bi bi-person-circle"></i>
                                <span class="initials">{{ strtoupper(substr($user->nama, 0, 2)) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="mb-1">{{ $user->nama }}</h5>
                    <p class="text-muted mb-3">{{ $user->program_studi }}</p>
                    
                    <!-- Upload Photo Form -->
                    <form action="{{ route('user.profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                        @csrf
                        <div class="mb-3">
                            <input type="file" class="form-control" id="photoInput" name="photo" 
                                   accept="image/jpeg,image/jpg,image/png" style="display: none;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('photoInput').click()">
                                <i class="bi bi-camera me-1"></i>Pilih Foto
                            </button>
                        </div>
                        <div class="form-text mb-3">
                            Format: JPG, PNG. Maksimal 2MB.
                        </div>
                        <button type="submit" class="btn btn-success btn-sm" id="uploadBtn" style="display: none;">
                            <i class="bi bi-upload me-1"></i>Upload Foto
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-bar-chart me-2"></i>Statistik Saya
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="h5 mb-0 text-primary">{{ $user->submissions()->count() }}</div>
                            <small class="text-muted">Total Pengajuan</small>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 text-success">{{ $user->submissions()->where('status', 'approved')->count() }}</div>
                            <small class="text-muted">Disetujui</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Information Section -->
        <div class="col-xl-8 col-lg-7">
            <!-- Personal Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-person-badge me-2"></i>Informasi Pribadi
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.profile.update') }}" method="POST" id="profileForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nidn" class="form-label">NIDN</label>
                                    <input type="text" class="form-control bg-light" id="nidn" 
                                           value="{{ $user->nidn }}" readonly>
                                    <div class="form-text">NIDN tidak dapat diubah</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                           id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required>
                                    @error('nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="program_studi" class="form-label">Program Studi</label>
                                    <input type="text" class="form-control bg-light" id="program_studi" 
                                           value="{{ $user->program_studi }}" readonly>
                                    <div class="form-text">Program studi tidak dapat diubah</div>
                                </div>
                            </div>
                           <!--  <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department" class="form-label">Departemen</label>
                                    <input type="text" class="form-control bg-light" id="department" 
                                           value="{{ $user->department ? $user->department->name : 'Tidak ada' }}" readonly>
                                    <div class="form-text">Departemen tidak dapat diubah</div>
                                </div>
                            </div> -->
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-shield-lock me-2"></i>Ubah Kata Sandi
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.change-password') }}" method="POST" id="passwordForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                               id="current_password" name="current_password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                            <i class="bi bi-eye" id="current_password_icon"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                               id="new_password" name="new_password" required minlength="6">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                            <i class="bi bi-eye" id="new_password_icon"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Minimal 6 karakter</div>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" 
                                               id="new_password_confirmation" name="new_password_confirmation" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                            <i class="bi bi-eye" id="new_password_confirmation_icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Tips Keamanan:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Gunakan kombinasi huruf besar, kecil, angka, dan simbol</li>
                                <li>Hindari menggunakan informasi personal yang mudah ditebak</li>
                                <li>Ganti password secara berkala</li>
                            </ul>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-key me-1"></i>Ubah kata sandi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Photo upload preview
    document.getElementById('photoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                this.value = '';
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert('Format file tidak didukung. Gunakan JPG atau PNG.');
                this.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profilePreview');
                if (preview.tagName === 'IMG') {
                    preview.src = e.target.result;
                } else {
                    // Replace default avatar with image
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Profile Photo';
                    img.className = 'profile-photo img-thumbnail';
                    img.id = 'profilePreview';
                    preview.parentNode.replaceChild(img, preview);
                }
            };
            reader.readAsDataURL(file);

            // Show upload button
            document.getElementById('uploadBtn').style.display = 'inline-block';
        }
    });

    // Password confirmation validation
    document.getElementById('new_password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('new_password').value;
        const confirmation = this.value;
        
        if (confirmation && password !== confirmation) {
            this.setCustomValidity('Password tidak sesuai');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });

    // Form submission handling
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';
        
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Simpan Perubahan';
        }, 3000);
    });

    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Mengubah...';
        
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-key me-1"></i>Ubah Password';
        }, 3000);
    });
});

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
@endpush

@push('styles')
<style>
.profile-photo {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border-radius: 50%;
}

.default-avatar {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0 auto;
}

.default-avatar i {
    font-size: 100px;
    color: white;
    opacity: 0.3;
}

.default-avatar .initials {
    position: absolute;
    font-size: 48px;
    font-weight: bold;
    color: white;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    transform: translateY(-1px);
}

.profile-photo-container {
    position: relative;
    display: inline-block;
}

.bg-light {
    background-color: #f8f9fa !important;
}

@media (max-width: 768px) {
    .profile-photo, .default-avatar {
        width: 150px;
        height: 150px;
    }
    
    .default-avatar i {
        font-size: 75px;
    }
    
    .default-avatar .initials {
        font-size: 36px;
    }
}
</style>
@endpush
@endsection