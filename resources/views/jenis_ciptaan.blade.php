<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Jenis Ciptaan | SiHaki STMIK AMIKOM Surakarta</title>
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
            {{-- ✅ FIXED: Form action should go to jenis_ciptaan route --}}
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
                        
                        {{-- ✅ ENHANCED: Show search results for titles if searching by title --}}
                        @if(isset($result->search_results) && $result->search_results->count() > 0)
                            <div class="mt-4">
                                <h6 class="text-muted">
                                    <i class="bi bi-list-ul me-1"></i>Hasil pencarian judul dalam kategori {{ $result->type_name }}:
                                </h6>
                                <div class="row">
                                    @foreach($result->search_results->take(6) as $submission)
                                        <div class="col-md-6 mb-2">
                                            <div class="card border-0 shadow-sm submission-card">
                                                <div class="card-body py-2">
                                                    <h6 class="card-title mb-1">
                                                        {{-- ✅ FIXED: Link to detail_ciptaan with proper ID --}}
                                                        <a href="{{ route('detail_ciptaan', ['id' => $submission->id ?? 1]) }}" 
                                                        class="text-decoration-none text-primary">
                                                            {{ $submission->title }}
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>{{ $submission->creator }} • 
                                                        <i class="bi bi-building me-1"></i>{{ $submission->department }} • 
                                                        <i class="bi bi-calendar me-1"></i>{{ $submission->year }}
                                                    </small>
                                                    @if(isset($query) && stripos($submission->title, $query) !== false)
                                                        <br><span class="badge bg-success badge-sm">
                                                            <i class="bi bi-check-circle me-1"></i>Sesuai pencarian
                                                        </span>
                                                    @endif
                                                    
                                                    {{-- ✅ NEW: Action button for detail --}}
                                                    <div class="mt-2">
                                                        <a href="{{ route('detail_ciptaan', ['id' => $submission->id ?? 1]) }}" 
                                                        class="btn btn-outline-primary btn-sm">
                                                            <i class="bi bi-eye me-1"></i>Lihat Detail
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($result->search_results->count() > 6)
                                        <div class="col-12">
                                            <div class="alert alert-light">
                                                <i class="bi bi-info-circle me-1"></i>
                                                <small class="text-muted">
                                                    ... dan <strong>{{ $result->search_results->count() - 6 }}</strong> karya lainnya. 
                                                    <a href="{{ route('detail_jenis', ['type' => $result->type]) }}" class="text-primary">
                                                        Lihat semua
                                                    </a>
                                                </small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        {{-- ✅ ENHANCED: View all button --}}
                        <div class="mt-3">
                            <a href="{{ route('detail_jenis', ['type' => $result->type]) }}" 
                            class="btn btn-outline-primary">
                                <i class="bi bi-arrow-right me-1"></i>Lihat Semua {{ $result->type_name }} ({{ $result->count }} karya)
                            </a>
                        </div>
                    </div>
                @endforeach
                
            @elseif(isset($query) && $query)
                {{-- ✅ ENHANCED: No results found --}}
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Data tidak ditemukan</strong> untuk pencarian "<strong>{{ $query }}</strong>"
                    @if(isset($searchBy))
                        berdasarkan <strong>{{ $searchBy === 'jenis_ciptaan' ? 'Jenis Ciptaan' : 'Judul Ciptaan' }}</strong>
                    @endif
                    <br><small>Coba gunakan kata kunci yang berbeda atau pilih filter pencarian lain.</small>
                    
                    {{-- ✅ NEW: Search suggestions --}}
                    <div class="mt-3">
                        <strong>Saran pencarian:</strong>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <a href="{{ route('jenis_ciptaan', ['search_by' => 'jenis_ciptaan', 'q' => 'program']) }}" 
                            class="btn btn-outline-primary btn-sm">Program Komputer</a>
                            <a href="{{ route('jenis_ciptaan', ['search_by' => 'jenis_ciptaan', 'q' => 'sinematografi']) }}" 
                            class="btn btn-outline-primary btn-sm">Sinematografi</a>
                            <a href="{{ route('jenis_ciptaan', ['search_by' => 'jenis_ciptaan', 'q' => 'buku']) }}" 
                            class="btn btn-outline-primary btn-sm">Buku</a>
                            <a href="{{ route('jenis_ciptaan', ['search_by' => 'jenis_ciptaan', 'q' => 'poster']) }}" 
                            class="btn btn-outline-primary btn-sm">Poster</a>
                            <a href="{{ route('jenis_ciptaan', ['search_by' => 'jenis_ciptaan', 'q' => 'fotografi']) }}" 
                            class="btn btn-outline-primary btn-sm">Fotografi</a>
                            <a href="{{ route('jenis_ciptaan', ['search_by' => 'jenis_ciptaan', 'q' => 'seni']) }}" 
                            class="btn btn-outline-primary btn-sm">Seni Gambar</a>
                            <a href="{{ route('jenis_ciptaan', ['search_by' => 'jenis_ciptaan', 'q' => 'karakter']) }}" 
                            class="btn btn-outline-primary btn-sm">Karakter Animasi</a>
                            <a href="{{ route('jenis_ciptaan', ['search_by' => 'jenis_ciptaan', 'q' => 'alat']) }}" 
                            class="btn btn-outline-primary btn-sm">Alat Peraga</a>
                            <a href="{{ route('jenis_ciptaan', ['search_by' => 'jenis_ciptaan', 'q' => 'basis']) }}" 
                            class="btn btn-outline-primary btn-sm">Basis Data</a>
                        </div>
                    </div>
                </div>
            @else
                {{-- ✅ ENHANCED: Complete category display - All 9 types --}}
                <div class="row">
                    <!-- Program Komputer -->
                    <div class="col-md-6 mb-4">
                        <div class="category-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="category-title">Program Komputer</h3>
                                    <p class="category-description">Karya cipta berupa aplikasi, software, atau sistem komputer</p>
                                    <div class="mt-3">
                                        <a href="{{ route('detail_jenis', ['type' => 'program_komputer']) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="category-icon">
                                    <i class="bi bi-code-slash"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sinematografi -->
                    <div class="col-md-6 mb-4">
                        <div class="category-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="category-title">Sinematografi</h3>
                                    <p class="category-description">Karya cipta berupa film, video, atau karya audiovisual</p>
                                    <div class="mt-3">
                                        <a href="{{ route('detail_jenis', ['type' => 'sinematografi']) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="category-icon">
                                    <i class="bi bi-camera-video"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Buku -->
                    <div class="col-md-6 mb-4">
                        <div class="category-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="category-title">Buku</h3>
                                    <p class="category-description">Karya cipta berupa buku, jurnal, atau publikasi tertulis</p>
                                    <div class="mt-3">
                                        <a href="{{ route('detail_jenis', ['type' => 'buku']) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="category-icon">
                                    <i class="bi bi-book"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Poster -->
                    <div class="col-md-6 mb-4">
                        <div class="category-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="category-title">Poster</h3>
                                    <p class="category-description">Karya cipta berupa desain poster atau media visual promosi</p>
                                    <div class="mt-3">
                                        <a href="{{ route('detail_jenis', ['type' => 'poster']) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="category-icon">
                                    <i class="bi bi-image"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fotografi -->
                    <div class="col-md-6 mb-4">
                        <div class="category-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="category-title">Fotografi</h3>
                                    <p class="category-description">Karya cipta berupa foto atau karya fotografi artistik</p>
                                    <div class="mt-3">
                                        <a href="{{ route('detail_jenis', ['type' => 'fotografi']) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="category-icon">
                                    <i class="bi bi-camera"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seni Gambar -->
                    <div class="col-md-6 mb-4">
                        <div class="category-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="category-title">Seni Gambar</h3>
                                    <p class="category-description">Karya cipta berupa lukisan, ilustrasi, atau seni rupa</p>
                                    <div class="mt-3">
                                        <a href="{{ route('detail_jenis', ['type' => 'seni_gambar']) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="category-icon">
                                    <i class="bi bi-palette"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Karakter Animasi -->
                    <div class="col-md-6 mb-4">
                        <div class="category-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="category-title">Karakter Animasi</h3>
                                    <p class="category-description">Karya cipta berupa desain karakter untuk animasi atau game</p>
                                    <div class="mt-3">
                                        <a href="{{ route('detail_jenis', ['type' => 'karakter_animasi']) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="category-icon">
                                    <i class="bi bi-person-workspace"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Alat Peraga -->
                    <div class="col-md-6 mb-4">
                        <div class="category-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="category-title">Alat Peraga</h3>
                                    <p class="category-description">Karya cipta berupa alat bantu pembelajaran atau demonstrasi</p>
                                    <div class="mt-3">
                                        <a href="{{ route('detail_jenis', ['type' => 'alat_peraga']) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="category-icon">
                                    <i class="bi bi-tools"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Basis Data -->
                    <div class="col-md-6 mb-4">
                        <div class="category-card">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="category-title">Basis Data</h3>
                                    <p class="category-description">Karya cipta berupa database atau sistem basis data</p>
                                    <div class="mt-3">
                                        <a href="{{ route('detail_jenis', ['type' => 'basis_data']) }}" 
                                           class="btn btn-outline-primary">
                                            <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                                        </a>
                                    </div>
                                </div>
                                <div class="category-icon">
                                    <i class="bi bi-database"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ✅ ADD: Summary section --}}
                <div class="mt-5">
                    <div class="alert alert-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="alert-heading mb-2">
                                    <i class="bi bi-info-circle me-2"></i>9 Jenis Ciptaan HKI
                                </h5>
                                <p class="mb-0">
                                    STMIK AMIKOM Surakarta mendukung pengajuan HKI untuk 9 jenis ciptaan yang berbeda. 
                                    Klik pada kategori di atas untuk melihat detail dan contoh karya dalam setiap jenis.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="d-flex justify-content-end gap-3">
                                    <div class="text-center">
                                        <div class="fs-3 fw-bold text-primary">9</div>
                                        <small class="text-muted">Jenis Ciptaan</small>
                                    </div>
                                    <div class="text-center">
                                        <div class="fs-3 fw-bold text-success">
                                            {{ \App\Models\HkiSubmission::where('status', 'approved')->count() }}
                                        </div>
                                        <small class="text-muted">Total Karya</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ✅ FIXED: Search functionality
            const searchForm = document.querySelector('form');
            const searchBtn = document.querySelector('.search-btn');
            const searchInput = document.querySelector('#searchInput');
            
            // Fix search button event
            if (searchBtn) {
                searchBtn.addEventListener('click', function(e) {
                    const searchValue = searchInput.value.trim();
                    console.log('Searching for:', searchValue);
                    // Form akan submit secara normal
                });
            }

            // Enter key search
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchForm.submit();
                    }
                });
            }

            // ✅ ENHANCED: Dynamic placeholder dengan semua 9 jenis
            const searchBy = document.querySelector('#searchBy');
            if (searchBy) {
                searchBy.addEventListener('change', function() {
                    const searchType = this.value;
                    const placeholders = {
                        'jenis_ciptaan': 'Contoh: Program Komputer, Sinematografi, Buku, Poster, Fotografi, Seni Gambar, Karakter Animasi, Alat Peraga, Basis Data',
                        'judul_ciptaan': 'Contoh: Sistem Informasi, Aplikasi Mobile, Film Dokumenter, Poster Kampus'
                    };
                    
                    if (searchInput) {
                        searchInput.placeholder = placeholders[searchType] || 'Masukkan kata kunci pencarian...';
                    }
                });
            }

            // ✅ ENHANCED: Card hover effects
            document.querySelectorAll('.category-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });

            // ✅ NEW: Submission card hover effects
            document.querySelectorAll('.submission-card').forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'all 0.3s ease';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>