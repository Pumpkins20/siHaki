@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="card-title">Selamat Datang, {{ Auth::user()->nama }}!</h4>
                            <p class="card-text">Kelola submission HKI Anda dan pantau progresnya di sini.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('user.submissions.create') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-plus-circle"></i> Ajukan HKI Baru
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Progress Bar Pengajuan -->
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Progress Pengajuan</h6>
                </div>
                <div class="card-body text-center">
                    <div class="progress-ring">
                        <svg class="progress-ring" width="120" height="120">
                            <circle class="progress-ring__circle" stroke="#28a745" stroke-width="4" fill="transparent" r="45" cx="60" cy="60"/>
                        </svg>
                        <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <h4 class="text-success">{{ $progress }}%</h4>
                            <small class="text-muted">Completion</small>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-success">
                                    <strong>{{ $stats['approved_submissions'] }}</strong>
                                    <br><small>Approved</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-warning">
                                    <strong>{{ $stats['pending_submissions'] }}</strong>
                                    <br><small>Pending</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="col-xl-8 col-lg-6">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card border-start border-primary border-4 shadow h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Pengajuan
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_submissions'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-file-earmark-text fs-2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-start border-warning border-4 shadow h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Draft
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['draft_submissions'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-file-earmark-edit fs-2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-start border-info border-4 shadow h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Under Review
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['review_submissions'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-hourglass-split fs-2 text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card border-start border-danger border-4 shadow h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Perlu Revisi
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['revision_submissions'] }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-arrow-clockwise fs-2 text-gray-300"></i>
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
                    <a href="{{ route('user.history') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye"></i> Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($recent_submissions->count() > 0)
                        @foreach($recent_submissions as $submission)
                        <div class="d-flex align-items-center border-bottom py-3">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }} text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-{{ $submission->status === 'approved' ? 'check' : ($submission->status === 'rejected' ? 'x' : 'clock') }}"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ Str::limit($submission->title, 40) }}</h6>
                                <p class="mb-1 text-muted small">{{ ucfirst($submission->type) }}</p>
                                <p class="mb-0 text-muted small">{{ $submission->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge bg-{{ $submission->status === 'approved' ? 'success' : ($submission->status === 'rejected' ? 'danger' : 'warning') }}">
                                    {{ ucfirst(str_replace('_', ' ', $submission->status)) }}
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
                        <a href="{{ route('user.panduan') }}" class="btn btn-info">
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
                    <h6>Kontak Admin SiHaki</h6>
                    <div class="mt-3">
                        <p class="mb-2">
                            <i class="bi bi-envelope text-primary"></i> 
                            <strong>Email:</strong> hki@amikom.ac.id
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-telephone text-success"></i> 
                            <strong>Telp:</strong> (0271) 7851507
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-whatsapp text-success"></i> 
                            <strong>WhatsApp:</strong> 081329303450
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-clock text-info"></i> 
                            <strong>Jam Kerja:</strong> Senin-Jumat: 08:00-16:00
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="mailto:hki@amikom.ac.id" class="btn btn-primary me-2">
                            <i class="bi bi-envelope"></i> Email
                        </a>
                        <a href="https://wa.me/6281329303450" class="btn btn-success" target="_blank">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
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
    .bg-gradient-success {
        background: linear-gradient(87deg, #2dce89 0%, #2dcecc 100%);
    }
    .progress-ring {
        position: relative;
    }
</style>
@endpush
@endsection