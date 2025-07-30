{{-- filepath: resources/views/detail_pencipta.blade.php --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>{{ $pencipta->nama }} | SiHaki</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- ✅ LOAD: Chart.js CDN di head -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Styling yang sudah ada... */
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            overflow: hidden;
            margin: 0 auto 20px;
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .statistics-section {
            background: #f8f9fa;
            padding: 40px 0;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .chart-container {
            position: relative;
            height: 350px;
            margin: 20px 0;
        }

        /* ✅ SIMPLIFIED: Simple chart styling */
        .chart-wrapper {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .stat-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .stat-info {
            flex-grow: 1;
        }

        .stat-count {
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
        }

        .summary-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Card styling untuk HKI submissions */
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

    <!-- Profile Header -->
    <section class="profile-header">
        <div class="container">
            <div class="text-center">
                <div class="profile-avatar">
                    @if($pencipta->foto && $pencipta->foto !== 'default.png')
                        <img src="{{ asset('storage/profile_photos/' . $pencipta->foto) }}" 
                            alt="{{ $pencipta->nama }}"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div style="display: none; width: 100%; height: 100%; align-items: center; justify-content: center; background: #667eea; color: white; font-size: 36px; font-weight: bold;">
                            {{ substr($pencipta->nama, 0, 2) }}
                        </div>
                    @else
                        {{ substr($pencipta->nama, 0, 2) }}
                    @endif
                </div>
                <h2 class="mb-2">{{ $pencipta->nama }}</h2>
                <p class="mb-1">{{ $pencipta->institusi }}</p>
                <p class="mb-3">{{ $pencipta->jurusan }}</p>
                <div class="badge bg-light text-dark fs-6 px-3 py-2">
                    <i class="bi bi-award me-2"></i>{{ $pencipta->total_hki }} Total Pengajuan HKI
                </div>
            </div>
        </div>
    </section>

    <!-- ✅ SIMPLIFIED: Statistics Section -->
    @if(isset($statistics) && $statistics['total_approved'] > 0)
    <section class="statistics-section">
        <div class="container">
            <h4 class="mb-4 text-center">
                <i class="bi bi-bar-chart me-2"></i>Statistik Pengajuan HKI
            </h4>
            
            <div class="row">
                <!-- Summary Card -->
                <div class="col-lg-4 mb-4">
                    <div class="summary-card">
                        <div class="summary-number">{{ $statistics['total_approved'] }}</div>
                        <h5>Total Pengajuan Disetujui</h5>
                        <p class="mb-0">
                            @if(isset($statistics['oldest_submission']) && isset($statistics['latest_submission']) && $statistics['oldest_submission'] && $statistics['latest_submission'])
                                Periode: {{ $statistics['oldest_submission']->created_at->format('Y') }} - {{ $statistics['latest_submission']->created_at->format('Y') }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- ✅ SIMPLIFIED: Chart by Type -->
                <div class="col-lg-8 mb-4">
                    <div class="stats-card">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-pie-chart me-2"></i>Pengajuan Berdasarkan Jenis Ciptaan
                        </h6>
                        <div class="chart-container">
                            <canvas id="typeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Breakdown by Type -->
                <div class="col-lg-6 mb-4">
                    <div class="stats-card">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-list-ul me-2"></i>Detail Berdasarkan Jenis
                        </h6>
                        @if(isset($statistics['by_type']) && count($statistics['by_type']) > 0)
                            @foreach($statistics['by_type'] as $typeStat)
                                <div class="stat-item">
                                    <div class="stat-color" style="background-color: {{ $typeStat['color'] ?? '#6c757d' }}"></div>
                                    <div class="stat-info">
                                        <div class="fw-bold">{{ $typeStat['name'] ?? 'Unknown' }}</div>
                                        <small class="text-muted">
                                            {{ isset($typeStat['count']) && $statistics['total_approved'] > 0 ? round(($typeStat['count'] / $statistics['total_approved']) * 100, 1) : 0 }}% dari total
                                        </small>
                                    </div>
                                    <span class="stat-count">{{ $typeStat['count'] ?? 0 }}</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted text-center py-3">
                                <i class="bi bi-info-circle me-1"></i>Tidak ada data statistik
                            </div>
                        @endif
                    </div>
                </div>

                <!-- ✅ SIMPLIFIED: Trend by Year -->
                <div class="col-lg-6 mb-4">
                    <div class="stats-card">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-graph-up me-2"></i>Tren Pengajuan per Tahun
                        </h6>
                        <div class="chart-container" style="height: 250px;">
                            <canvas id="yearChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- HKI Submissions (kode yang sudah ada...) -->
    <section class="py-5">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('beranda') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('pencipta') }}">Pencipta</a></li>
                    <li class="breadcrumb-item active">{{ $pencipta->nama }}</li>
                </ol>
            </nav>

            <h4 class="mb-4">
                <i class="bi bi-file-earmark-text me-2"></i>Daftar Pengajuan HKI ({{ $submissions->count() }} item)
            </h4>

            @if($submissions->count() > 0)
                @foreach($submissions as $submission)
                    <div class="hki-card">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="text-primary mb-3">{{ $submission->judul }}</h5>
                                
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <strong>Tipe HKI:</strong>
                                        <span class="badge bg-info ms-2">{{ $submission->tipe_hki }}</span>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Jenis HKI:</strong>
                                        <span class="badge bg-secondary ms-2">{{ $submission->jenis_hki }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <strong>Uraian Singkat:</strong>
                                    <p class="text-muted mt-1">{{ Str::limit($submission->uraian_singkat, 200) }}</p>
                                </div>

                                <div class="mb-3">
                                    <strong>Tanggal Publikasi:</strong>
                                    <span class="text-muted">{{ $submission->tanggal_publikasi }}</span>
                                </div>

                                <div class="mb-3">
                                    <strong>Pencipta Utama:</strong>
                                    <span class="text-primary">{{ $submission->pencipta_utama }}</span>
                                </div>

                                @if(count($submission->anggota_pencipta) > 0)
                                    <div class="mb-3">
                                        <strong>Anggota Pencipta:</strong>
                                        <div class="anggota-list">
                                            @foreach($submission->anggota_pencipta as $index => $anggota)
                                                <div class="anggota-item">
                                                    <div class="anggota-number">{{ $index + 1 }}</div>
                                                    <div>{{ $anggota }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-4 text-end">
                                <div class="mb-3">
                                    @if($submission->has_certificate)
                                        <span class="certificate-badge">
                                            <i class="bi bi-award me-1"></i>Tersertifikasi
                                        </span>
                                    @else
                                        <span class="no-certificate-badge">
                                            <i class="bi bi-clock me-1"></i>Belum Tersertifikasi
                                        </span>
                                    @endif
                                </div>

                                @if($submission->has_certificate)
                                    <button class="btn btn-view-certificate" 
                                            onclick="viewCertificate('{{ $submission->id }}')">
                                        <i class="bi bi-eye me-1"></i>Lihat Sertifikat
                                    </button>
                                @endif

                                <div class="mt-3 text-muted small">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Dibuat: {{ $submission->created_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-x fs-1 text-muted"></i>
                    <h5 class="mt-3 text-muted">Belum Ada Pengajuan HKI</h5>
                    <p class="text-muted">Pencipta ini belum memiliki pengajuan HKI yang disetujui</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- ✅ SIMPLIFIED: Chart initialization -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing charts...');
            
            @if(isset($statistics) && $statistics['total_approved'] > 0)
                // Check if Chart.js is loaded
                if (typeof Chart !== 'undefined') {
                    console.log('Chart.js is available, creating charts...');
                    initTypeChart();
                    initYearChart();
                } else {
                    console.error('Chart.js is not loaded');
                }
            @endif
            
            // Initialize card effects
            initCardEffects();
        });

        // ✅ SIMPLIFIED: Type Chart
        function initTypeChart() {
            const ctx = document.getElementById('typeChart');
            if (!ctx) {
                console.error('TypeChart canvas not found');
                return;
            }

            const typeData = {!! json_encode($statistics['by_type'] ?? []) !!};
            console.log('Type data:', typeData);
            
            if (!typeData || typeData.length === 0) {
                console.warn('No type data available');
                return;
            }

            try {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: typeData.map(item => item.name || 'Unknown'),
                        datasets: [{
                            data: typeData.map(item => item.count || 0),
                            backgroundColor: typeData.map(item => item.color || '#6c757d'),
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
                                    usePointStyle: true,
                                    font: { size: 12 }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.parsed * 100) / total).toFixed(1) : 0;
                                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Type chart created successfully');
            } catch (error) {
                console.error('Error creating type chart:', error);
            }
        }

        // ✅ SIMPLIFIED: Year Chart
        function initYearChart() {
            const ctx = document.getElementById('yearChart');
            if (!ctx) {
                console.error('YearChart canvas not found');
                return;
            }

            const yearData = {!! json_encode($statistics['by_year'] ?? []) !!};
            console.log('Year data:', yearData);
            
            if (!yearData || yearData.length === 0) {
                console.warn('No year data available');
                return;
            }

            try {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: yearData.map(item => item.year || 'Unknown'),
                        datasets: [{
                            label: 'Jumlah Pengajuan',
                            data: yearData.map(item => item.count || 0),
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#667eea',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8
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
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
                console.log('Year chart created successfully');
            } catch (error) {
                console.error('Error creating year chart:', error);
            }
        }

        // Initialize card effects
        function initCardEffects() {
            const cards = document.querySelectorAll('.hki-card, .stats-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        }

        // Certificate viewing function
        function viewCertificate(submissionId) {
            const url = `/sertifikat/view/${submissionId}`;
            window.open(url, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes,toolbar=no,menubar=no');
        }
    </script>
</body>
</html>