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
            @if(isset($results) && $results->count())
                <div class="mb-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Menampilkan <strong>{{ $results->count() }}</strong> hasil pencarian
                        @if(isset($query) && $query)
                            untuk "<strong>{{ $query }}</strong>"
                        @endif
                        @if(isset($searchBy))
                            berdasarkan <strong>{{ $searchBy === 'jenis_ciptaan' ? 'Jenis Ciptaan' : 'Judul Ciptaan' }}</strong>
                        @endif
                    </div>
                </div>
                
                @foreach($results as $result)
                    <div class="category-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="category-title">{{ $result->type_name }}</h3>
                                <p class="category-description">{{ $result->description }}</p>
                                <div class="mt-2">
                                    <span class="badge bg-primary">{{ $result->count }} Karya</span>
                                    @if(isset($query) && $searchBy === 'jenis_ciptaan' && stripos($result->type, $query) !== false)
                                        <span class="badge bg-success">Sesuai pencarian jenis</span>
                                    @endif
                                </div>
                            </div>
                            <div class="category-icon">
                                @switch($result->type)
                                    @case('program_komputer')
                                        <i class="bi bi-code-slash"></i>
                                        @break
                                    @case('sinematografi')
                                        <i class="bi bi-camera-video"></i>
                                        @break
                                    @case('buku')
                                        <i class="bi bi-book"></i>
                                        @break
                                    @case('poster')
                                        <i class="bi bi-image"></i>
                                        @break
                                    @case('fotografi')
                                        <i class="bi bi-camera"></i>
                                        @break
                                    @case('seni_gambar')
                                        <i class="bi bi-palette"></i>
                                        @break
                                    @case('karakter_animasi')
                                        <i class="bi bi-person-workspace"></i>
                                        @break
                                    @case('alat_peraga')
                                        <i class="bi bi-tools"></i>
                                        @break
                                    @case('basis_data')
                                        <i class="bi bi-database"></i>
                                        @break
                                    @default
                                        <i class="bi bi-file-earmark"></i>
                                @endswitch
                            </div>
                        </div>
                        
                        {{-- Show search results for titles if searching by title --}}
                        @if(isset($result->search_results) && $result->search_results->count() > 0)
                            <div class="mt-4">
                                <h6 class="text-muted">Hasil pencarian judul:</h6>
                                <div class="row">
                                    @foreach($result->search_results->take(6) as $submission)
                                        <div class="col-md-6 mb-2">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body py-2">
                                                    <h6 class="card-title mb-1">{{ $submission->title }}</h6>
                                                    <small class="text-muted">
                                                        {{ $submission->creator }} • {{ $submission->department }} • {{ $submission->year }}
                                                    </small>
                                                    @if(isset($query) && stripos($submission->title, $query) !== false)
                                                        <br><span class="badge bg-success badge-sm">Sesuai pencarian</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($result->search_results->count() > 6)
                                        <div class="col-12">
                                            <small class="text-muted">
                                                ... dan {{ $result->search_results->count() - 6 }} karya lainnya
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <a href="{{ route('detail_jenis', ['type' => $result->type]) }}" 
                               class="btn btn-outline-primary">
                                Lihat Semua {{ $result->type_name }} >
                            </a>
                        </div>
                    </div>
                @endforeach
                
            @elseif(isset($query) && $query)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Data tidak ditemukan</strong> untuk pencarian "{{ $query }}"
                    @if(isset($searchBy))
                        berdasarkan {{ $searchBy === 'jenis_ciptaan' ? 'Jenis Ciptaan' : 'Judul Ciptaan' }}
                    @endif
                    <br><small>Coba gunakan kata kunci yang berbeda.</small>
                </div>
            @else
                <!-- Default category display -->
                <div class="category-card">
                    <div>
                        <h3 class="category-title">Program Komputer</h3>
                        <p class="category-description">Karya cipta berupa aplikasi, software, atau sistem komputer</p>
                    </div>
                    <div class="category-icon">
                        <i class="bi bi-code-slash"></i>
                    </div>
                </div>

                <div class="category-card">
                    <div>
                        <h3 class="category-title">Sinematografi</h3>
                        <p class="category-description">Karya cipta berupa film, video, atau karya audiovisual</p>
                    </div>
                    <div class="category-icon">
                        <i class="bi bi-camera-video"></i>
                    </div>
                </div>

                <!-- Add more default categories as needed -->
            @endif
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