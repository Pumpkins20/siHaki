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
        /* ✅ FIXED: Define CSS variables */
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
            --success-color: #27ae60;
            --info-color: #3498db;
        }

        /* ✅ ENHANCED: Enhanced styles for pencipta cards */
        .pencipta-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .pencipta-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .pencipta-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 24px;
            margin-right: 20px;
            flex-shrink: 0;
            overflow: hidden;
        }

        .pencipta-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .pencipta-info h5 {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .pencipta-info .institusi {
            color: var(--text-light);
            font-size: 14px;
            margin-bottom: 3px;
        }

        .pencipta-info .jurusan {
            color: var(--info-color);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .total-hki-badge {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: inline-block;
        }

        .search-match-badge {
            background: var(--success-color);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin-left: 10px;
        }

        .recent-works {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
        }

        .recent-works ul li {
            color: var(--text-light);
            font-size: 13px;
            margin-bottom: 3px;
        }

        .btn-detail {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-detail:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .filter-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            padding: 30px 0;
            margin-bottom: 40px;
        }

        .filter-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .filter-title {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }

        .search-form-horizontal {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .form-group-flex {
            flex: 1;
            min-width: 200px;
        }

        .form-group-flex.search-input {
            flex: 2;
            min-width: 250px;
        }

        .btn-search {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            height: fit-content;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-light);
        }

        .no-results i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #bdc3c7;
        }

        /* ✅ FIXED: Pagination Styling with proper variables */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .pagination {
            margin: 0;
        }

        .pagination .page-item {
            margin: 0 4px;
        }

        .pagination .page-link {
            border: 2px solid #e9ecef;
            color: var(--text-dark);
            background-color: white;
            padding: 10px 15px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .pagination .page-link:hover {
            border-color: var(--primary-color);
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .pagination .page-item.active .page-link:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
        }

        .pagination .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #6c757d;
        }

        /* ✅ FIXED: Results info styling */
        .alert-info {
            border-left: 4px solid var(--primary-color);
            background-color: rgba(102, 126, 234, 0.1);
            border-color: rgba(102, 126, 234, 0.2);
        }

        /* ✅ NEW: Show all button styling */
        .btn-show-all {
            background: linear-gradient(135deg, var(--success-color) 0%, #2ecc71 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-show-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
            color: white;
        }

        /* ✅ NEW: Responsive design improvements */
        @media (max-width: 768px) {
            .search-form-horizontal {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-group-flex {
                min-width: 100%;
            }
            
            .pencipta-card {
                padding: 15px;
            }
            
            .pencipta-avatar {
                width: 60px;
                height: 60px;
                font-size: 18px;
                margin-right: 15px;
            }
            
            .pagination-wrapper {
                margin-top: 30px;
            }
            
            .pagination .page-link {
                padding: 8px 12px;
                font-size: 0.9rem;
            }
            
            .pagination .page-item {
                margin: 0 2px;
            }

            .alert-info {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .pencipta-card .d-flex {
                flex-direction: column;
                text-align: center;
            }
            
            .pencipta-avatar {
                margin: 0 auto 15px auto;
            }
            
            .btn-detail {
                margin-top: 15px;
                width: 100%;
            }
            
            .pagination .page-link {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
            
            .pagination .page-item {
                margin: 0 1px;
            }

            .pagination-wrapper {
                margin-top: 20px;
                padding-top: 15px;
            }
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
                    <!-- Dropdown Cari Berdasarkan -->
                    <div class="form-group-flex">
                        <label for="searchBy" class="form-label">Cari Berdasarkan</label>
                        <select class="form-select" id="searchBy" name="search_by">
                            <option value="nama_pencipta" {{ request('search_by') == 'nama_pencipta' ? 'selected' : '' }}>Nama Pencipta</option>
                            <option value="jurusan" {{ request('search_by') == 'jurusan' ? 'selected' : '' }}>Jurusan/Program Studi</option>
                        </select>
                    </div>

                    <!-- Input Pencarian -->
                    <div class="form-group-flex search-input">
                        <label for="searchInput" class="form-label">Kata Kunci</label>
                        <input type="text" class="form-control" id="searchInput" name="q" 
                               value="{{ request('q') }}" placeholder="Masukkan kata kunci pencarian...">
                    </div>

                    <!-- Tombol Cari dan Show All -->
                    <div class="d-flex gap-2">
                        <button class="btn-search" type="submit">
                            <i class="bi bi-search me-2"></i>Cari
                        </button>
                        <!-- ✅ NEW: Show All Button -->
                        @if(request()->has('q') || request()->has('search_by'))
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
                            berdasarkan <strong>{{ $searchBy === 'nama_pencipta' ? 'Nama Pencipta' : 'Jurusan/Program Studi' }}</strong>
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
                                <div class="jurusan">{{ $result->jurusan ?? 'N/A' }}</div>
                                
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="total-hki-badge">
                                        <i class="bi bi-award me-1"></i>{{ $result->total_hki }} Pengajuan HKI
                                    </span>
                                    
                                    @if(isset($query) && $searchBy === 'nama_pencipta' && stripos($result->nama, $query) !== false)
                                        <span class="search-match-badge">
                                            <i class="bi bi-check-circle me-1"></i>Sesuai pencarian nama
                                        </span>
                                    @elseif(isset($query) && $searchBy === 'jurusan' && stripos($result->jurusan, $query) !== false)
                                        <span class="search-match-badge">
                                            <i class="bi bi-check-circle me-1"></i>Sesuai pencarian jurusan
                                        </span>
                                    @endif
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
                                                    <small class="text-primary">({{ $submission->created_at->format('Y') }})</small>
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

                <!-- ✅ FIXED: Pagination with proper check -->
                @if($results->hasPages())
                    <div class="pagination-wrapper">
                        <nav aria-label="Pagination Navigation">
                            {{ $results->links('custom.pagination') }}
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
                        <p class="text-muted">berdasarkan {{ $searchBy === 'nama_pencipta' ? 'Nama Pencipta' : 'Jurusan/Program Studi' }}</p>
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
                
                <!-- ✅ NEW: Auto load all users when no search -->
                @php
                    // Get all users with approved submissions for display
                    $allUsers = \App\Models\User::select([
                        'users.id',
                        'users.nama',
                        'users.email',
                        'users.foto',
                        DB::raw('COUNT(DISTINCT hki_submissions.id) as total_hki')
                    ])
                    ->join('hki_submissions', 'users.id', '=', 'hki_submissions.user_id')
                    ->where('users.role', 'user')
                    ->where('hki_submissions.status', 'approved')
                    ->groupBy(['users.id', 'users.nama', 'users.email', 'users.foto'])
                    ->having('total_hki', '>', 0)
                    ->orderBy('total_hki', 'desc')
                    ->orderBy('users.nama')
                    ->paginate(6);
                    
                    // Load submissions for each user
                    $allUsers->getCollection()->transform(function ($user) {
                        $user->submissions = \App\Models\HkiSubmission::where('user_id', $user->id)
                            ->where('status', 'approved')
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get(['id', 'title', 'created_at']);
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
                                    <div class="jurusan">{{ $result->jurusan ?? 'N/A' }}</div>
                                    
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
                                                        <small class="text-primary">({{ $submission->created_at->format('Y') }})</small>
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
        // Dynamic placeholder based on search type
        document.getElementById('searchBy').addEventListener('change', function() {
            const searchInput = document.getElementById('searchInput');
            const searchType = this.value;
            
            const placeholders = {
                'nama_pencipta': 'Contoh: Ahmad Fauzi, Sari Dewi',
                'jurusan': 'Contoh: S1 Informatika, D3 Manajemen Informatika'
            };
            
            searchInput.placeholder = placeholders[searchType] || 'Masukkan kata kunci pencarian...';
        });

        // Form enhancement
        document.querySelector('form').addEventListener('submit', function(e) {
            const searchInput = document.getElementById('searchInput');
            if (searchInput.value.trim() === '') {
                // Allow empty search to show all results
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