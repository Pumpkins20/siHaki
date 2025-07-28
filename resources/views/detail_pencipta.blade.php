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
    <link href="{{ asset('landing-page/css/detail_pencipta.css') }}" rel="stylesheet">
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


    <!-- Profile Section -->
    <section class="profile-section">
        <div class="container">
            <div class="profile-header">
                <div class="profile-avatar">
                    MDS
                </div>
                <div class="profile-info">
                    <h2>
                        Mario Dwi Satria Nugraha
                        <i class="bi bi-patch-check-fill verified-badge"></i>
                    </h2>
                    <div class="institution">
                        <i class="bi bi-geo-alt"></i>
                        STMIK AMIKOM Surakarta
                    </div>
                    <div class="department">
                        <i class="bi bi-mortarboard"></i>
                        S1 Informatika / D3 Manajemen Informatika
                    </div>
                    <div class="expertise-tags">
                        <span class="tag">Program Komputer</span>
                        <span class="tag">Sinematografi</span>
                        <span class="tag">Alat Peraga</span>
                    </div>
                </div>
                <div class="profile-stats">
                    <div class="stat-card-compact">
                        <div class="stat-icon articles">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <div class="stat-number">11</div>
                        <div class="stat-label">Pengajuan HKI</div>
                        <div class="stat-sublabel">Total Submitted</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Chart Section -->
    <section class="chart-section">
        <div class="container">
            <div class="chart-header">
                <h4>Statistik Pengajuan</h4>
            </div>
            <div class="chart-container">
                <div class="chart-placeholder">
                    <i class="bi bi-bar-chart-line fs-1 text-muted"></i>
                    <div>Publication Chart</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Publications Section -->
    <section class="publications-section">
        <div class="container">
            <div class="section-tabs">
                <a href="#" class="tab-item active">Daftar Pengajuan</a>
            </div>

            <div class="publication-item">
                <div class="publication-title">
                    <a href="{{ route('detail_ciptaan', ['id' => 1]) }}">Judul Ciptaan</a>
                </div>
                <div class="publication-meta">
                     <div class="meta-badge">Tipe Ciptaan</div>
                    <div class="meta-badge">Jenis Ciptaan</div>
                </div>
                <div class="publication-details">
                    <span>Anggota Pengajuan : jumlah </span>
                    <span><i class="bi bi-calendar3"></i> Tanggal Publikasi</span>
                </div>
            </div>

            
            <div class="publication-item">
                <div class="publication-title">
                    <a href="#">Judul Ciptaan</a>
                </div>
                <div class="publication-meta">
                     <div class="meta-badge">Tipe Ciptaan</div>
                    <div class="meta-badge">Jenis Ciptaan</div>
                </div>
                <div class="publication-details">
                    <span>Anggota Pengajuan : X</span>
                    <span>Creator : Zhang D.</span>
                    <span><i class="bi bi-calendar3"></i> Tanggal Publikasi</span>
                </div>
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
    </section>

    
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Tab functionality
        document.querySelectorAll('.tab-item').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
            });
        });

        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });

        // Animate numbers on scroll
        function animateNumbers() {
            const statNumbers = document.querySelectorAll('.stat-number');
            
            statNumbers.forEach(number => {
                const target = parseInt(number.textContent.replace(/,/g, ''));
                const increment = target / 50;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    number.textContent = Math.floor(current).toLocaleString();
                }, 30);
            });
        }

        // Trigger animation when page loads
        window.addEventListener('load', function() {
            setTimeout(animateNumbers, 500);
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href.startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>