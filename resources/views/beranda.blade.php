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
                            <a class="nav-link active"  href="#home">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pencipta') }}">Pencipta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('jenis_ciptaan') }}">Jenis Ciptaan</a>
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
                
                <form class="search-form" method="GET" action="{{ route('beranda') }}">
                    <div class="form-group">
                        <label for="authorFilter">Filter Pencarian</label>
                        <select id="authorFilter" name="filter" class="form-select">
                            <option value="">-- Pilih Filer Pencarian --</option>
                            <!-- jika dicari berdasarkan nama pencipta atau jurusan ntar dialihkan ke menu pencipta.blade dan data akan muncul disana
                            sedangkan jika dicari berdasarkan judul atau tipe ciptaan akan dialihkan ke menu jenis_ciptaa.blade dan data akan muncul -->

                            <option value="nama" {{ request('filter') == 'nama' ? 'selected' : '' }}>Berdasarkan Nama Pencipta</option>
                            <option value="institusi" {{ request('filter') == 'institusi' ? 'selected' : '' }}>Berdasarkan Jurusan</option>
                            <option value="judul" {{ request('filter') == 'judul' ? 'selected' : '' }}>Berdasarkan Judul Ciptaan</option>
                            <option value="tipe" {{ request('filter') == 'tipe' ? 'selected' : '' }}>Berdasarkan Tipe Ciptaan</option>
                        </select>
                    </div>
                    
                    <div class="form-group" style="flex: 2;">
                        <label for="searchInput">Search</label>
                        <input type="text" class="form-control" id="searchInput" name="q" value="{{ request('q') }}" placeholder="Masukkan kata kunci pencarian...">
                    </div>
                    
                    <button class="search-btn" type="submit">
                        <i class="bi bi-search me-2"></i>
                        Cari
                    </button>
                </form>

                @if(isset($ciptaans) && $ciptaans->count())
                    <div class="mt-4">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Pencipta</th>
                                    <th>Jurusan</th>
                                    <th>Tipe</th>
                                    <th>Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ciptaans as $ciptaan)
                                    <tr>
                                        <td>
                                            @if(request('filter') == 'judul' || request('filter') == 'tipe')
                                                <a href="{{ route('ciptaan.show', $ciptaan->id) }}">{{ $ciptaan->judul }}</a>
                                            @else
                                                {{ $ciptaan->judul }}
                                            @endif
                                        </td>
                                        <td>
                                            @if(request('filter') == 'nama' || request('filter') == 'institusi')
                                                <a href="{{ route('pencipta.show', $ciptaan->pencipta_id) }}">{{ $ciptaan->pencipta }}</a>
                                            @else
                                                {{ $ciptaan->pencipta }}
                                            @endif
                                        </td>
                                        <td>{{ $ciptaan->jurusan }}</td>
                                        <td>{{ $ciptaan->tipe }}</td>
                                        <td>{{ $ciptaan->tahun }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $ciptaans->withQueryString()->links() }}
                    </div>
                @elseif(request()->has('q'))
                    <div class="alert alert-warning mt-4">Data tidak ditemukan.</div>
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
    </script>
</body>
</html>