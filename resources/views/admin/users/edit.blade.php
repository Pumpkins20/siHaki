@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none">Kelola User</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user) }}" class="text-decoration-none">{{ $user->nama }}</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Edit User: {{ $user->nama }}</h1>
                    <p class="text-muted mb-0">Update informasi user dan pengaturan akun</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
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
                        <i class="bi bi-pencil me-2"></i>Edit Informasi User
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" id="editUserForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-person-badge me-2"></i>Informasi Dasar
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nidn" class="form-label">NIDN</label>
                                        <input type="number" class="form-control bg-light" id="nidn" 
                                               value="{{ $user->nidn }}" readonly>
                                        <div class="form-text">NIDN tidak dapat diubah</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                               id="nama" name="nama" value="{{ old('nama', $user->nama) }}" 
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
                                        <input type="text" class="form-control bg-light @error('username') is-invalid @enderror" 
                                               id="username" name="username" value="{{ old('username', $user->username) }}" 
                                               placeholder="Username untuk login" required readonly>
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Username harus unique</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" 
                                               placeholder="email@example.com" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">No. Telepon</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                               placeholder="Nomor telepon">
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
                                            @foreach(App\Models\User::PROGRAM_STUDI_OPTIONS as $prodi)
                                                <option value="{{ $prodi }}" {{ old('program_studi', $user->program_studi) == $prodi ? 'selected' : '' }}>
                                                    {{ $prodi }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('program_studi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Department & Role -->
                        <div class="mb-4">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="bi bi-building me-2"></i>Departemen & Role
                            </h6>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department_id" class="form-label">Departemen <span class="text-danger">*</span></label>
                                        <select class="form-select @error('department_id') is-invalid @enderror" 
                                                id="department_id" name="department_id" required>
                                            <option value="">Pilih Departemen</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
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
                                                id="role" name="role" required {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                            <option value="">Pilih Role</option>
                                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Dosen</option>
                                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        @if($user->id === Auth::id())
                                            <input type="hidden" name="role" value="{{ $user->role }}">
                                            <div class="form-text text-warning">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Anda tidak dapat mengubah role sendiri
                                            </div>
                                        @endif
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                                   name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                                   {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Akun Aktif
                                            </label>
                                        </div>
                                        @if($user->id === Auth::id())
                                            <input type="hidden" name="is_active" value="1">
                                            <div class="form-text text-warning">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Anda tidak dapat menonaktifkan akun sendiri
                                            </div>
                                        @else
                                            <div class="form-text">User dapat login jika akun aktif</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="reset_password" 
                                                   name="reset_password" value="1">
                                            <label class="form-check-label" for="reset_password">
                                                Reset Password ke NIDN
                                            </label>
                                        </div>
                                        <div class="form-text">Centang untuk mereset password user ke NIDN default</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <div>
                                <button type="reset" class="btn btn-outline-warning me-2">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-save"></i> Update User
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Information -->
        <div class="col-xl-4 col-lg-5">
            <!-- Current User Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-person-circle me-2"></i>Informasi Saat Ini
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($user->foto && $user->foto !== 'default.png')
                            <img src="{{ asset('storage/profile_photos/' . $user->foto) }}" 
                                 alt="Profile Photo" class="rounded-circle mb-2" width="80" height="80">
                        @else
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white mx-auto mb-2" 
                                 style="width: 80px; height: 80px;">
                                {{ strtoupper(substr($user->nama, 0, 2)) }}
                            </div>
                        @endif
                        <h6 class="mb-1">{{ $user->nama }}</h6>
                        <small class="text-muted">{{ $user->program_studi }}</small>
                    </div>
                    
                    <div class="small">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="40%"><strong>NIDN:</strong></td>
                                <td><span class="font-monospace">{{ $user->nidn }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Username:</strong></td>
                                <td><span class="font-monospace">{{ $user->username }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Role:</strong></td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Admin</span>
                                    @else
                                        <span class="badge bg-primary">Dosen</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Bergabung:</strong></td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            @if($user->role === 'user')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-activity me-2"></i>Aktivitas Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Submission:</span>
                            <strong>{{ $user->submissions()->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Approved:</span>
                            <strong class="text-success">{{ $user->submissions()->where('status', 'approved')->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pending:</span>
                            <strong class="text-warning">{{ $user->submissions()->whereIn('status', ['submitted', 'under_review'])->count() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Rejected:</span>
                            <strong class="text-danger">{{ $user->submissions()->where('status', 'rejected')->count() }}</strong>
                        </div>
                    </div>
                    
                    @if($user->submissions()->exists())
                        <hr>
                        <div class="small">
                            <strong>Submission Terakhir:</strong><br>
                            @php $lastSubmission = $user->submissions()->latest()->first(); @endphp
                            {{ Str::limit($lastSubmission->title, 40) }}<br>
                            <small class="text-muted">{{ $lastSubmission->created_at->diffForHumans() }}</small>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Edit Guidelines -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-info-circle me-2"></i>Panduan Edit
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <h6 class="fw-bold">Yang Dapat Diubah:</h6>
                        <ul class="mb-3">
                            <li>Nama lengkap user</li>
                            <li>Username (harus unique)</li>
                            <li>Email user</li>
                            <li>Nomor telepon</li>
                            <li>Program studi</li>
                            <li>Departemen</li>
                            <li>Role user (kecuali untuk diri sendiri)</li>
                            <li>Status aktif/nonaktif</li>
                        </ul>
                        
                        <h6 class="fw-bold">Yang Tidak Dapat Diubah:</h6>
                        <ul class="mb-3">
                            <li>NIDN user</li>
                            <li>Tanggal bergabung</li>
                            <li>History submissions</li>
                        </ul>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Anda tidak dapat mengubah role atau menonaktifkan akun sendiri.
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
    // Form submission handling
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memperbarui...';
        
        // Re-enable after 10 seconds if still processing
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-save"></i> Update User';
        }, 10000);
    });

    // Phone number validation - only numbers
    document.getElementById('phone').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9+\-\s]/g, '');
    });

    // Reset password confirmation
    document.getElementById('reset_password').addEventListener('change', function() {
        if (this.checked) {
            if (!confirm('Apakah Anda yakin ingin mereset password user ini ke NIDN default?')) {
                this.checked = false;
            }
        }
    });
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

.bg-light {
    background-color: #f8f9fa !important;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}
</style>
@endpush
@endsection