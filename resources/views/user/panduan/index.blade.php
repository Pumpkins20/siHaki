@extends('layouts.user')

@section('title', 'Panduan')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Panduan</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">Panduan SiHaki</h1>
                    <p class="text-muted mb-0">Panduan lengkap penggunaan sistem dan FAQ</p>
                </div>
                <div>
                    <a href="{{ route('user.panduan.export-faq') . '?' . http_build_query(request()->query()) }}" 
                       class="btn btn-success me-2">
                        <i class="bi bi-download"></i> Export FAQ
                    </a>
                    <a href="{{ route('user.submissions.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Ajukan HKI
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
                            <small class="text-muted">Panduan lengkap format PDF</small>
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
                            <small class="text-muted">Pertanyaan yang sering diajukan</small>
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
                            <div class="h6 mb-0 font-weight-bold text-gray-800">24/7 Bantuan</div>
                            <small class="text-muted">Tim support siap membantu</small>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-headset fs-2 text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-xl-8 col-lg-7">
            <!-- Download Guides Section -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-download me-2"></i>Download Panduan
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
                                            <a href="{{ route('user.panduan.download', $guide['file']) }}" 
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
                            <i class="bi bi-question-circle me-2"></i>Frequently Asked Questions (FAQ)
                        </h6>
                        <small class="text-muted">{{ $filteredFaqs->count() }} pertanyaan ditemukan</small>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <form method="GET" action="{{ route('user.panduan.index') }}" class="mb-4">
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
                                <a href="{{ route('user.panduan.index') }}" class="btn btn-sm btn-outline-secondary">
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
                                                    'pengajuan' => 'success', 
                                                    'dokumen' => 'warning',
                                                    'anggota' => 'info',
                                                    'status' => 'secondary',
                                                    'sertifikat' => 'danger'
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
                            <a href="{{ route('user.panduan.index') }}" class="btn btn-primary">
                                Lihat Semua FAQ
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
           

            <!-- Quick Links -->
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-link-45deg me-2"></i>Link Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('user.submissions.create') }}" 
                           class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-plus-circle text-success me-2"></i>Ajukan HKI Baru
                        </a>
                        <a href="{{ route('user.history.index') }}" 
                           class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-clock-history text-info me-2"></i>Riwayat Pengajuan
                        </a>
                        <a href="{{ route('user.submissions.index') }}" 
                           class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>Kelola Submission
                        </a>
                        <a href="mailto:hki@amikom.ac.id" 
                           class="list-group-item list-group-item-action border-0">
                            <i class="bi bi-envelope text-warning me-2"></i>Hubungi Admin
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="card shadow">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="bi bi-headset me-2"></i>Butuh Bantuan Lebih?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <i class="bi bi-person-badge fs-1 text-primary mb-3"></i>
                        <h6>Tim Support SiHaki</h6>
                        <p class="small text-muted mb-3">
                            Kami siap membantu Anda 24/7 untuk semua pertanyaan terkait pengajuan HKI.
                        </p>
                        
                        <div class="d-grid gap-2">
                            <a href="mailto:hki@amikom.ac.id" class="btn btn-primary btn-sm">
                                <i class="bi bi-envelope me-1"></i>Email Support
                            </a>
                            <a href="https://wa.me/6281329303450" class="btn btn-success btn-sm" target="_blank">
                                <i class="bi bi-whatsapp me-1"></i>WhatsApp
                            </a>
                        </div>
                        
                        <div class="mt-3 small text-muted">
                            <i class="bi bi-clock me-1"></i>
                            Senin-Jumat: 08:00-16:00 WIB
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

    // Smooth scroll to FAQ if coming from external link
    if (window.location.hash) {
        const target = document.querySelector(window.location.hash);
        if (target) {
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        }
    }

    // Track popular FAQ clicks
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function() {
            // Analytics tracking could be added here
            console.log('FAQ clicked:', this.textContent.trim());
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    border-color: #28a745;
}

.list-group-item-action:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.7em;
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
    }
}
</style>
@endpush
@endsection