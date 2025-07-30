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
                            <a class="nav-link" href="{{ route('panduan') }}">Panduan</a>
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
                <!-- ✅ DYNAMIC: Display actual data -->
                @foreach($submissions as $submission)
                    <div class="result-card">
                        <div class="result-card-body">
                            <div class="result-header">
                                <div class="result-info">
                                    <h5>{{ $submission->title ?? 'Judul tidak tersedia' }}</h5>
                                    <div>{{ $submission->creator ?? 'Pencipta tidak tersedia' }}</div>
                                    <div>{{ $submission->publication_date ?? 'Tanggal tidak tersedia' }}</div>
                                    <div class="mt-2">
                                        <span class="badge bg-primary">{{ $submission->type ?? 'Tipe tidak tersedia' }}</span>
                                        <span class="badge bg-secondary">{{ $submission->department ?? 'Jurusan tidak tersedia' }}</span>
                                    </div>
                                </div>
                                <div class="result-action ms-auto">
                                    <a href="{{ route('detail_ciptaan', ['id' => $submission->id]) }}" 
                                       class="btn btn-outline-primary">
                                        Lihat Detail HKI >
                                    </a>
                                </div>
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
                    <strong>Tidak ada data ditemukan</strong> atau sedang dalam tahap pengembangan.
                    <br><small>Berikut adalah contoh tampilan data:</small>
                </div>

                <!-- Example Data -->
                <div class="result-card">
                    <div class="result-card-body">
                        <div class="result-header">
                            <div class="result-info">
                                <h5>Sistem Informasi Manajemen Perpustakaan Digital</h5>
                                <div>Dr. Ahmad Fauzi, M.Kom</div>
                                <div>15 Maret 2024</div>
                                <div class="mt-2">
                                    <span class="badge bg-primary">Program Komputer</span>
                                    <span class="badge bg-secondary">Teknik Informatika</span>
                                </div>
                            </div>
                            <div class="result-action ms-auto">
                                <a href="{{ route('detail_ciptaan', ['id' => 1]) }}" 
                                   class="btn btn-outline-primary">
                                    Lihat Detail HKI >
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="result-card">
                    <div class="result-card-body">
                        <div class="result-header">
                            <div class="result-info">
                                <h5>Aplikasi Mobile Learning Bahasa Pemrograman</h5>
                                <div>Sari Dewi Lestari, S.Kom, M.T</div>
                                <div>28 Februari 2024</div>
                                <div class="mt-2">
                                    <span class="badge bg-primary">Program Komputer</span>
                                    <span class="badge bg-secondary">Sistem Informasi</span>
                                </div>
                            </div>
                            <div class="result-action ms-auto">
                                <a href="{{ route('detail_ciptaan', ['id' => 2]) }}" 
                                   class="btn btn-outline-primary">
                                    Lihat Detail HKI >
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="result-card">
                    <div class="result-card-body">
                        <div class="result-header">
                            <div class="result-info">
                                <h5>Platform E-Commerce Produk UMKM</h5>
                                <div>Budi Santoso, M.T, Ph.D</div>
                                <div>10 Januari 2024</div>
                                <div class="mt-2">
                                    <span class="badge bg-primary">Program Komputer</span>
                                    <span class="badge bg-secondary">Teknik Informatika</span>
                                </div>
                            </div>
                            <div class="result-action ms-auto">
                                <a href="{{ route('detail_ciptaan', ['id' => 3]) }}" 
                                   class="btn btn-outline-primary">
                                    Lihat Detail HKI >
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

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
        // Search functionality
        document.querySelector('.search-btn').addEventListener('click', function() {
            const searchInput = document.querySelector('#searchInput');
            const searchValue = searchInput.value.trim();
            
            if (searchValue) {
                console.log('Searching for:', searchValue);
            }
        });

        // Enter key search
        document.querySelector('#searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.search-btn').click();
            }
        });

        // Add hover effects and animations
        document.querySelectorAll('.result-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>