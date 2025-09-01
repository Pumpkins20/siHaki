<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Pencipta | SiHaki STMIK AMIKOM S</title>
    <meta name="description" content="Sistem Informasi Hak Kekayaan Intelektual STMIK AMIKOM Surakarta">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('landing-page/css/pencipta.css') }}" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        .search-form-horizontal {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: end;
        }

        .form-group-flex {
            display: flex;
            flex-direction: column;
            min-width: 200px;
            flex: 1;
        }

        .form-group-flex.search-input {
            min-width: 300px;
            flex: 2;
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
                            <a class="nav-link active" href="#pencipta">Pencipta</a>
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

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <div class="filter-card">
                <h4 class="filter-title">
                    <i class="bi bi-search me-2"></i>Cari Pencipta HKI
                </h4>
                <form method="GET" action="{{ route('pencipta') }}" class="search-form-horizontal">
                    <!-- Row 1: Search criteria -->
                    <div class="form-group-flex">
                        <label for="searchBy" class="form-label">Cari Berdasarkan</label>
                        <select class="form-select" id="searchBy" name="search_by">
                            <option value="nama_pencipta" {{ request('search_by') == 'nama_pencipta' ? 'selected' : '' }}>Nama Pengusul</option>
                            <option value="program_studi" {{ request('search_by') == 'program_studi' ? 'selected' : '' }}>Program Studi</option>
                        </select>
                    </div>

                    <!-- Input Pencarian -->
                    <div class="form-group-flex search-input">
                        <label for="searchInput" class="form-label">Kata Kunci</label>
                        <input type="text" class="form-control" id="searchInput" name="q" 
                            value="{{ request('q') }}" placeholder="Masukkan kata kunci pencarian...">
                    </div>

                    <!-- 
                    <div class="form-group-flex">
                        <label for="programStudi" class="form-label">Program Studi</label>
                        <select class="form-select" id="programStudi" name="program_studi">
                            <option value="">Semua Program Studi</option>
                            {{-- ✅ FIXED: Menggunakan data dari database --}}
                            @if(isset($availableProdi) && $availableProdi->count() > 0)
                                @foreach($availableProdi as $prodi)
                                    <option value="{{ $prodi }}" {{ request('program_studi') == $prodi ? 'selected' : '' }}>
                                        {{ $prodi }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Tidak ada data program studi</option>
                            @endif
                        </select>
                    </div>Row 2: Additional filters -->

                    <div class="form-group-flex">
                        <label for="tahunPengajuan" class="form-label">Tahun Pengajuan</label>
                        <select class="form-select" id="tahunPengajuan" name="tahun_pengajuan">
                            <option value="">Semua Tahun</option>
                            {{-- ✅ FIXED: Menggunakan data dari database berdasarkan submission_date --}}
                            @if(isset($availableYears) && $availableYears->count() > 0)
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ request('tahun_pengajuan') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            @else
                                <option value="" disabled>Tidak ada data tahun pengajuan</option>
                            @endif
                        </select>
                    </div>

                    <!-- Tombol Cari dan Show All -->
                    <div class="d-flex gap-2">
                        <button class="btn-search" type="submit">
                            <i class="bi bi-search me-2"></i>Cari
                        </button>
                        @if(request()->has('q') || request()->has('search_by') || request()->has('program_studi') || request()->has('tahun_pengajuan'))
                            <a href="{{ route('pencipta') }}" class="btn-show-all">
                                <i class="bi bi-list-ul me-2"></i>Lihat Semua
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section class="results-section">
        <div class="container">
            @if(isset($results) && $results->count())
                <!-- Results Info -->
                <div class="mb-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Menampilkan <strong>{{ $results->firstItem() }}-{{ $results->lastItem() }}</strong> 
                        dari <strong>{{ $results->total() }}</strong> pencipta
                        @if(isset($query) && $query)
                            untuk pencarian "<strong>{{ $query }}</strong>"
                        @endif
                        @if(isset($searchBy))
                            berdasarkan <strong>{{ $searchBy === 'nama_pencipta' ? 'Nama Pencipta' : 'Program Studi' }}</strong>
                        @endif
                        @if(request('program_studi'))
                            dalam program studi <strong>{{ request('program_studi') }}</strong>
                        @endif
                        {{-- ✅ FIXED: Update info tahun pengajuan --}}
                        @if(request('tahun_pengajuan'))
                            untuk tahun pengajuan <strong>{{ request('tahun_pengajuan') }}</strong>
                        @endif
                    </div>
                </div>
                
                <!-- Pencipta Cards -->
                @foreach($results as $result)
                    <div class="pencipta-card">
                        <div class="d-flex align-items-start">
                            <!-- Avatar -->
                            <div class="pencipta-avatar">
                                @if($result->foto && $result->foto !== 'default.png')
                                    <img src="{{ asset('storage/profile_photos/' . $result->foto) }}" 
                                        alt="{{ $result->nama }}"
                                        onerror="this.style.display='none'; this.parentElement.innerHTML='{{ substr($result->nama, 0, 2) }}';">
                                @else
                                    {{ substr($result->nama, 0, 2) }}
                                @endif
                            </div>
                            
                            <!-- Info -->
                            <div class="pencipta-info flex-grow-1">
                                <h5>{{ $result->nama }}</h5>
                                <div class="institusi">{{ $result->institusi ?? 'STMIK AMIKOM Surakarta' }}</div>
                                <div class="jurusan">{{ $result->program_studi ?? $result->jurusan ?? 'N/A' }}</div>
                                
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="total-hki-badge">
                                        <i class="bi bi-award me-1"></i>{{ $result->total_hki }} Pengajuan HKI
                                        {{-- ✅ NEW: Show year filter info --}}
                                        @if(request('tahun_pengajuan'))
                                            <small class="text-muted">(Tahun {{ request('tahun_pengajuan') }})</small>
                                        @endif
                                    </span>
                                    
                                    @if(isset($query) && $searchBy === 'nama_pencipta' && stripos($result->nama, $query) !== false)
                                        <span class="search-match-badge">
                                            <i class="bi bi-check-circle me-1"></i>Sesuai pencarian nama
                                        </span>
                                    @elseif(isset($query) && $searchBy === 'program_studi' && (stripos($result->program_studi, $query) !== false || stripos($result->jurusan, $query) !== false))
                                        <span class="search-match-badge">
                                            <i class="bi bi-check-circle me-1"></i>Sesuai pencarian program studi
                                        </span>
                                    @endif
                                </div>
                                
                                {{-- Show recent HKI works --}}
                                @if(isset($result->submissions) && $result->submissions->count() > 0)
                                    <div class="recent-works">
                                        <small class="text-muted fw-bold">
                                            Pengajuan HKI 
                                            @if(request('tahun_pengajuan'))
                                                Tahun {{ request('tahun_pengajuan') }}:
                                            @else
                                                Terbaru:
                                            @endif
                                        </small>
                                        <ul class="list-unstyled mt-2 mb-0">
                                            @foreach($result->submissions->take(3) as $submission)
                                                <li>
                                                    <i class="bi bi-file-earmark-text me-1"></i>
                                                    {{ Str::limit($submission->title, 60) }}
                                                    {{-- ✅ FIXED: Tampilkan submission_date jika ada --}}
                                                    <small class="text-primary">
                                                        ({{ $submission->submission_date ? $submission->submission_date->format('Y') : $submission->created_at->format('Y') }})
                                                    </small>
                                                </li>
                                            @endforeach
                                            @if($result->submissions->count() > 3)
                                                <li class="text-muted">
                                                    <i class="bi bi-three-dots me-1"></i>
                                                    dan {{ $result->submissions->count() - 3 }} pengajuan lainnya
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Action Button -->
                            <div class="ms-3">
                                <a href="{{ route('detail_pencipta', $result->id) }}" class="btn btn-detail">
                                    <i class="bi bi-eye me-1"></i>Lihat Detail HKI
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                @if($results->hasPages())
                    <div class="pagination-wrapper">
                        <nav aria-label="Pagination Navigation">
                            {{ $results->appends(request()->query())->links('custom.pagination') }}
                        </nav>
                    </div>
                @endif
                
            @elseif(isset($query) && $query)
                <!-- No Results -->
                <div class="no-results">
                    <i class="bi bi-search"></i>
                    <h4>Data Tidak Ditemukan</h4>
                    <p>Tidak ada pencipta yang ditemukan untuk pencarian "<strong>{{ $query }}</strong>"</p>
                    @if(isset($searchBy))
                        <p class="text-muted">berdasarkan {{ $searchBy === 'nama_pencipta' ? 'Nama Pencipta' : 'Program Studi' }}</p>
                    @endif
                    @if(request('program_studi'))
                        <p class="text-muted">dalam program studi {{ request('program_studi') }}</p>
                    @endif
                    {{-- ✅ FIXED: Update pesan tahun pengajuan --}}
                    @if(request('tahun_pengajuan'))
                        <p class="text-muted">untuk tahun pengajuan {{ request('tahun_pengajuan') }}</p>
                    @endif
                    <a href="{{ route('pencipta') }}" class="btn btn-outline-primary mt-3">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Semua Pencipta
                    </a>
                </div>
            @else
                <!-- Default content when no search - Show all pencipta -->
                <div class="text-center mb-4">
                    <h4><i class="bi bi-people me-2"></i>Semua Pencipta HKI AMIKOM</h4>
                    <p class="text-muted">Daftar lengkap pencipta yang memiliki pengajuan HKI yang telah disetujui</p>
                </div>
                
                {{-- ✅ FIXED: Auto load all users berdasarkan submission_date --}}
                @php
                    // Get all users with approved submissions for display
                    $allUsers = \App\Models\User::select([
                        'users.id',
                        'users.nama',
                        'users.email',
                        'users.foto',
                        'users.program_studi',
                        DB::raw('COUNT(DISTINCT hki_submissions.id) as total_hki')
                    ])
                    ->join('hki_submissions', 'users.id', '=', 'hki_submissions.user_id')
                    ->where('users.role', 'user')
                    ->where('hki_submissions.status', 'approved')
                    ->groupBy(['users.id', 'users.nama', 'users.email', 'users.foto', 'users.program_studi'])
                    ->having('total_hki', '>', 0)
                    ->orderBy('total_hki', 'desc')
                    ->orderBy('users.nama')
                    ->paginate(6);
                    
                    // Load submissions for each user
                    $allUsers->getCollection()->transform(function ($user) {
                        $user->submissions = \App\Models\HkiSubmission::where('user_id', $user->id)
                            ->where('status', 'approved')
                            ->orderBy('submission_date', 'desc')
                            ->limit(5)
                            ->get(['id', 'title', 'submission_date', 'created_at']);
                        return $user;
                    });
                @endphp
                
                @if($allUsers->count() > 0)
                    <!-- Results Info -->
                    <div class="mb-4">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Menampilkan <strong>{{ $allUsers->firstItem() }}-{{ $allUsers->lastItem() }}</strong> 
                            dari <strong>{{ $allUsers->total() }}</strong> pencipta
                        </div>
                    </div>
                    
                    <!-- Pencipta Cards -->
                    @foreach($allUsers as $result)
                        <div class="pencipta-card">
                            <div class="d-flex align-items-start">
                                <!-- Avatar -->
                                <div class="pencipta-avatar">
                                    @if($result->foto && $result->foto !== 'default.png')
                                        <img src="{{ asset('storage/profile_photos/' . $result->foto) }}" 
                                            alt="{{ $result->nama }}"
                                            onerror="this.style.display='none'; this.parentElement.innerHTML='{{ substr($result->nama, 0, 2) }}';">
                                    @else
                                        {{ substr($result->nama, 0, 2) }}
                                    @endif
                                </div>
                                
                                <!-- Info -->
                                <div class="pencipta-info flex-grow-1">
                                    <h5>{{ $result->nama }}</h5>
                                    <div class="institusi">{{ $result->institusi ?? 'STMIK AMIKOM Surakarta' }}</div>
                                    {{-- ✅ FIXED: Prioritas program_studi daripada jurusan --}}
                                    <div class="jurusan">{{ $result->program_studi ?? $result->jurusan ?? 'N/A' }}</div>
                                    
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="total-hki-badge">
                                            <i class="bi bi-award me-1"></i>{{ $result->total_hki }} Pengajuan HKI
                                        </span>
                                    </div>
                                    
                                    {{-- Show recent HKI works --}}
                                    @if(isset($result->submissions) && $result->submissions->count() > 0)
                                        <div class="recent-works">
                                            <small class="text-muted fw-bold">Pengajuan HKI Terbaru:</small>
                                            <ul class="list-unstyled mt-2 mb-0">
                                                @foreach($result->submissions->take(3) as $submission)
                                                    <li>
                                                        <i class="bi bi-file-earmark-text me-1"></i>
                                                        {{ Str::limit($submission->title, 60) }}
                                                        {{-- ✅ FIXED: Prioritas submission_date --}}
                                                        <small class="text-primary">
                                                            ({{ $submission->submission_date ? $submission->submission_date->format('Y') : $submission->created_at->format('Y') }})
                                                        </small>
                                                    </li>
                                                @endforeach
                                                @if($result->submissions->count() > 3)
                                                    <li class="text-muted">
                                                        <i class="bi bi-three-dots me-1"></i>
                                                        dan {{ $result->submissions->count() - 3 }} pengajuan lainnya
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Action Button -->
                                <div class="ms-3">
                                    <a href="{{ route('detail_pencipta', $result->id) }}" class="btn btn-detail">
                                        <i class="bi bi-eye me-1"></i>Lihat Detail HKI
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination for all users -->
                    @if($allUsers->hasPages())
                        <div class="pagination-wrapper">
                            <nav aria-label="Pagination Navigation">
                                {{ $allUsers->links('custom.pagination') }}
                            </nav>
                        </div>
                    @endif
                @else
                    <div class="no-results">
                        <i class="bi bi-people"></i>
                        <h4>Belum Ada Data Pencipta</h4>
                        <p class="text-muted">Belum ada pencipta dengan pengajuan HKI yang disetujui</p>
                    </div>
                @endif
            @endif
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Available years from database:', @json($availableYears ?? []));
            console.log('Available prodi from database:', @json($availableProdi ?? []));
            console.log('Current tahun_pengajuan filter:', '{{ request("tahun_pengajuan") }}');
        });
        // Dynamic placeholder based on search type
        document.getElementById('searchBy').addEventListener('change', function() {
            const searchInput = document.getElementById('searchInput');
            const searchType = this.value;
            
            const placeholders = {
                'nama_pencipta': 'Contoh: Ahmad Fauzi, Sari Dewi',
                'program_studi': 'Contoh: S1 Informatika, D3 Manajemen Informatika'
            };
            
            searchInput.placeholder = placeholders[searchType] || 'Masukkan kata kunci pencarian...';
        });

        // Form enhancement
        document.querySelector('form').addEventListener('submit', function(e) {
            const searchInput = document.getElementById('searchInput');
            const programStudi = document.getElementById('programStudi');
            const tahunPengajuan = document.getElementById('tahunPengajuan');
            
            // ✅ NEW: Log form data sebelum submit untuk debugging
            console.log('Form submission data:', {
                q: searchInput.value,
                search_by: document.getElementById('searchBy').value,
                program_studi: programStudi.value,
                tahun_pengajuan: tahunPengajuan.value
            });
            
            // Allow search if at least one field has value
            if (searchInput.value.trim() === '' && 
                programStudi.value === '' && 
                tahunPengajuan.value === '') {
                // Allow to show all results
                return true;
            }
        });


        // Card hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.pencipta-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>