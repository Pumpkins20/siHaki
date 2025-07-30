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
    <link href="{{ asset('landing-page/css/main.css') }}" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
       
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
                            {{-- ✅ FIXED: Use correct route name --}}
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
                        <p class="lead">SiHAKI (Sistem Informasi Hak Kekayaan Intelektual), menghadirkan akses terhadap data dan pengelolaan HKI di lingkungan STMIK Amikom Surakarta</p>
                        <a href="#search" class="btn-primary-custom">
                            <i class="bi bi-search me-2"></i>
                            Mulai Pencarian
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section" id="search">
        <div class="container">
            <div class="search-container">
                <div class="search-title">
                    <h2>Daftar Ciptaan</h2>
                    <p>Pilih kriteria pencarian dan masukkan kata kunci untuk menemukan karya yang Anda cari</p>
                </div>
                
                {{-- ✅ UPDATED: Form action to search route --}}
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

                {{-- ✅ ENHANCED: Results display --}}
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
                                @foreach($ciptaans as $ciptaan)
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
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="viewDetail({{ $ciptaan->id }})" title="Lihat Detail">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

                {{-- ✅ ADD: Search suggestions --}}
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
                        <img src="{{ asset('landing-page/img/logo-footer.png') }}" alt="Logo" class="img-fluid" style="max-height: 70px;">
 </div>
                    <div class="footer-contact">
                        <p><strong>Alamat:</strong> Jl. Veteran, Notosuman, Singopuran, Kec. Kartasura,</p>
                        <p>Kabupaten Sukoharjo, Provinsi Jawa Tengah 57164</p>
                        <p><strong>Telp/Fax:</strong> (0271) 7851507</p>
                        <p><strong>Email:</strong> amikomsolo@amikomsolo.ac.id</p>
                        <p><strong>WhatsApp:</strong> 081329303450</p>
                    </div>
                </div>
                
             
            </div>
            
            <div class="footer-bottom">
                <div class="copyright">
                    © Copyright <strong>STMIK AMIKOM Surakarta</strong>. All Rights Reserved
                </div>
                <div class="social-links">
                    <a href="#"><i class="bi bi-facebook"></i></a>
                    <a href="#"><i class="bi bi-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
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

        function viewDetail(id) {
            // Implement detail view functionality
            console.log('View detail for ID:', id);
            // You can redirect to detail page or show modal
            alert('Detail untuk ciptaan ID: ' + id);
        }

        // Form validation
        document.querySelector('.search-form').addEventListener('submit', function(e) {
            const filter = document.getElementById('authorFilter').value;
            const query = document.getElementById('searchInput').value.trim();
            
            if (!filter) {
                e.preventDefault();
                alert('Silakan pilih filter pencarian terlebih dahulu');
                return false;
            }
            
            if (!query) {
                e.preventDefault();
                alert('Silakan masukkan kata kunci pencarian');
                return false;
            }
        });

        // Dynamic placeholder based on filter selection
        document.getElementById('authorFilter').addEventListener('change', function() {
            const searchInput = document.getElementById('searchInput');
            const filter = this.value;
            
            const placeholders = {
                'nama': 'Contoh: Ahmad Fauzi, Sari Dewi',
                'institusi': 'Contoh: Informatika, Manajemen Informatika',
                'judul': 'Contoh: Sistem Informasi, Aplikasi Mobile',
                'tipe': 'Contoh: Program Komputer, Sinematografi'
            };
            
            searchInput.placeholder = placeholders[filter] || 'Masukkan kata kunci pencarian...';
        });
    </script>
</body>
</html>