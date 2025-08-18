<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Beranda | Sihaki STMIK AMIKOM Surakarta</title>
    <meta name="description" content="Sistem Informasi Hak Kekayaan Intelektual STMIK AMIKOM Surakarta">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('landing-page/css/main.css') }}" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* ✅ FIXED: Statistics section styles - prevent overflow */
        .statistics-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 60px 0;
            color: white;
            overflow: hidden; /* ✅ PREVENT OVERFLOW */
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            max-height: 200px; /* ✅ LIMIT HEIGHT */
        }

        .stat-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 5px;
            line-height: 1.2; /* ✅ FIXED LINE HEIGHT */
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 10px;
            line-height: 1.3; /* ✅ FIXED LINE HEIGHT */
        }

        .stat-description {
            font-size: 0.875rem;
            opacity: 0.8;
            line-height: 1.3; /* ✅ FIXED LINE HEIGHT */
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            max-height: 400px; /* ✅ LIMIT CHART HEIGHT */
            overflow: hidden; /* ✅ PREVENT OVERFLOW */
        }

        .recent-submissions-section {
            padding: 60px 0;
            background: #f8f9fa;
            overflow: hidden; /* ✅ PREVENT OVERFLOW */
        }

        .submission-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            max-height: 150px; /* ✅ LIMIT HEIGHT */
            overflow: hidden; /* ✅ PREVENT OVERFLOW */
        }

        .submission-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .contributors-section {
            padding: 60px 0;
            overflow: hidden; /* ✅ PREVENT OVERFLOW */
        }

        .contributor-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            max-height: 250px; /* ✅ LIMIT HEIGHT */
            overflow: hidden; /* ✅ PREVENT OVERFLOW */
        }

        .contributor-card:hover {
            transform: translateY(-5px);
        }

        .contributor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
            margin: 0 auto 15px auto;
            overflow: hidden;
            flex-shrink: 0; /* ✅ PREVENT SHRINKING */
        }

        .contributor-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .type-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            color: white;
            margin: 2px;
            white-space: nowrap; /* ✅ PREVENT WRAPPING */
        }

        /* ✅ NEW: Container limits to prevent infinite scrolling */
        .container {
            max-width: 1200px;
            overflow: hidden;
        }

        .row {
            margin-left: -15px;
            margin-right: -15px;
        }

        .col-lg-3, .col-lg-4, .col-lg-6, .col-md-6 {
            padding-left: 15px;
            padding-right: 15px;
            margin-bottom: 30px;
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
                            <a class="nav-link active" href="#home">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pencipta') }}">Pencipta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('jenis_ciptaan') }}">Jenis Ciptaan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="hero-content">
                        <h1>SiHaki</h1>
                        <h2>STMIK AMIKOM Surakarta</h2>
                        <p class="lead">SiHAKI (Sistem Informasi Hak Kekayaan Intelektual), menghadirkan akses terhadap data dan pengelolaan HKI di lingkungan STMIK AMIKOM Surakarta</p>
                        <a href="#search" class="btn-primary-custom">
                            <i class="bi bi-search me-2"></i>
                            Mulai Pencarian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ✅ FIXED: Statistics Section with proper limits -->
    @if(isset($statistics) && !empty($statistics))
    <section class="statistics-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Statistik HKI AMIKOM Surakarta</h2>
                <p class="lead opacity-90">Data keseluruhan pengajuan Hak Kekayaan Intelektual</p>
            </div>

            <!-- Main Statistics -->
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($statistics['total_submissions']) }}</div>
                        <div class="stat-label">Total Pengajuan</div>
                        <div class="stat-description">Keseluruhan pengajuan HKI</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($statistics['approved_submissions']) }}</div>
                        <div class="stat-label">Disetujui</div>
                        <div class="stat-description">Pengajuan yang telah disetujui</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number">{{ number_format($statistics['total_users']) }}</div>
                        <div class="stat-label">Pencipta Aktif</div>
                        <div class="stat-description">Dosen dengan HKI disetujui</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-number">{{ $statistics['approval_rate'] }}%</div>
                        <div class="stat-label">Tingkat Persetujuan</div>
                        <div class="stat-description">Persentase pengajuan disetujui</div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            @if(isset($statistics['by_type']) && $statistics['by_type']->count() > 0)
            <div class="row">
                <div class="col-lg-6">
                    <div class="chart-container">
                        <h5 class="mb-4 text-dark">
                            <i class="bi bi-pie-chart me-2"></i>Distribusi Berdasarkan Jenis
                        </h5>
                        <canvas id="typeChart" height="300"></canvas>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="chart-container">
                        <h5 class="mb-4 text-dark">
                            <i class="bi bi-bar-chart me-2"></i>Tren Pengajuan 5 Tahun Terakhir
                        </h5>
                        <canvas id="yearChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    <!-- ✅ FIXED: Recent Submissions Section with limits -->
    @if(isset($statistics['recent_submissions']) && $statistics['recent_submissions']->count() > 0)
    <section class="recent-submissions-section">
        <div class="container">
            <div class="text-center mb-5">
                <h3 class="fw-bold">Pengajuan HKI Terbaru</h3>
                <p class="text-muted">Daftar pengajuan yang baru saja disetujui</p>
            </div>

            <div class="row">
                @foreach($statistics['recent_submissions']->take(6) as $submission)
                <div class="col-lg-4 col-md-6">
                    <div class="submission-item">
                        <h6 class="fw-bold text-primary mb-2">{{ Str::limit($submission['title'], 50) }}</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-person text-muted me-2"></i>
                            <span class="text-muted">{{ $submission['user_name'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="type-badge" style="background-color: {{ $submission['creation_type_color'] }};">
                                {{ $submission['creation_type'] }}
                            </span>
                            <small class="text-muted">{{ $submission['year'] }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center">
                <a href="{{ route('jenis_ciptaan') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-right me-2"></i>Lihat Semua Pengajuan
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- ✅ FIXED: Top Contributors Section with limits -->
    @if(isset($statistics['top_contributors']) && $statistics['top_contributors']->count() > 0)
    <section class="contributors-section">
        <div class="container">
            <div class="text-center mb-5">
                <h3 class="fw-bold">Pencipta Terdepan</h3>
                <p class="text-muted">Dosen dengan kontribusi HKI terbanyak</p>
            </div>

            <div class="row">
                @foreach($statistics['top_contributors']->take(5) as $contributor)
                <div class="col-lg-4 col-md-6">
                    <div class="contributor-card">
                        <div class="contributor-avatar">
                            @if($contributor->foto && $contributor->foto !== 'default.png')
                                <img src="{{ asset('storage/profile_photos/' . $contributor->foto) }}" 
                                     alt="{{ $contributor->nama }}"
                                     onerror="this.style.display='none'; this.parentElement.innerHTML='{{ substr($contributor->nama, 0, 2) }}';">
                            @else
                                {{ substr($contributor->nama, 0, 2) }}
                            @endif
                        </div>
                        <h6 class="fw-bold">{{ $contributor->nama }}</h6>
                        <p class="text-muted small mb-2">{{ $contributor->program_studi }}</p>
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="badge bg-primary">{{ $contributor->total_submissions }} HKI</span>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('detail_pencipta', $contributor->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="text-center">
                <a href="{{ route('pencipta') }}" class="btn btn-outline-primary">
                    <i class="bi bi-people me-2"></i>Lihat Semua Pencipta
                </a>
            </div>
        </div>
    </section>
    @endif

    <!-- Search Section -->
    <section class="search-section" id="search">
        <div class="container">
            <div class="search-container">
                <div class="search-title">
                    <h2>Pencarian Ciptaan</h2>
                    <p>Pilih kriteria pencarian dan masukkan kata kunci untuk menemukan karya yang Anda cari</p>
                </div>
                
                <form class="search-form" method="POST" action="{{ route('public.search') }}">
                    @csrf
                    <div class="form-group">
                        <label for="authorFilter">Filter Pencarian</label>
                        <select id="authorFilter" name="filter" class="form-select" required>
                            <option value="">-- Pilih Filter Pencarian --</option>
                            <option value="nama" {{ request('filter') == 'nama' ? 'selected' : '' }}>Berdasarkan Nama Pencipta</option>
                            <option value="institusi" {{ request('filter') == 'institusi' ? 'selected' : '' }}>Berdasarkan Jurusan</option>
                            <option value="judul" {{ request('filter') == 'judul' ? 'selected' : '' }}>Berdasarkan Judul Ciptaan</option>
                            <option value="tipe" {{ request('filter') == 'tipe' ? 'selected' : '' }}>Berdasarkan Tipe Ciptaan</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="flex: 2;">
                        <label for="searchInput">Search</label>
                        <input type="text" class="form-control" id="searchInput" name="q" 
                               value="{{ request('q') }}" placeholder="Masukkan kata kunci pencarian..." required>
                    </div>
                    
                    <button class="search-btn" type="submit">
                        <i class="bi bi-search me-2"></i>
                        Cari
                    </button>
                </form>

                {{-- Search Results --}}
                @if(isset($ciptaans) && $ciptaans->count())
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Menampilkan <strong>{{ $ciptaans->count() }}</strong> hasil pencarian
                            @if(request('q'))
                                untuk "<strong>{{ request('q') }}</strong>"
                            @endif
                            @if(request('filter'))
                                dengan filter <strong>{{ ucfirst(request('filter')) }}</strong>
                            @endif
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Pencipta</th>
                                        <th>Jurusan</th>
                                        <th>Tipe</th>
                                        <th>Tahun</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ciptaans->take(20) as $ciptaan)
                                        <tr>
                                            <td>
                                                <strong>{{ $ciptaan->judul }}</strong>
                                                @if(request('filter') == 'judul')
                                                    <br><small class="text-success">
                                                        <i class="bi bi-check-circle"></i> Sesuai pencarian judul
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $ciptaan->pencipta }}
                                                @if(request('filter') == 'nama')
                                                    <br><small class="text-success">
                                                        <i class="bi bi-check-circle"></i> Sesuai pencarian nama
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $ciptaan->jurusan }}
                                                @if(request('filter') == 'institusi')
                                                    <br><small class="text-success">
                                                        <i class="bi bi-check-circle"></i> Sesuai pencarian jurusan
                                                    </small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $ciptaan->tipe }}</span>
                                                @if(request('filter') == 'tipe')
                                                    <br><small class="text-success">
                                                        <i class="bi bi-check-circle"></i> Sesuai pencarian tipe
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $ciptaan->tahun }}</td>
                                            <td>
                                                <a href="{{ route('detail_ciptaan', $ciptaan->id) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @elseif(request()->has('q'))
                    <div class="alert alert-warning mt-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Data tidak ditemukan</strong> untuk pencarian "{{ request('q') }}"
                        @if(request('filter'))
                            dengan filter {{ ucfirst(request('filter')) }}
                        @endif
                        <br><small>Coba gunakan kata kunci yang berbeda atau filter pencarian lainnya.</small>
                    </div>
                @endif

                {{-- Search suggestions --}}
                @if(!request()->has('q'))
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-person me-2 text-primary"></i>Cari Berdasarkan Pencipta
                                        </h6>
                                        <p class="card-text small">Temukan karya berdasarkan nama pencipta atau jurusan</p>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-light text-dark">Nama Pencipta</span>
                                            <span class="badge bg-light text-dark">Program Studi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <i class="bi bi-file-text me-2 text-success"></i>Cari Berdasarkan Karya
                                        </h6>
                                        <p class="card-text small">Temukan karya berdasarkan judul atau tipe ciptaan</p>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-light text-dark">Judul Ciptaan</span>
                                            <span class="badge bg-light text-dark">Tipe Ciptaan</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="footer-logo">
                        <img src="{{ asset('landing-page/img/logo-footer.png') }}" alt="Logo" class="img-fluid" style="max-height: 90px;">
                    </div>
                    <div class="footer-contact">
                        <p><strong>Alamat:</strong> Jl. Veteran, Notosuman, Singopuran, Kec. Kartasura,</p>
                        <p>Kabupaten Sukoharjo, Provinsi Jawa Tengah 57164</p>
                        <p><strong>Instagram:</strong> 
                        <a href="https://www.instagram.com/lppm_amikomsolo" target="_blank" style="color: inherit; text-decoration: none;">
                            @lppm_amikomsolo
                        </a></p>
                        <p><strong>Email:</strong> 
                        <a href="mailto:lppm@amikomsolo.ac.id" style="color: inherit; text-decoration: none;">
                            lppm@amikomsolo.ac.id
                        </a></p>
                        <p><strong>WhatsApp:</strong> 
                        <a href="https://wa.me/6289504696000" target="_blank" style="color: inherit; text-decoration: none;">
                            089504696000
                        </a></p>

                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="copyright">
                    © Copyright <strong>STMIK AMIKOM Surakarta</strong>. All Rights Reserved
                </div>
                <div class="social-links">
                   <a href="https://wa.me/6289504696000" target="_blank" style="color: inherit; text-decoration: none;">
                    <i class="bi bi-whatsapp"></i>
                    </a>
                    <a href="https://www.instagram.com/lppm_amikomsolo" target="_blank" style="color: inherit; text-decoration: none;">
                    <i class="bi bi-instagram"></i>
                    </a>

                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // ✅ FIXED: Initialize charts with error handling
        document.addEventListener('DOMContentLoaded', function() {
            try {
                @if(isset($statistics) && isset($statistics['by_type']) && $statistics['by_type']->count() > 0)
                    initTypeChart();
                    initYearChart();
                @endif
            } catch (error) {
                console.error('Error initializing charts:', error);
            }
        });

        // ✅ FIXED: Type distribution chart with error handling
        function initTypeChart() {
            try {
                const ctx = document.getElementById('typeChart');
                if (!ctx) return;

                const typeData = @json($statistics['by_type'] ?? []);
                
                if (!typeData || typeData.length === 0) {
                    console.warn('No type data available');
                    return;
                }
                
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: typeData.map(item => item.name),
                        datasets: [{
                            data: typeData.map(item => item.count),
                            backgroundColor: typeData.map(item => item.color),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error creating type chart:', error);
            }
        }

        // ✅ FIXED: Year trend chart with error handling
        function initYearChart() {
            try {
                const ctx = document.getElementById('yearChart');
                if (!ctx) return;

                const yearData = @json($statistics['by_year'] ?? []);
                
                if (!yearData || yearData.length === 0) {
                    console.warn('No year data available');
                    return;
                }
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: yearData.map(item => item.year),
                        datasets: [{
                            label: 'Jumlah Pengajuan',
                            data: yearData.map(item => item.count),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#667eea',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error creating year chart:', error);
            }
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Search functionality
        function performSearch() {
            const authorFilter = document.getElementById('authorFilter').value;
            const searchInput = document.getElementById('searchInput').value;
            
            if (!searchInput.trim()) {
                alert('Silakan masukkan kata kunci pencarian');
                return;
            }
            
            // Simulate search functionality
            console.log('Search Parameters:', {
                author: authorFilter,
                keyword: searchInput
            });
            
            // You can implement actual search logic here
            alert(`Mencari: "${searchInput}" dengan filter: ${authorFilter || 'Semua'}`);
        }

        // Enter key search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch();
            }
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
            } else {
                header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            }
        });
    </script>
</body>
</html>