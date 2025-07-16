
@extends('layouts.admin')

@section('title', 'Detail User')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none">Kelola User</a></li>
                    <li class="breadcrumb-item active">{{ $user->nama }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Detail User: {{ $user->nama }}</h1>
                    <p class="text-muted mb-0">Informasi lengkap tentang user</p>
                </div>
                <div>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning me-2">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- User Profile -->
        <div class="col-xl-4 col-lg-5">
            <!-- Profile Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-person-circle me-2"></i>Profile
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="profile-photo-container mb-3">
                        @if($user->foto && $user->foto !== 'default.png')
                            <img src="{{ asset('storage/profile_photos/' . $user->foto) }}" 
                                 alt="Profile Photo" 
                                 class="profile-photo img-thumbnail">
                        @else
                            <div class="default-avatar">
                                <i class="bi bi-person-circle"></i>
                                <span class="initials">{{ strtoupper(substr($user->nama, 0, 2)) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <h5 class="mb-1">{{ $user->nama }}</h5>
                    <p class="text-muted mb-2">{{ $user->program_studi }}</p>
                    
                    <div class="mb-3">
                        @if($user->role === 'admin')
                            <span class="badge bg-danger fs-6">
                                <i class="bi bi-shield-check me-1"></i>Administrator
                            </span>
                        @else
                            <span class="badge bg-primary fs-6">
                                <i class="bi bi-person me-1"></i>Dosen
                            </span>
                        @endif
                        
                        @if($user->is_active)
                            <span class="badge bg-success fs-6 ms-1">
                                <i class="bi bi-check-circle me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6 ms-1">
                                <i class="bi bi-x-circle me-1"></i>Nonaktif
                            </span>
                        @endif
                    </div>
                    
                    <div class="text-start">
                        <p class="mb-1"><strong>NIDN:</strong> {{ $user->nidn }}</p>
                        <p class="mb-1"><strong>Username:</strong> {{ $user->username }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                        @if($user->phone)
                            <p class="mb-1"><strong>Telepon:</strong> {{ $user->phone }}</p>
                        @endif
                        <p class="mb-1"><strong>Departemen:</strong> {{ $user->department->name ?? 'Tidak ada' }}</p>
                        <p class="mb-0"><strong>Bergabung:</strong> {{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-lightning me-2"></i>Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit User
                        </a>
                        @if($user->role === 'user')
                            <a href="{{ route('admin.submissions.index', ['user' => $user->id]) }}" class="btn btn-info">
                                <i class="bi bi-file-earmark-text"></i> Lihat Submission
                            </a>
                        @endif
                        <button type="button" class="btn btn-outline-secondary" onclick="resetPassword()">
                            <i class="bi bi-key"></i> Reset Password
                        </button>
                        @if($user->id !== Auth::id())
                            <button type="button" class="btn {{ $user->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                    onclick="toggleStatus()">
                                <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i> 
                                {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- User Details -->
        <div class="col-xl-8 col-lg-7">
            <!-- Statistics -->
            @if($user->role === 'user')
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card border-start border-primary border-4 shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Submission</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->submissions()->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-file-earmark-text fs-2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-start border-success border-4 shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approved</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->submissions()->where('status', 'approved')->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-check-circle fs-2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-start border-warning border-4 shadow-sm h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->submissions()->whereIn('status', ['submitted', 'under_review'])->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-clock fs-2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Submissions -->
            @if($user->role === 'user')
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary">Submission Terbaru</h6>
                    <a href="{{ route('admin.submissions.index', ['user' => $user->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($user->submissions()->exists())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Jenis</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->submissions()->latest()->limit(5)->get() as $submission)
                                    <tr>
                                        <td>{{ Str::limit($submission->title, 30) }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($submission->creation_type) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $submission->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.submissions.show', $submission) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada submission</p>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Account Information -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-info-circle me-2"></i>Informasi Akun
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>User ID:</strong></td>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>NIDN:</strong></td>
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
                                            <span class="badge bg-danger">Administrator</span>
                                        @else
                                            <span class="badge bg-primary">Dosen</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>Status:</strong></td>
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
                                    <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Update Terakhir:</strong></td>
                                    <td>{{ $user->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email Verified:</strong></td>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Ya</span>
                                        @else
                                            <span class="badge bg-warning">Belum</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mereset password untuk user <strong>{{ $user->nama }}</strong>?</p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Password akan direset ke NIDN: <strong>{{ $user->nidn }}</strong>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-key"></i> Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} user <strong>{{ $user->nama }}</strong>?</p>
                @if($user->is_active)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        User yang dinonaktifkan tidak dapat login ke sistem.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }}">
                        <i class="bi bi-{{ $user->is_active ? 'pause' : 'play' }}"></i> 
                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resetPassword() {
    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
}

function toggleStatus() {
    new bootstrap.Modal(document.getElementById('toggleStatusModal')).show();
}
</script>
@endpush

@push('styles')
<style>
.profile-photo {
    width: 150px;
    height: 150px;
    object-fit: cover;
    border-radius: 50%;
}

.default-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin: 0 auto;
}

.default-avatar i {
    font-size: 75px;
    color: white;
    opacity: 0.3;
}

.default-avatar .initials {
    position: absolute;
    font-size: 36px;
    font-weight: bold;
    color: white;
}

.border-start {
    border-left-width: 4px !important;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}
</style>
@endpush
@endsection