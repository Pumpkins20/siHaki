{{-- filepath: resources/views/admin/panduan/index.blade.php --}}

@extends('layouts.admin')

@section('title', 'Panduan Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Panduan Admin</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Panduan Admin SiHaki</h1>
                    <p class="text-muted mb-0">Panduan lengkap untuk operator sistem HKI</p>
                </div>
                <div>
                    <a href="{{ route('admin.submissions.index') }}" class="btn btn-primary">
                        <i class="bi bi-file-earmark-check"></i> Tinjau Pengajuan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Access Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-start border-primary border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Download</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">Panduan PDF</div>
                            <small class="text-muted">Panduan admin format PDF</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-file-earmark-pdf fs-2 text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">FAQ</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $filteredFaqs->count() }} Pertanyaan</div>
                            <small class="text-muted">Pertanyaan untuk admin</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-question-circle fs-2 text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Support</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">Hubungi Kontak Kami</div>
                            <small class="text-muted">Tim IT siap membantu</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-headset fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="col-md-3">
            <div class="card border-start border-info border-4 shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Training</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">Video Tutorial</div>
                            <small class="text-muted">Panduan video sistem</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-play-circle fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-xl-8 col-lg-7">
            <!-- Download Guides Section -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-download me-2"></i>Download Panduan Admin
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($guides as $guide)
                        <div class="col-md-6">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <i class="bi bi-file-earmark-pdf fs-3 text-danger"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="card-title mb-1">{{ $guide['title'] }}</h6>
                                            <p class="card-text small text-muted mb-2">{{ $guide['description'] }}</p>
                                            <div class="small text-muted mb-3">
                                                <span class="me-3"><i class="bi bi-file-text me-1"></i>{{ $guide['pages'] }} hal</span>
                                                <span class="me-3"><i class="bi bi-hdd me-1"></i>{{ $guide['size'] }}</span>
                                                <span><i class="bi bi-calendar me-1"></i>{{ date('d M Y', strtotime($guide['updated'])) }}</span>
                                            </div>
                                            <a href="{{ route('admin.panduan.download', $guide['file']) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="bi bi-question-circle me-2"></i>FAQ untuk Admin
                        </h6>
                        <small class="text-muted">{{ $filteredFaqs->count() }} pertanyaan ditemukan</small>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <form method="GET" action="{{ route('admin.panduan.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <select name="category" class="form-select" onchange="this.form.submit()">
                                    <option value="all" {{ $category == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                                    @foreach($categories as $key => $name)
                                        <option value="{{ $key }}" {{ $category == $key ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari pertanyaan..." value="{{ $search }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        @if($category !== 'all' || $search)
                            <div class="mt-2">
                                <a href="{{ route('admin.panduan.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                </a>
                            </div>
                        @endif
                    </form>

                    <!-- FAQ Accordion -->
                    @if($filteredFaqs->count() > 0)
                        <div class="accordion" id="faqAccordion">
                            @foreach($filteredFaqs as $index => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $faq['id'] }}">
                                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" 
                                            type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ $faq['id'] }}" 
                                            aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" 
                                            aria-controls="collapse{{ $faq['id'] }}">
                                        <div class="d-flex align-items-center w-100">
                                            @php
                                                $categoryColors = [
                                                    'umum' => 'primary',
                                                    'review' => 'success', 
                                                    'dokumen' => 'warning',
                                                    'sertifikat' => 'danger',
                                                    'users' => 'info',
                                                    'sistem' => 'secondary'
                                                ];
                                                $color = $categoryColors[$faq['category']] ?? 'primary';
                                            @endphp
                                            <span class="badge bg-{{ $color }} me-2">
                                                {{ $categories[$faq['category']] }}
                                            </span>
                                            <span class="flex-grow-1">{{ $faq['question'] }}</span>
                                            @if($faq['is_popular'])
                                                <span class="badge bg-warning text-dark ms-2">Populer</span>
                                            @endif
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $faq['id'] }}" 
                                     class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" 
                                     aria-labelledby="heading{{ $faq['id'] }}" 
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p class="mb-0">{{ $faq['answer'] }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-search fs-1 text-muted"></i>
                            <h5 class="mt-2 text-muted">Tidak ada FAQ ditemukan</h5>
                            <p class="text-muted">Coba ubah kata kunci pencarian atau kategori</p>
                            <a href="{{ route('admin.panduan.index') }}" class="btn btn-primary">
                                Lihat Semua FAQ
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-lightning me-2"></i>Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.submissions.index', ['status' => 'submitted']) }}" 
                           class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-file-earmark-check text-warning me-2"></i>Review Pending
                        </a>
                        <a href="{{ route('admin.users.create') }}" 
                           class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-person-plus text-success me-2"></i>Tambah User Baru
                        </a>
                        <a href="{{ route('admin.certificates.index') }}" 
                           class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-award text-info me-2"></i>Kirim Sertifikat
                        </a>
                        <a href="{{ route('admin.review-history.index') }}" 
                           class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-clock-history text-secondary me-2"></i>Riwayat Review
                        </a>
                        <a href="{{ route('admin.users.index') }}" 
                           class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-people text-primary me-2"></i>Kelola User
                        </a>
                    </div>
                </div>
            </div>

            <!-- 
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="bi bi-star me-2"></i>FAQ Terpopuler
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($popularFaqs->take(3) as $faq)
                    <div class="border-bottom pb-2 mb-2">
                        <h6 class="small fw-bold">{{ Str::limit($faq['question'], 50) }}</h6>
                        <p class="small text-muted mb-1">{{ Str::limit($faq['answer'], 80) }}</p>
                        @php
                            $categoryColors = [
                                'umum' => 'primary',
                                'review' => 'success', 
                                'dokumen' => 'warning',
                                'sertifikat' => 'danger',
                                'users' => 'info',
                                'sistem' => 'secondary'
                            ];
                            $color = $categoryColors[$faq['category']] ?? 'primary';
                        @endphp
                        <span class="badge bg-{{ $color }} small">{{ $categories[$faq['category']] }}</span>
                    </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.panduan.index') }}" class="btn btn-sm btn-outline-success">
                            Lihat Semua FAQ
                        </a>
                    </div>
                </div>
            </div>Popular FAQ -->

            <!-- IT Support Contact -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-warning">
                        <i class="bi bi-headset me-2"></i>IT Support
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="bi bi-person-badge fs-1 text-warning mb-3"></i>
                        <h6>Dukungan Teknis</h6>
                        <p class="small text-muted mb-3">
                            Dukungan teknis untuk sistem SiHaki
                        </p>
                        
                        <div class="d-grid gap-2">
                          <a href="https://instagram.com/riodsn_" 
                            class="btn btn-danger btn-sm d-flex align-items-center justify-content-center" 
                            target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-instagram me-2"></i>
                                <span>Instagram</span>
                            </a>

                            <a href="https://wa.me/6281329303450" 
                            class="btn btn-success btn-sm d-flex align-items-center justify-content-center" 
                            target="_blank" rel="noopener noreferrer">
                                <i class="bi bi-whatsapp me-2"></i>
                                <span>WhatsApp</span>
                            </a>

                        </div>
                        
                        <div class="mt-3 small text-muted">
                            <i class="bi bi-clock me-1"></i>
                            24/7 Emergency Support
                        </div>
                    </div>
                </div>
            </div>

            <!-- 
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-info">
                        <i class="bi bi-server me-2"></i>Status Sistem
                    </h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Server Status:</span>
                            <span class="badge bg-success">Online</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Database:</span>
                            <span class="badge bg-success">Connected</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Storage:</span>
                            <span class="badge bg-warning">75% Used</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Last Backup:</span>
                            <span class="text-muted">{{ now()->subDays(1)->format('d M Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>System Version:</span>
                            <span class="text-muted">v2.1.0</span>
                        </div>
                    </div>
                </div>
            </div>System Status -->
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality with delay
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Auto-submit form after 1 second of no typing
                // this.form.submit();
            }, 1000);
        });
    }

    // Track FAQ clicks for analytics
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Admin FAQ clicked:', this.textContent.trim());
        });
    });

    // Smooth scroll to FAQ if coming from external link
    if (window.location.hash) {
        const target = document.querySelector(window.location.hash);
        if (target) {
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
    }
});
</script>
@endpush

@push('styles')
<style>
.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    border-color: #b6d7ff;
    color: #0c63e4;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    border-color: #86b7fe;
}

.list-group-item-action:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.85em;
    padding: 0.5rem 0.8rem;
    min-width: 70px;
    text-align: center;
    display: inline-block;
    margin-right: 1rem;
    font-weight: 600;
}

.card-title {
    line-height: 1.3;
}

.border-start {
    border-left-width: 4px !important;
}

@media (max-width: 768px) {
    .card-body {
        padding: 1rem;
    }
    
    .accordion-button {
        padding: 0.75rem;
        font-size: 0.9rem;
    }
    
    .badge {
        font-size: 0.6em;
        min-width: 60px;
    }
}
</style>
@endpush
@endsection