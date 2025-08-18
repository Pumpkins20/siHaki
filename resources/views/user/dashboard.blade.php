@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title">Selamat Datang, {{ Auth::user()->nama }}!</h4>
                            <p class="card-text">Kelola pengajuan HKI Anda dan pantau progresnya di sini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--
@if(session('warning'))
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-3 border rounded bg-warning-subtle border-warning" role="alert">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="text-warning-emphasis">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Perhatian!</strong> {{ session('warning') }}
                        </div>
                        <div class="mt-3">
                            <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="bi bi-key me-1"></i>Ganti Password Sekarang
                            </a>
                        </div>
                    </div>
                    <button type="button" class="btn-close ms-3" onclick="this.parentElement.parentElement.parentElement.style.display='none'"></button>
                </div>
            </div>
        </div>
    </div>
@endif-->

    <div class="row">
        <!-- 
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Progress Pengajuan</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-success">
                                    <h4><strong>{{ $stats['approved_submissions'] }}</strong>
                                    <br><small>Approved</small></h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-warning">
                                    <h4><strong>{{ $stats['pending_submissions'] }}</strong>
                                    <br><small>Pending</small></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->

      <!-- Statistics Cards -->
<div class="col-xl-12 col-lg-12">
    <div class="row">
        {{-- Card 1 - Pending Review --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow h-100" style="border-left: 4px solid #17a2b8;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pending Review
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_submissions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-square bg-info text-white rounded">
                                <i class="bi bi-clock fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2 - Approved --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow h-100" style="border-left: 4px solid #28a745;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved_submissions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-square bg-success text-white rounded">
                                <i class="bi bi-check-circle fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3 - Revision Needed --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow h-100" style="border-left: 4px solid #ffc107;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Revision Needed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['revision_needed'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-square bg-warning text-white rounded">
                                <i class="bi bi-arrow-clockwise fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4 - Rejected --}}
        <div class="col-md-3 mb-4">
            <div class="card shadow h-100" style="border-left: 4px solid #dc3545;">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Rejected
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['rejected_submissions'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-square bg-danger text-white rounded">
                                <i class="bi bi-x-circle fs-2"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="row">
        <!-- Riwayat Terbaru -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Terbaru</h6>
                    <a href="{{ route('user.history.index') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($recent_submissions->count() > 0)
                        @foreach($recent_submissions as $submission)
                        @php
                            $statusColor = \App\Helpers\StatusHelper::getStatusColor($submission->status);
                            $statusIcon = \App\Helpers\StatusHelper::getStatusIcon($submission->status);
                            $statusName = \App\Helpers\StatusHelper::getStatusName($submission->status);
                        @endphp
                        <div class="d-flex align-items-center border-bottom py-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-{{ $statusColor }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-{{ $statusIcon }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ Str::limit($submission->title, 40) }}</h6>
                                <p class="mb-1 text-muted small">{{ ucfirst($submission->type) }}</p>
                                <p class="mb-0 text-muted small">{{ $submission->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-{{ $statusColor }}">
                                    <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusName }}
                                </span>
                                <a href="{{ route('user.submissions.show', $submission) }}" class="btn btn-sm btn-outline-primary ms-2">
                                    Detail
                                </a>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 text-muted">Belum ada pengajuan</p>
                            <a href="{{ route('user.submissions.create') }}" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Buat Pengajuan Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Actions & Notifications -->
        <div class="col-xl-4 col-lg-5">
            <!-- Tombol Cepat -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tombol Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('user.submissions.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Ajukan HKI
                        </a>
                        <a href="{{ route('user.panduan.index') }}" class="btn btn-info">
                            <i class="bi bi-book"></i> Lihat Panduan
                        </a>
                        <a href="#" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#contactModal">
                            <i class="bi bi-person-lines-fill"></i> Hubungi Admin
                        </a>
                    </div>
                </div>
            </div>

            <!-- Reminder Dokumen -->
            @if($reminders->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Reminder Dokumen</h6>
                </div>
                <div class="card-body">
                    @foreach($reminders as $reminder)
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>{{ $reminder->title }}</strong>
                        <br><small>{{ $reminder->review_notes }}</small>
                        <div class="mt-2">
                            <a href="{{ route('user.submissions.edit', $reminder) }}" class="btn btn-sm btn-warning">
                                Perbaiki Sekarang
                            </a>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Notifikasi Terbaru -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Notifikasi Terbaru</h6>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-{{ $notification->icon }} text-{{ $notification->type }}"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-1 small">{{ $notification->title }}</h6>
                                <p class="mb-0 text-muted small">{{ $notification->message }}</p>
                                <span class="text-muted small">{{ $notification->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-bell fs-3 text-muted"></i>
                            <p class="text-muted small mt-2">Tidak ada notifikasi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal fade" id="contactModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hubungi Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <h6>Kontak LPPM STMIK AMIKOM Surakarta</h6>
                    <div class="mt-3">
                        <p class="mb-2">
                        <i class="bi bi-envelope text-primary"></i> 
                        <strong>Email:</strong> 
                        <a href="mailto:lppm@amikomsolo.ac.id" style="text-decoration: none; color: inherit;">
                            lppm@amikomsolo.ac.id
                        </a></p>
                    <p class="mb-2">
                        <i class="bi bi-instagram text-danger"></i> 
                        <strong>Instagram:</strong> 
                        <a href="https://www.instagram.com/lppm_amikomsolo" target="_blank" style="text-decoration: none; color: inherit;">
                            @lppm_amikomsolo
                        </a> </p>
                    <p class="mb-2">
                        <i class="bi bi-whatsapp text-success"></i> 
                        <strong>WhatsApp:</strong> 
                        <a href="https://wa.me/6289504696000" target="_blank" style="text-decoration: none; color: inherit;">
                            089504696000
                        </a> </p>
                    </div>
                    <div class="mt-4">
                        <a href="https://www.instagram.com/lppm_amikomsolo" 
                        class="btn btn-danger me-2" target="_blank">
                            <i class="bi bi-instagram"></i> Instagram
                        </a>

                        <a href="https://wa.me/6289504696000" 
                        class="btn btn-success" target="_blank">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ganti Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('user.change-password') }}" method="POST">
                @csrf
                <div class="modal-body">
                
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                        <div class="form-text">Minimal 6 karakter</div>
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Ganti Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set progress ring
    setProgress({{ $progress }});
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert:not(.alert-warning)');
        alerts.forEach(alert => {
            if (alert.classList.contains('show')) {
                alert.classList.remove('show');
            }
        });
    }, 5000);
});
</script>
@endpush

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(87deg, #007bff 0%, #0056b3 100%);
    }
    
    .icon-square {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    
    /* ✅ UNIFIED: Consistent hover effects */
    .card:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
</style>
@endpush
@endsection