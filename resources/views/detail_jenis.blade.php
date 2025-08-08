<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>SiHaki | Sistem Informasi Hak Kekayaan Intelektual</title>
    <meta name="description" content="Sistem Informasi Hak Kekayaan Intelektual STMIK AMIKOM Surakarta">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('landing-page/css/detail_jenis.css') }}" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #AE4FA8;
            --secondary-color: #F9A02F;
            --accent-color: #96CEB4;
            --text-dark: #2C3E50;
            --text-light: #7F8C8D;
            --bg-light: #F8F9FA;
            --gradient-purple: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            background: #fff;
        }

        /* Header */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand img {
            height: 45px;
        }

        .navbar-nav .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 15px;
            transition: color 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color) !important;
        }

        /* Search Section */
        .search-section {
            background: var(--bg-light);
            padding: 20px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .search-title h3 {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 15px;
        }

        .search-title p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        /* Form styling untuk layout horizontal */
        .search-section form {
            background: var(--bg-light);
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: end;
            gap: 15px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group.search-input-group {
            flex: 2;
            min-width: 250px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .form-control, .form-select {
            border: 2px solid #E9ECEF;
            border-radius: 10px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            width: 100%;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(174, 79, 168, 0.25);
            outline: none;
        }

        .search-btn {
            background: var(--primary-color);
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            height: fit-content;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .search-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Results Section */
        .results-section {
            padding: 40px 0;
            background: #f8f9fa;
            min-height: 60vh;
        }

        /* ✅ IDENTICAL: HKI Card styling sama persis dengan detail_pencipta */
        .hki-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border-left: 4px solid #667eea;
        }

        .hki-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* ✅ IDENTICAL: Certificate badges */
        .certificate-badge {
            background: #27ae60;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .no-certificate-badge {
            background: #95a5a6;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        /* ✅ IDENTICAL: Button styles */
        .btn-view-certificate {
            background: #27ae60;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .btn-view-certificate:hover {
            background: #219a52;
            color: white;
            transform: translateY(-2px);
        }

        /* ✅ IDENTICAL: Anggota pencipta styling */
        .anggota-list {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .anggota-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .anggota-item:last-child {
            border-bottom: none;
        }

        .anggota-number {
            background: #667eea;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            margin-right: 12px;
        }

        /* ✅ IDENTICAL: Breadcrumb styling */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 1rem;
        }

        .breadcrumb-item {
            font-size: 0.9rem;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .breadcrumb-item.active {
            color: var(--text-light);
        }

        /* ✅ IDENTICAL: Pagination styling */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            padding-top: 20px;
        }

        .pagination {
            margin: 0;
        }

        .pagination .page-item {
            margin: 0 4px;
        }

        .pagination .page-link {
            border: 2px solid #e9ecef;
            color: var(--text-dark);
            background-color: white;
            padding: 10px 15px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .pagination .page-link:hover {
            border-color: var(--primary-color);
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(174, 79, 168, 0.3);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 12px rgba(174, 79, 168, 0.3);
        }

        .pagination .page-item.active .page-link:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
        }

        /* ✅ IDENTICAL: Alert styling */
        .alert {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            color: var(--text-dark);
            border-left: 4px solid var(--primary-color);
        }

        /* ✅ IDENTICAL: Badge styling */
        .badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.6rem;
            border-radius: 6px;
            font-weight: 500;
        }

        .badge.bg-info {
            background-color: #17a2b8 !important;
        }

        .badge.bg-secondary {
            background-color: #6c757d !important;
        }

        .badge.bg-success {
            background-color: #28a745 !important;
        }

        /* ✅ IDENTICAL: Typography */
        h4 {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-muted {
            color: var(--text-light) !important;
        }

        /* ✅ IDENTICAL: Button variations */
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            background-color: transparent;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
        }

        /* ✅ IDENTICAL: Responsive design */
        @media (max-width: 768px) {
            .search-section form {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-group,
            .form-group.search-input-group {
                flex: none;
                min-width: auto;
                width: 100%;
            }
            
            .search-btn {
                width: 100%;
                margin-top: 10px;
            }
            
            .hki-card {
                padding: 20px;
            }
            
            .hki-card .row {
                flex-direction: column;
            }
            
            .hki-card .col-md-4 {
                text-align: left !important;
                margin-top: 15px;
            }
            
            .pagination .page-link {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
            
            .pagination .page-item {
                margin: 0 2px;
            }
        }

        @media (max-width: 576px) {
            .results-section {
                padding: 20px 0;
            }
            
            .hki-card {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .anggota-list {
                padding: 10px;
            }
            
            .anggota-number {
                width: 20px;
                height: 20px;
                font-size: 10px;
            }
        }

        /* ✅ IDENTICAL: Loading states */
        .loading-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid #e9ecef;
        }

        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        /* ✅ IDENTICAL: Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }

        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 1.1rem;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <img src="{{ asset('landing-page/img/logo-amikom.png') }}" alt="Logo AMIKOM" style="height: 40px;">
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('beranda') }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pencipta') }}">Pencipta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('jenis_ciptaan') }}">Jenis Ciptaan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Search Section -->
    <section class="search-section py-4">
        <div class="container">
            <form method="GET" action="{{ route('jenis_ciptaan') }}">
                <!-- Dropdown Cari Berdasarkan -->
                <div class="form-group">
                    <label for="searchBy">Cari Berdasarkan</label>
                    <select class="form-select" id="searchBy" name="search_by">
                        <option value="jenis_ciptaan" {{ request('search_by') == 'jenis_ciptaan' ? 'selected' : '' }}>Jenis Ciptaan</option>
                        <option value="judul_ciptaan" {{ request('search_by') == 'judul_ciptaan' ? 'selected' : '' }}>Judul Ciptaan</option>
                    </select>
                </div>

                <!-- Input Pencarian -->
                <div class="form-group search-input-group">
                    <label for="searchInput" id="searchLabel">Cari</label>
                    <input type="text" class="form-control" id="searchInput" name="q" value="{{ request('q') }}" placeholder="Masukkan kata kunci pencarian...">
                </div>

                <!-- Tombol Cari -->
                <button class="search-btn" type="submit">
                    <i class="bi bi-search"></i>
                    Cari
                </button>
            </form>
        </div>
    </section>

    <!-- Results Section -->
    <section class="results-section">
        <div class="container">
            @if(isset($submissions) && $submissions->count() > 0)
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jenis_ciptaan') }}">Jenis Ciptaan</a></li>
                        <li class="breadcrumb-item active">
                            {{ isset($type) ? ucfirst(str_replace('_', ' ', $type)) : 'Hasil Pencarian' }}
                        </li>
                    </ol>
                </nav>

                <h4 class="mb-4">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    @if(isset($type))
                        Daftar {{ ucfirst(str_replace('_', ' ', $type)) }} ({{ $submissions->total() ?? $submissions->count() }} item)
                    @else
                        Hasil Pencarian ({{ $submissions->total() ?? $submissions->count() }} item)
                    @endif
                </h4>

                <!-- ✅ NEW: HKI Cards sama seperti detail_pencipta -->
                @foreach($submissions as $submission)
                    <div class="hki-card">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="text-primary mb-3">{{ $submission->title ?? 'Tidak ada judul' }}</h5>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <strong>Tipe HKI:</strong>
                                        <span class="badge bg-info ms-2">{{ ucfirst($submission->type ?? 'Unknown') }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Jenis HKI:</strong>
                                        <span class="badge bg-secondary ms-2">{{ ucfirst(str_replace('_', ' ', $submission->creation_type ?? 'unknown')) }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <strong>Uraian Singkat:</strong>
                                    <p class="text-muted mt-1">{{ Str::limit($submission->description ?? 'Tidak ada deskripsi', 200) }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Tanggal Publikasi:</strong>
                                    <span class="text-muted">
                                        {{ $submission->first_publication_date 
                                            ? $submission->first_publication_date->format('d M Y') 
                                            : ($submission->created_at ? $submission->created_at->format('d M Y') : 'Tidak diketahui') }}
                                    </span>
                                </div>

                               <!--  <div class="mb-3">
                                    <strong>Pencipta Utama:</strong>
                                    <span class="text-primary">
                                        {{ $submission->members->where('is_leader', true)->first()->name ?? $submission->user->nama ?? 'Tidak diketahui' }}
                                    </span>
                                </div> -->

                                {{-- ✅ IDENTICAL: Anggota pencipta section sama persis dengan detail_pencipta --}}
                                @if($submission->members->where('is_leader', false)->count() > 0)
                                    <div class="mb-3">
                                        <strong>Anggota Pencipta:</strong>
                                        <div class="anggota-list">
                                            @foreach($submission->members->where('is_leader', false) as $index => $member)
                                                <div class="anggota-item">
                                                    <div class="anggota-number">{{ $index + 1 }}</div>
                                                    <div>
                                                        {{ $member->name }}
                                                        @if($index === 0)
                                                            <span class="badge bg-primary ms-2">(PENCIPTA UTAMA)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- ✅ IDENTICAL: Right column sama persis dengan detail_pencipta --}}
                            <div class="col-md-4 text-end">
                                @php
                                    $certificate = $submission->documents->where('document_type', 'certificate')->first();
                                @endphp
                                
                                <div class="mb-3">
                                    @if($certificate)
                                        <span class="certificate-badge">
                                            <i class="bi bi-award me-1"></i>Tersertifikasi
                                        </span>
                                    @else
                                        <span class="no-certificate-badge">
                                            <i class="bi bi-clock me-1"></i>Sertifikat Sedang Diproses
                                        </span>
                                    @endif
                                </div>

                                @if($certificate)
                                    <button class="btn btn-view-certificate" 
                                            onclick="viewCertificate('{{ $submission->id }}')">
                                        <i class="bi bi-eye me-1"></i>Lihat Sertifikat
                                    </button>
                                @else
                                    <button class="btn btn-secondary" disabled>
                                        <i class="bi bi-hourglass-split me-1"></i>Sertifikat Belum Tersedia
                                    </button>
                                @endif

                                <div class="mt-3 text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Dibuat: {{ $submission->created_at->format('d M Y') }}
                                </div>
                                
                                <!--<div class="mt-2">
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Disetujui
                                    </span>
                                </div> -->

                                <!-- Detail Button 
                                <div class="mt-3">
                                    <a href="{{ route('detail_ciptaan', ['id' => $submission->id]) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Lihat Detail Lengkap
                                    </a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                @if($submissions->hasPages())
                    <div class="pagination-wrapper">
                        <nav aria-label="Page navigation">
                            {{ $submissions->appends(request()->query())->links('custom.pagination') }}
                        </nav>
                    </div>
                @endif
            @else
                <!-- ✅ FALLBACK: Static example data when no results -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Tidak ada data ditemukan</strong> 
                    <!--<br><small>Berikut adalah contoh tampilan data:</small> -->
                </div>

                <!--
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('jenis_ciptaan') }}">Jenis Ciptaan</a></li>
                        <li class="breadcrumb-item active">Contoh Data</li>
                    </ol>
                </nav>
                
                <h4 class="mb-4">
                    <i class="bi bi-file-earmark-text me-2"></i>Contoh Pengajuan HKI (3 item)
                </h4>

                
                <div class="hki-card">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="text-primary mb-3">Sistem Informasi Manajemen Perpustakaan Digital</h5>
                            
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Tipe HKI:</strong>
                                    <span class="badge bg-info ms-2">Copyright</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Jenis HKI:</strong>
                                    <span class="badge bg-secondary ms-2">Program Komputer</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Uraian Singkat:</strong>
                                <p class="text-muted mt-1">Sistem informasi berbasis web untuk mengelola koleksi perpustakaan digital dengan fitur pencarian, peminjaman, dan manajemen koleksi buku elektronik.</p>
                            </div>

                            <div class="mb-3">
                                <strong>Tanggal Publikasi:</strong>
                                <span class="text-muted">15 Maret 2024</span>
                            </div>

                            <div class="mb-3">
                                <strong>Pencipta Utama:</strong>
                                <span class="text-primary">Dr. Ahmad Fauzi, M.Kom</span>
                            </div> --

                            <div class="mb-3">
                                <strong>Anggota Pencipta:</strong>
                                <div class="anggota-list">
                                    <div class="anggota-item">
                                        <div class="anggota-number">1</div>
                                        <div>Sari Dewi Lestari, S.Kom</div>
                                    </div>
                                    <div class="anggota-item">
                                        <div class="anggota-number">2</div>
                                        <div>Budi Santoso, M.T</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 text-end">
                            <div class="mb-3">
                                <span class="certificate-badge">
                                    <i class="bi bi-award me-1"></i>Tersertifikasi
                                </span>
                            </div>

                            <button class="btn btn-success btn-view-certificate" 
                                    onclick="viewCertificate('1')">
                                <i class="bi bi-eye me-1"></i>Lihat Sertifikat
                            </button>

                            <div class="mt-3 text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>
                                Dibuat: 15 Mar 2024
                            </div>
                            
                            <div class="mt-2">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Disetujui
                                </span>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('detail_ciptaan', ['id' => 1]) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>Lihat Detail Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hki-card">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="text-primary mb-3">Aplikasi Mobile Learning Bahasa Pemrograman</h5>
                            
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Tipe HKI:</strong>
                                    <span class="badge bg-info ms-2">Copyright</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Jenis HKI:</strong>
                                    <span class="badge bg-secondary ms-2">Program Komputer</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Uraian Singkat:</strong>
                                <p class="text-muted mt-1">Aplikasi mobile untuk pembelajaran bahasa pemrograman dengan fitur interaktif, quiz, dan modul pembelajaran yang dapat diakses secara offline.</p>
                            </div>

                            <div class="mb-3">
                                <strong>Tanggal Publikasi:</strong>
                                <span class="text-muted">28 Februari 2024</span>
                            </div>

                            <div class="mb-3">
                                <strong>Pencipta Utama:</strong>
                                <span class="text-primary">Sari Dewi Lestari, S.Kom, M.T</span>
                            </div>
                        </div>

                        <div class="col-md-4 text-end">
                            <div class="mb-3">
                                <span class="no-certificate-badge">
                                    <i class="bi bi-clock me-1"></i>Sertifikat Sedang Diproses
                                </span>
                            </div>

                            <button class="btn btn-secondary" disabled>
                                <i class="bi bi-hourglass-split me-1"></i>Sertifikat Belum Tersedia
                            </button>

                            <div class="mt-3 text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>
                                Dibuat: 28 Feb 2024
                            </div>
                            
                            <div class="mt-2">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Disetujui
                                </span>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('detail_ciptaan', ['id' => 2]) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>Lihat Detail Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hki-card">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="text-primary mb-3">Platform E-Commerce Produk UMKM</h5>
                            
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <strong>Tipe HKI:</strong>
                                    <span class="badge bg-info ms-2">Copyright</span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Jenis HKI:</strong>
                                    <span class="badge bg-secondary ms-2">Program Komputer</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <strong>Uraian Singkat:</strong>
                                <p class="text-muted mt-1">Platform e-commerce khusus untuk produk UMKM dengan fitur marketplace, payment gateway, dan sistem manajemen inventory terintegrasi.</p>
                            </div>

                            <div class="mb-3">
                                <strong>Tanggal Publikasi:</strong>
                                <span class="text-muted">10 Januari 2024</span>
                            </div>

                            <div class="mb-3">
                                <strong>Pencipta Utama:</strong>
                                <span class="text-primary">Budi Santoso, M.T, Ph.D</span>
                            </div>
                        </div>

                        <div class="col-md-4 text-end">
                            <div class="mb-3">
                                <span class="certificate-badge">
                                    <i class="bi bi-award me-1"></i>Tersertifikasi
                                </span>
                            </div>

                            <button class="btn btn-view-certificate" 
                                    onclick="viewCertificate('3')">
                                <i class="bi bi-eye me-1"></i>Lihat Sertifikat
                            </button>

                            <div class="mt-3 text-muted small">
                                <i class="bi bi-calendar3 me-1"></i>
                                Dibuat: 10 Jan 2024
                            </div>
                            
                            <div class="mt-2">
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Disetujui
                                </span>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('detail_ciptaan', ['id' => 3]) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>Lihat Detail Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                -->
                <!-- Static Pagination -->
                <div class="pagination-wrapper">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            <li class="page-item active">
                                <a class="page-link" href="#">1</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ✅ IDENTICAL: Certificate viewing function sama dengan detail_pencipta
        function viewCertificate(submissionId) {
            const url = `/sertifikat/view/${submissionId}`;
            console.log('Opening certificate:', url);
            
            // Try to open certificate
            const certificateWindow = window.open(url, 'certificate', 'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no');
            
            // Check if popup was blocked
            if (!certificateWindow || certificateWindow.closed || typeof certificateWindow.closed == 'undefined') {
                // Popup blocked, show alternative
                if (confirm('Popup diblokir. Buka sertifikat di tab baru?')) {
                    window.open(url, '_blank');
                }
            }
            
            // Handle errors (if the window loads but shows error)
            setTimeout(() => {
                try {
                    if (certificateWindow && !certificateWindow.closed) {
                        certificateWindow.addEventListener('error', function() {
                            alert('Terjadi kesalahan saat memuat sertifikat. Silakan coba lagi nanti.');
                            certificateWindow.close();
                        });
                    }
                } catch (e) {
                    // Cross-origin error, ignore
                    console.log('Cross-origin window access blocked (normal behavior)');
                }
            }, 1000);
        }

        // ✅ IDENTICAL: Card effects sama dengan detail_pencipta
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchBtn = document.querySelector('.search-btn');
            const searchInput = document.querySelector('#searchInput');
            
            if (searchBtn) {
                searchBtn.addEventListener('click', function() {
                    const searchValue = searchInput.value.trim();
                    console.log('Searching for:', searchValue);
                });
            }

            // Enter key search
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchBtn.click();
                    }
                });
            }

            // ✅ IDENTICAL: Card hover effects sama persis dengan detail_pencipta
            document.querySelectorAll('.hki-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // ✅ IDENTICAL: Button hover effects
            document.querySelectorAll('.btn-view-certificate, .btn-outline-primary').forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    if (!this.disabled) {
                        this.style.transform = 'translateY(-1px)';
                    }
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>