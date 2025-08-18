@extends('layouts.admin')

@section('title', 'Tambah User Baru')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none">Kelola Pengguna</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Tambah Pengguna Baru</h1>
                    <p class="text-muted mb-0">Buat akun pengguna baru untuk dosen atau admin</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Form -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-person-plus me-2"></i>Informasi Pengguna Baru
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                        @csrf
                        
                        <!-- ✅ 
                        @if(config('app.debug'))
                            <div class="alert alert-info">
                                <strong>DEBUG:</strong> Form akan submit ke: {{ route('admin.users.store') }}
                            </div>
                        @endif DEBUG: Add form debugging in development -->
                        
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-person-badge me-2"></i>Informasi Dasar
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nidn" class="form-label">NIDN <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nidn') is-invalid @enderror" 
                                            id="nidn" name="nidn" value="{{ old('nidn') }}" 
                                            placeholder="Masukkan NIDN" required>
                                        @error('nidn')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">NIDN akan menjadi password default</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                            id="nama" name="nama" value="{{ old('nama') }}" 
                                            placeholder="Masukkan nama lengkap" required>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                            id="username" name="username" value="{{ old('username') }}" 
                                            placeholder="Username untuk login" required>
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Username sama dengan NIDN</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                            id="email" name="email" value="{{ old('email') }}" 
                                            placeholder="Masukkan email" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Nomor Telepon</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                            id="phone" name="phone" value="{{ old('phone') }}" 
                                            placeholder="Contoh: 08123456789">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="program_studi" class="form-label">Program Studi <span class="text-danger">*</span></label>
                                        <select class="form-select @error('program_studi') is-invalid @enderror" 
                                                id="program_studi" name="program_studi" required>
                                            <option value="">Pilih Program Studi</option>
                                            <option value="D3 Manajemen Informatika" {{ old('program_studi') == 'D3 Manajemen Informatika' ? 'selected' : '' }}>D3 Manajemen Informatika</option>
                                            <option value="S1 Informatika" {{ old('program_studi') == 'S1 Informatika' ? 'selected' : '' }}>S1 Informatika</option>
                                            <option value="S1 Sistem Informasi" {{ old('program_studi') == 'S1 Sistem Informasi' ? 'selected' : '' }}>S1 Sistem Informasi</option>
                                            <option value="S1 Teknologi Informasi" {{ old('program_studi') == 'S1 Teknologi Informasi' ? 'selected' : '' }}>S1 Teknologi Informasi</option>
                                        </select>
                                        @error('program_studi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department_id" class="form-label">Departemen <span class="text-danger">*</span></label>
                                        <select class="form-select @error('department_id') is-invalid @enderror" 
                                                id="department_id" name="department_id" required>
                                            <option value="">Pilih Departemen</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" 
                                                        {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('department_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                        <select class="form-select @error('role') is-invalid @enderror" 
                                                id="role" name="role" required>
                                            <option value="">Pilih Role</option>
                                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Dosen</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Pilih role sesuai dengan fungsi user</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Account Settings -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-gear me-2"></i>Pengaturan Akun
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" 
                                                name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Akun Aktif
                                            </label>
                                        </div>
                                        <div class="form-text">User dapat login jika akun aktif</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="bi bi-save"></i> Simpan User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-xl-4 col-lg-5">
            <!-- Guidelines -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-info-circle me-2"></i>Panduan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <h6 class="fw-bold">Informasi Penting:</h6>
                        <ul class="mb-3">
                            <li><strong>NIDN:</strong> Akan menjadi password default</li>
                            <li><strong>Username:</strong> Harus unique untuk login</li>
                            <li><strong>Email:</strong> Akan digunakan untuk notifikasi</li>
                            <li><strong>Role Dosen:</strong> Dapat mengajukan HKI</li>
                            <li><strong>Role Admin:</strong> Dapat mengelola sistem</li>
                        </ul>
                        
                        <div class="p-3 border-start border-4 border-info bg-light rounded mb-3">
                        <i class="bi bi-shield-lock text-info me-2"></i>
                        <strong>Keamanan:</strong> User wajib mengganti password default saat login pertama kali.
                        </div>

                    </div>
                </div>
            </div>

            <!-- Department Info 
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-building me-2"></i>Departemen Tersedia
                    </h6>
                </div>
                <div class="card-body">
                    @if($departments->count() > 0)
                        <div class="small">
                            @foreach($departments as $dept)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $dept->name }}</span>
                                    <span class="badge bg-secondary">{{ $dept->users_count ?? 0 }} user</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted small">
                            <i class="bi bi-building"></i>
                            <p class="mt-2">Belum ada departemen</p>
                            <a href="{{ route('admin.departments.create') }}" class="btn btn-sm btn-primary">
                                Tambah Departemen
                            </a>
                        </div>
                    @endif
                </div>
            </div>-->
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate username from NIDN
    document.getElementById('nidn').addEventListener('input', function() {
        const nidn = this.value;
        const usernameField = document.getElementById('username');
        if (nidn && !usernameField.value) {
            usernameField.value = nidn;
        }
    });

    // Form submission handling with debug
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        
        // ✅ DEBUG: Log form data
        if (window.console) {
            const formData = new FormData(this);
            console.log('Form submission data:');
            for (let [key, value] of formData.entries()) {
                if (key !== 'password') { // Don't log password
                    console.log(key + ': ' + value);
                }
            }
        }
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyimpan...';
        
        // Re-enable after 15 seconds if still processing (likely an error)
        setTimeout(() => {
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-save"></i> Simpan User';
                console.error('Form submission took too long, re-enabling button');
            }
        }, 15000);
    });

    // Phone number validation - only numbers
    document.getElementById('phone').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9+\-\s]/g, '');
    });

    // ✅ Add form validation feedback
    const form = document.getElementById('createUserForm');
    form.addEventListener('invalid', function(e) {
        e.preventDefault();
        const firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) {
            firstInvalid.focus();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, true);
});
</script>
@endpush

@push('styles')
<style>
.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.border-top {
    border-top: 1px solid #dee2e6 !important;
}

.card-header {
    background-color: #f8f9fc;
}
</style>
@endpush
@endsection