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
    <link href="{{ asset('landing-page/css/jenis_ciptaan.css') }}" rel="stylesheet">
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
                          <a class="nav-link" href="{{ route('beranda') }}">Beranda</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('pencipta') }}">Pencipta</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#jenis">Jenis Ciptaan</a>
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
            <form method="GET" action="{{ route('pencipta') }}">
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
 
         <!-- Content Section -->
    <section class="content-section">
        <div class="container">
            <!-- Program Komputer -->
            <div class="category-card">
                <div>
                    <h3 class="category-title">Program Komputer</h3>
                </div>
                <div class="category-stats">
                    <div class="text-center">
                        <div class="total-label">Total Pengajuan</div>
                        <div class="total-count">X</div>
                    </div>
                    <a href="{{ route('detail_jenis', ['jenis' => 'Program Komputer']) }}" class="view-btn">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- Sinematografi -->
            <div class="category-card">
                <div>
                    <h3 class="category-title">Sinematografi</h3>
                </div>
                <div class="category-stats">
                    <div class="text-center">
                        <div class="total-label">Total Pengajuan</div>
                        <div class="total-count">X</div>
                    </div>
                    <a href="{{ route('detail_jenis', ['jenis' => 'Sinematografi']) }}" class="view-btn">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>


            <!-- Pagination -->
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
                        <li class="page-item">                            <a class="page-link" href="#">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Search functionality
        document.querySelector('.search-btn').addEventListener('click', function() {
            const searchInput = document.querySelector('.search-input');
            const searchValue = searchInput.value.trim();
            
            if (searchValue) {
                // Simulate search functionality
                console.log('Searching for:', searchValue);
                // Here you would typically make an API call or filter results
            }
        });

        // Enter key search
        document.querySelector('.search-input').addEventListener('keypress', function(e) {            if (e.key === 'Enter') {
                document.querySelector('.search-btn').click();            }
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