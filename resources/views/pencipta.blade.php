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
    <link href="{{ asset('landing-page/css/pencipta.css') }}" rel="stylesheet">
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
                            <a class="nav-link active" href="#pencipta">Pencipta</a>
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
 <!-- Search Section -->
    <section class="search-section py-4">
        <div class="container">
            <form method="GET" action="{{ route('pencipta') }}">
                <!-- Dropdown Cari Berdasarkan -->
                <div class="form-group">
                    <label for="searchBy">Cari Berdasarkan</label>
                    <select class="form-select" id="searchBy" name="search_by">
                        <option value="nama_pencipta" {{ request('search_by') == 'nama_pencipta' ? 'selected' : '' }}>Nama Pencipta</option>
                        <option value="program_studi" {{ request('search_by') == 'program_studi' ? 'selected' : '' }}>Program Studi/Jurusan</option>
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
        @if(isset($results) && $results->count())
            <div class="mb-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    Menampilkan <strong>{{ $results->count() }}</strong> hasil pencarian
                    @if(isset($query) && $query)
                        untuk "<strong>{{ $query }}</strong>"
                    @endif
                    @if(isset($searchBy))
                        berdasarkan <strong>{{ $searchBy === 'nama_pencipta' ? 'Nama Pencipta' : 'Program Studi' }}</strong>
                    @endif
                </div>
            </div>
            
            @foreach($results as $result)
                <div class="result-card">
                    <div class="result-card-body">
                        <div class="result-header">
                            <div class="result-avatar">
                                {{ substr($result->nama, 0, 2) }}
                            </div>
                            <div class="result-info">
                                <h5>{{ $result->nama }}</h5>
                                <div>{{ $result->institusi }}</div>
                                <div>Jurusan: {{ $result->jurusan }}</div>
                                <div class="mt-2">
                                    <span class="badge bg-primary">{{ $result->total_hki }} Karya HKI</span>
                                    @if(isset($query) && $searchBy === 'nama_pencipta' && stripos($result->nama, $query) !== false)
                                        <span class="badge bg-success">Sesuai pencarian nama</span>
                                    @elseif(isset($query) && $searchBy === 'program_studi' && stripos($result->jurusan, $query) !== false)
                                        <span class="badge bg-success">Sesuai pencarian jurusan</span>
                                    @endif
                                </div>
                                
                                {{-- Show some of the HKI works --}}
                                @if(isset($result->submissions) && $result->submissions->count() > 0)
                                    <div class="mt-3">
                                        <small class="text-muted">Karya HKI terbaru:</small>
                                        <ul class="list-unstyled mt-1">
                                            @foreach($result->submissions->take(3) as $submission)
                                                <li class="small">
                                                    <i class="bi bi-arrow-right me-1"></i>
                                                    {{ $submission->title }} 
                                                    <span class="text-muted">({{ $submission->created_at->format('Y') }})</span>
                                                </li>
                                            @endforeach
                                            @if($result->submissions->count() > 3)
                                                <li class="small text-muted">
                                                    ... dan {{ $result->submissions->count() - 3 }} karya lainnya
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            <div class="result-action ms-auto">
                                <a href="{{ route('detail_pencipta', ['id' => $result->id]) }}" 
                                   class="btn btn-outline-primary">Lihat Daftar HKI ></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
        @elseif(isset($query) && $query)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Data tidak ditemukan</strong> untuk pencarian "{{ $query }}"
                @if(isset($searchBy))
                    berdasarkan {{ $searchBy === 'nama_pencipta' ? 'Nama Pencipta' : 'Program Studi' }}
                @endif
                <br><small>Coba gunakan kata kunci yang berbeda.</small>
            </div>
        @else
            <!-- Default content when no search -->
            <div class="text-center py-5">
                <i class="bi bi-search fs-1 text-muted"></i>
                <h4 class="mt-3">Cari Pencipta HKI</h4>
                <p class="text-muted">Gunakan form pencarian di atas untuk menemukan pencipta berdasarkan nama atau program studi</p>
            </div>
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